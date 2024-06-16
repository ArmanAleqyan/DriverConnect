<?php

namespace App\Http\Controllers\Admin\Payments;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\PaymentsData;
use Illuminate\Support\Facades\Log;

class SberBankIntegrationController extends Controller
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
    private $request_url;

    public function __construct()
    {
        $this->redirectUri = route('handleCallback'); // Укажите ваш redirect_uri
        $this->certPath = public_path('Certs/client_cert.pem'); // Укажите путь к вашему сертификату
        $this->sslKeyPath = public_path('Certs/client_key.pem'); // Укажите путь к вашему ключу
        $this->truststorePath = public_path('Certs/combined_real_ca.pem'); // Укажите путь к вашему truststore
        $this->paymentData = PaymentsData::where('bank_name', 'sberbank')->first();
        $this->clientId =  $this->paymentData->client_id;
        $this->clientSecret =  $this->paymentData->client_secret;
        $this->accessToken = $this->paymentData->access_token;
        $this->scope = $this->paymentData->scope;
        $this->request_url = $this->paymentData->request_url;

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

    public function get_swagger(){
        try {
            $client = new Client();
            $response = $client->request('GET', "$this->request_url/fintech/api/swagger-ui.html", [
                'cert' =>   $this->certPath,
                'ssl_key' => $this->sslKeyPath, // Путь к ключевому файлу
                'verify' =>  $this->truststorePath,
                'curl' => [
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_VERBOSE => true,
                    CURLOPT_SSL_VERIFYPEER => true, // Или false, в зависимости от требований безопасности
                ],
            ]);
            return redirect()->route('settings_page')->with('success', 'Всё работает');
        } catch (RequestException $e) {
            return redirect()->route('settings_page')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('settings_page')->with(['error' => $e->getMessage()], 500);
        }
    }

    public function handleCallback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return response()->json(['error' => 'Authorization code not found'], 400);
        }
        $tokenData = $this->getAccessTokenWithAuthCode($code);
        if (isset($tokenData['error'])) {
            return redirect()->route('settings_page')->with(['error' =>$tokenData['error']], 400);
//            return response()->json(['error' => $tokenData['error']], 400);
        }
        $this->paymentData->code = $code;
        $this->paymentData->access_token = $tokenData['access_token'];
        $this->paymentData->refresh_token = $tokenData['refresh_token'];
        $this->paymentData->save();

        return redirect()->route('settings_page')->with('success', 'Токены Успешно получены');
    }

    private function getAccessTokenWithAuthCode($code)
    {
        try {
            $client = new Client();
            $response = $client->request('POST', "$this->request_url/ic/sso/api/v2/oauth/token", [
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
            return $tokens;

        } catch (RequestException $e) {
            // Логирование ошибки и возврат сообщения об ошибке
//            Log::error('Failed to exchange authorization code: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to exchange authorization code. Please try again later.'], 500);
        } catch (\Exception $e) {
            // Логирование ошибки и возврат сообщения об ошибке
//            Log::error('Failed to get access token: ' . $e->getMessage());
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

            $response = $client->request('POST', "$this->request_url/ic/sso/api/v2/oauth/token", [
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

    public function get_corporate_card(){

        $client = new Client();
        $response = $client->request('GET', "$this->request_url/fintech/api/v1/corporate-cards", [
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

}
