<?php

namespace App\Services;

use App\Models\PaymentsData;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use phpseclib3\Crypt\RSA;

class SberbankService
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $certPath;
    private $sslKeyPath;
    private $truststorePath;
    private $paymentData;
    private $accessToken;
    private $scope;
    private $refresh_token;
    private $request_url;
    public function __construct()
    {
        $this->redirectUri = route('handleCallback');
        $this->certPath = public_path('Certs/client_cert.pem');
        $this->sslKeyPath = public_path('Certs/client_key.pem');
        $this->truststorePath = public_path('Certs/combined_real_ca.pem');

        // Получаем данные из базы данных
        $this->paymentData = PaymentsData::where('bank_name', 'sberbank')->first();
        $this->clientId = $this->paymentData->client_id;
        $this->clientSecret = $this->paymentData->client_secret;
        $this->accessToken = $this->paymentData->access_token;
        $this->scope = $this->paymentData->scope;
        $this->refresh_token = $this->paymentData->refresh_token;
        $this->request_url = $this->paymentData->request_url;
        $this->publicKey = file_get_contents(public_path('Hash/ERP_SBA_SBBOL_2022.pubkey.pem'));


        $this->client = new Client([
            'base_uri' => "$this->request_url",
            'cert' => $this->certPath,
            'ssl_key' => $this->sslKeyPath,
            'verify' => $this->truststorePath,
        ]);
    }

    public function encryptCardNumber($cardNumber)
    {

        $rsa = RSA::loadPublicKey($this->publicKey);
        $rsa = $rsa->withPadding(RSA::ENCRYPTION_OAEP);
        $encryptedCardNumber = $rsa->encrypt($cardNumber);

        $encodedCardNumber = base64_encode($encryptedCardNumber);


        return $encodedCardNumber;
    }


    public function getBusinessCardCommission($amount, $receiverCardNumber = null, $receiverPhoneNumber = null, $senderBusinessCardId)
    {
        if ($receiverCardNumber) {
            $receiverCardNumber = $this->encryptCardNumber($receiverCardNumber);
        }



        try {
            $body = [
                'amount' =>  $amount,
                'receiverCardNumber' => $receiverCardNumber,
                'senderBusinessCardId' => $senderBusinessCardId,
            ];

            // Удаляем `receiverPhoneNumber` если он не используется
//        if (!is_null($receiverPhoneNumber)) {
//            $body['receiverPhoneNumber'] = $receiverPhoneNumber;
//        }

            // Логируем тело запроса для отладки
            $response = $this->client->post('fintech/api/v1/business-cards/transfer/commission', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);
            $responseBody = $response->getBody()->getContents();



            return json_decode($responseBody, true);
        } catch (RequestException $e) {

            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();

                return json_decode($responseBody, true);
            } else {


                return ['error' => $e->getMessage()];
            }
        }
    }

    public function sendBusinessCardTransfer($amount, $commission, $externalId, $purpose, $receiverCardNumber = null, $receiverPhoneNumber = null, $senderBusinessCardId, $digestSignatures = [])
    {

        if ($receiverCardNumber) {
            $receiverCardNumber = $this->encryptCardNumber($receiverCardNumber);
        }

        $body = [
            'amount' => (float) $amount,
            'commission' => (float) $commission,
            'externalId' => $externalId,
            'purpose' => $purpose,
            'receiverCardNumber' => $receiverCardNumber,
            'senderBusinessCardId' => $senderBusinessCardId,
            'digestSignatures' => $digestSignatures
        ];

        if (!is_null($receiverPhoneNumber)) {
            $body['receiverPhoneNumber'] = $receiverPhoneNumber;
        }




        try {
            $response = $this->client->post('/fintech/api/v1/business-cards/transfer', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);
            $responseBody = $response->getBody()->getContents();


            return json_decode($responseBody, true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();

                return json_decode($responseBody, true);
            } else {

                return ['error' => $e->getMessage()];
            }
        }
    }


    public function request_url()
    {
        return  $this->request_url;
    }
    public function getClientId()
    {
        return $this->clientId;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    public function getCertPath()
    {
        return $this->certPath;
    }

    public function getSslKeyPath()
    {
        return $this->sslKeyPath;
    }

    public function getTruststorePath()
    {
        return $this->truststorePath;
    }

    public function getPaymentData()
    {
        return $this->paymentData;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getRefreshToken()
    {
        return $this->refresh_token;
    }


    // Добавьте здесь методы, которые будут использоваться в контроллерах
}
