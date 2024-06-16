<?php

namespace App\Http\Controllers\Api\V2\Payments;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\SberbankService;
use Illuminate\Support\Facades\Log;

class SberbankController extends Controller
{
    protected $sberbankService;

    public function __construct(SberbankService $sberbankService)
    {
        $this->sberbankService = $sberbankService;
    }



    public function getCommission(Request $request)
    {
        $amount =  1000.00;
        $receiverCardNumber = "2200701301930720";
        $receiverPhoneNumber = null;
        $senderBusinessCardId ='0bffce3b-2851-49f9-a537-70db1913bcf7';

        $response = $this->sberbankService->getBusinessCardCommission($amount, $receiverCardNumber, $receiverPhoneNumber, $senderBusinessCardId);

        return response()->json($response);
    }

    public function sendTransfer(Request $request)
    {
        $amount = 100;
        $commission = 1;
        $externalId = $request->input('externalId');
        $purpose ="Hello";
        $receiverCardNumber = "2200701301930720";
        $receiverPhoneNumber = $request->input('receiverPhoneNumber');
        $senderBusinessCardId ='0bffce3b-2851-49f9-a537-70db1913bcf7';
        $digestSignatures = $request->input('digestSignatures', []);



        $response = $this->sberbankService->sendBusinessCardTransfer($amount, $commission, $externalId, $purpose, $receiverCardNumber, $receiverPhoneNumber, $senderBusinessCardId, $digestSignatures);

        return response()->json($response);
    }
}
