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

    public function __construct()
    {
        $this->redirectUri = route('handleCallback'); // Укажите ваш redirect_uri
        $this->certPath = public_path('TestCert/certificate.pem'); // Укажите путь к вашему сертификату
        $this->sslKeyPath = public_path('TestCert/certificate.pem'); // Укажите путь к вашему ключу
        $this->truststorePath = public_path('TestCert/truststore.pem'); // Укажите путь к вашему truststore


        // Получаем данные из базы данных
        $this->paymentData = PaymentsData::where('bank_name', 'sberbank')->first();
        $this->clientId =  $this->paymentData->client_id;
        $this->clientSecret =  $this->paymentData->client_secret;
        $this->accessToken = $this->paymentData->access_token;

    }

    public function get_swagger(){
        try {
            $client = new Client();


            $response = $client->request('GET', 'https://sbi.sberbank.ru:9443/fintech/api/v1/client-info', [
                'cert' => [public_path('Certs/sbbapi_cert.pem'), 'Ваш_Пароль'], // Укажите пароль, если он есть
                'ssl_key' => [public_path('Certs/sbbapi_key.pem'), 'Ваш_Пароль'], // Укажите пароль, если он есть
                'verify' => public_path('Certs/combined.pem'), // Используйте обновленный файл
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_VERBOSE => true,
                    CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_SSL_VERIFYHOST => 2,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                ],
            ]);


            $res = $response->getBody()->getContents();

            return view('swagger',  compact('res')) ;
        } catch (RequestException $e) {
            return response()->json(['error' => 'Failed to connect to Sberbank API: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {

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
            'scope' => 'openid GET_CLIENT_ACCOUNTS GET_STATEMENT_ACCOUNT PAY_DOC_RU PAY_DOC_CUR',
            'state' => '296014df-dbc8-4559-ab32-041bf5064a40',
            'nonce' => '80012c9c-1b9a-449e-a8d5-75100ea698ac'
        ];

        $query = http_build_query($params);
        return redirect("https://efs-sbbol-ift-web.testsbi.sberbank.ru:9443/ic/sso/api/v2/oauth/authorize?$query");

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
        return response()->json([
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token']
        ]);
    }

    private function getAccessTokenWithAuthCode($code)
    {
//        dd($code);
        try {
            $client = new Client();
            $params = [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ];

            $response = $client->request('POST', 'https://iftfintech.testsbi.sberbank.ru:9443/ic/sso/api/v2/oauth/token', [
                'form_params' => $params,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                ],
                'cert' => public_path('TestCert/certificate.pem'),
                'ssl_key' => public_path('TestCert/certificate.pem'),
                'verify' => public_path('TestCert/truststore.pem'),
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_VERBOSE => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['access_token']) && isset($result['refresh_token'])) {
                $this->paymentData->access_token = $result['access_token'];
                $this->paymentData->refresh_token = $result['refresh_token'];
                $this->paymentData->code = $code;
                $this->paymentData->save();
                return $result;
            } else {
                return ['error' => 'Failed to get access token'];
            }
        } catch (ClientException $e) {
            return ['error' => 'Failed to get access token: ' . $e->getResponse()->getBody()->getContents()];
        } catch (\Exception $e) {
            return ['error' => 'Failed to get access token: ' . $e->getMessage()];
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

            $response = $client->request('POST', 'https://iftfintech.testsbi.sberbank.ru:9443/ic/sso/api/v2/oauth/token', [
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

            if (isset($result['access_token']) && isset($result['refresh_token'])) {
                // Обновляем токены в базе данных

                $this->paymentData->access_token = $result['access_token'];
                $this->paymentData->refresh_token = $result['refresh_token'];
                $this->paymentData->save();
                return ['access_token' => $result['access_token']];
            } else {
                return ['error' => 'Failed to refresh access token'];
            }
        } catch (ClientException $e) {
            return ['error' => 'Failed to refresh access token: ' . $e->getResponse()->getBody()->getContents()];
        } catch (\Exception $e) {
            return ['error' => 'Failed to refresh access token: ' . $e->getMessage()];
        }
    }

    public function orderBusinessCard(Request $request)
    {
        try {
            $client = new Client();
            $params = [
                'typeName' => 'Business card',
                'paymentSystemName' => 'Visa',
                'servicePeriod' => 'MONTH',
                'accountNumber' => '40802810600000200000',
                'embossedText' => 'SBERBANK',
                'externalId' => (string) Str::uuid(),
                'plastic' => true,
                'cardLimits' => [
                    'dayCashLimit' => 100000,
                    'dayNonCashLimit' => 500000,
                    'dayTransactionsLimit' => 1000000,
                    'monthCashLimit' => 2000000,
                    'monthLimitAllOperations' => 5000000,
                    'monthTransactionsLimit' => 3000000,
                ],
                'branchInfo' => [
                    'address' => 'Москва, ул. Ленина, д. 10',
                    'agencyCode' => '5221',
                    'branchCode' => '0480',
                    'name' => 'Доп. офис №5221/0480',
                    'regionCode' => '52'
                ],
                'cardholder' => [
                    'birthDate' => '1980-01-01',
                    'birthPlace' => 'Москва',
                    'cellphone' => '79991234567',
                    'citizenship' => 'Россия',
                    'email' => 'example@example.com',
                    'firstName' => 'Иван',
                    'middleName' => 'Иванович',
                    'lastName' => 'Иванов',
                    'embossedLastName' => 'IVANOV',
                    'embossedName' => 'IVAN',
                    'sex' => '1',
                    'address' => [
                        'postalCode' => '123456',
                        'country' => 'Россия',
                        'countryCode' => 'RU',
                        'state' => 'Московская обл.',
                        'settlementName' => 'Москва',
                        'city' => 'Москва',
                        'district' => 'Центральный',
                        'street' => 'Ленина',
                        'house' => '10',
                        'building' => '1',
                        'flat' => '101'
                    ],
                    'identityDoc' => [
                        'issueDate' => '2000-01-01',
                        'issuer' => 'ОВД по г. Москва',
                        'issuerCode' => '770-001',
                        'number' => '123456',
                        'serial' => '654321',
                        'type' => 'Паспорт гражданина Российской Федерации',
                        'typeCode' => '21'
                    ]
                ]
            ];

            $response = $client->request('POST', 'https://iftfintech.testsbi.sberbank.ru:9443/fintech/api/v1/corporate-card-request', [
                'json' => $params,
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
                    CURLOPT_SSL_VERIFYPEER => false,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['error'])) {
                return response()->json(['error' => $result['error']], 400);
            }

            return response()->json($result);
        } catch (ClientException $e) {
            return response()->json(['error' => 'Failed to order business card: ' . $e->getResponse()->getBody()->getContents()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to order business card: ' . $e->getMessage()], 500);
        }
    }
    // Метод для выполнения выплаты
    public function initiatePayment(Request $request)
    {
        $amount = 1000; // Сумма в копейках для перевода 10 рублей
        $receiverCardHash = '4083060013614652'; // Хэшированный номер карты получателя, изменить на нужный

        // Получение токена доступа
        $accessToken = $this->getAccessTokenWithAuthCode($request->input('authorization_code'));
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
