<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use App\Models\UserCard;
use App\Models\PaymentsData;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CardController extends Controller
{

    private $clientId;
    private $clientSecret;
    private $redirectUri ; // Укажите ваш redirect_uri
    private $certPath ; // Укажите путь к вашему сертификату
    private $sslKeyPath ; // Укажите путь к вашему ключу
    private $truststorePath ;
    private $paymentData;
    private $accessToken;
    private $scope;

    public function __construct()
    {
        $this->redirectUri = route('handleCallback'); // Укажите ваш redirect_uri
//      TEST  $this->certPath = public_path('TestCert/certificate.pem'); // Укажите путь к вашему сертификату
//    TEST    $this->sslKeyPath = public_path('TestCert/certificate.pem'); // Укажите путь к вашему ключу
   // TEST     $this->truststorePath = public_path('TestCert/truststore.pem'); // Укажите путь к вашему truststore


        $this->certPath = public_path('Certs/client_cert.pem'); // Укажите путь к вашему сертификату
        $this->sslKeyPath = public_path('Certs/client_key.pem'); // Укажите путь к вашему ключу
        $this->truststorePath = public_path('Certs/combined_real_ca.pem'); // Укажите путь к вашему truststore


        // Получаем данные из базы данных
        $this->paymentData = PaymentsData::where('bank_name', 'sberbank')->first();
        $this->clientId =  $this->paymentData->client_id;
        $this->clientSecret =  $this->paymentData->client_secret;
        $this->accessToken = $this->paymentData->access_token;
        $this->scope = $this->paymentData->scope;

    }

    public function get_swagger(){
        try {
            $client = new Client();

            $response = $client->request('GET', 'https://fintech.sberbank.ru:9443/fintech/api/swagger-ui.html', [
                'cert' =>   $this->certPath,
                'ssl_key' => $this->sslKeyPath, // Путь к ключевому файлу
                'verify' =>  $this->truststorePath,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_VERBOSE => true,
                    CURLOPT_SSL_VERIFYPEER => true, // Или false, в зависимости от требований безопасности
                ],
            ]);
            $res = $response->getBody()->getContents();
            return view('swagger',  compact('res')) ;
        } catch (RequestException $e) {
            // Вернуть ошибку с кодом состояния 500
            return response()->json(['error' => 'Failed to connect to Sberbank API: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Вернуть ошибку с кодом состояния 500
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function add_card(Request $request){
             $rules=array(
                 'card_number' => 'required|digits_between:16,19|regex:/^\d+$/',
             );
             $message = [
                 'card_number.required' => 'Поле номер карты обязательно для заполнения.',
                 'card_number.digits_between' => 'Номер карты должен содержать от 16 до 19 цифр.',
                 'card_number.regex' => 'Номер карты должен содержать только цифры.',
             ];
             $validator=Validator::make($request->all(),$rules,$message);
             if($validator->fails())
             {
                 return response()->json([
                     'status' => false,
                     'validation_error' => true,
                     'message' =>$validator->errors()
                 ],400);
             }
             $cardNumber = $request->card_number;

             $firstSixDigits = substr($cardNumber, 0, 10);
             $apiUrl = "https://bin-ip-checker.p.rapidapi.com/?bin=$cardNumber&ip=8.8.8.8";
             $apiKey = '2d0241e137msh52edab1893c85cap1ae3dbjsn8b23cb3abf71'; // Замените 'your_rapidapi_key' на ваш реальный API ключ

             // Отправка запроса к API
             $response = Http::withHeaders([
                 'Content-Type' => 'application/json',
                 'x-rapidapi-host' => 'bin-ip-checker.p.rapidapi.com',
                 'x-rapidapi-key' => $apiKey
             ])->post($apiUrl, [
                 'bin' => $cardNumber,
                 'ip' => '8.8.8.8'
             ])->json();

             if (isset($response['BIN'])){
                 UserCard::UpdateOrcreate(['user_id'=> auth()->user()->id, 'pan'=> $cardNumber],[
                    'pan' =>  $cardNumber,
                     'user_id' => auth()->user()->id,
                     'brand' =>$response['BIN']['brand']??null,
                     'level' =>$response['BIN']['level']??null,
                     'bank_name' =>$response['BIN']['issuer']['name']??null,
                 ]);

                 return response()->json([
                    'status' => true,
                    'message' => 'Карта успешно добавлена'
                 ],200);
             }else{
                 return response()->json([
                    'status' => false,
                    'message' => 'Что то погло не так попробуйте немного позже'
                 ],422);
             }
         }

    public function getAuthorizationCode()
    {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => $this->scope,
            'state' => '296014df-dbc8-4559-ab32-041bf5064a40',
            'nonce' => '80012c9c-1b9a-449e-a8d5-75100ea698ac'
        ];

        $query = http_build_query($params);
        return redirect("https://sbi.sberbank.ru:9443/ic/sso/api/v2/oauth/authorize?$query");

    }


    public function handleCallback(Request $request)
    {



        $code = $request->query('code');

        if (!$code) {
            return response()->json(['error' => 'Authorization code not found'], 400);
        }


        $tokenData = $this->getAccessTokenWithAuthCode($code);

        if (isset($tokenData['error'])) {
            return response()->json(['error' => $tokenData['error']], 400);
        }


        $this->paymentData->code = $code;
        $this->paymentData->access_token = $tokenData['access_token'];
        $this->paymentData->refresh_token = $tokenData['refresh_token'];
        $this->paymentData->save();
        return response()->json([
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token']
        ]);
    }

    private function getAccessTokenWithAuthCode($code)
    {
        try {
            $client = new Client();

            $response = $client->request('POST', 'https://fintech.sberbank.ru:9443/ic/sso/api/v2/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $code,
                    'redirect_uri' => route('handleCallback'),
                ],
                'cert' => $this->certPath,
                'ssl_key' => $this->sslKeyPath,
                'verify' => $this->truststorePath,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_VERBOSE => true,
                    CURLOPT_SSL_VERIFYPEER => true,
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                ],
            ]);

            $body = $response->getBody()->getContents();
            $tokens = json_decode($body, true);

            // Обработка токенов (например, сохранение в БД или сессии)
            return $tokens;

        } catch (RequestException $e) {
            // Логирование ошибки и возврат сообщения об ошибке
            Log::error('Failed to exchange authorization code: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to exchange authorization code. Please try again later.'], 500);
        } catch (\Exception $e) {
            // Логирование ошибки и возврат сообщения об ошибке
            Log::error('Failed to get access token: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get access token. Please try again later.'], 500);
        }
    }

    public function refreshAccessToken()
    {
        try {
            $refreshToken = $this->paymentData->refresh_token;

            $client = new Client();
            $params = [
                'grant_type' => 'refresh_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $refreshToken,
            ];

            $response = $client->request('POST', 'https://fintech.sberbank.ru:9443/ic/sso/api/v2/oauth/token', [
                'form_params' => $params,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                ],
                'cert' => $this->certPath,
                'ssl_key' => $this->sslKeyPath,
                'verify' => $this->truststorePath,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_VERBOSE => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);


            if (isset($result['access_token'])) {
                // Обновляем токены в базе данных

                $this->paymentData->access_token = $result['access_token'];
                $this->paymentData->refresh_token = $result['refresh_token'];
                $this->paymentData->save();
//                return ['access_token' => $result['access_token']];
            } else {
//                return ['error' => 'Failed to refresh access token'];
            }
        } catch (ClientException $e) {
//            return ['error' => 'Failed to refresh access token: ' . $e->getResponse()->getBody()->getContents()];
        } catch (\Exception $e) {
//            return ['error' => 'Failed to refresh access token: ' . $e->getMessage()];
        }
    }



    public function get_corporate_card(){

        $client = new Client();
        $response = $client->request('GET', 'https://fintech.sberbank.ru:9443/fintech/api/v1/corporate-cards', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            'cert' => $this->certPath,
            'ssl_key' => $this->sslKeyPath,
            'verify' => $this->truststorePath,
            'curl' => [
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_VERBOSE => true,
                CURLOPT_SSL_VERIFYPEER => true,
            ],
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        dd($result );
    }
    // Метод для выполнения выплаты
    public function initiatePayment(Request $request)
    {
        $amount = 1000; // Сумма в копейках для перевода 10 рублей
        $receiverCardHash = '4083060013614652'; // Хэшированный номер карты получателя, изменить на нужный

        // Получение токена доступа
        $accessToken = $this->accessToken;
        if (isset($accessToken['error'])) {
            return response()->json(['error' => $accessToken['error']], 400);
        }

        // Формирование данных для запроса выплаты
        $paymentData = [
            'amount' => $amount,
            'receiverCardHash' => $receiverCardHash,
            // Добавьте другие необходимые поля
        ];

        try {
            $client = new Client();
            $response = $client->request('POST', 'https://iftfintech.testsbi.sberbank.ru:9443/v1/payments', [
                'json' => $paymentData,
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'cert' => $this->certPath,
                'ssl_key' => $this->sslKeyPath,
                'verify' => $this->truststorePath,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_VERBOSE => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return response()->json($result);
        } catch (ClientException $e) {
            Log::error('ClientException: ' . $e->getResponse()->getBody()->getContents());
            return response()->json(['error' => 'Failed to initiate payment'], 400);
        } catch (\Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to initiate payment'], 400);
        }
    }
    public function success_add_sum_in_balance(Request $request){

        dd($request);

    }

            public function fail_add_sum_in_balance(Request $request){
                dd($request);
            }
}
