<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\PromptPay;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    public function generatePromptPayQR($idNumber, $amount = null)
    {
        $qrCodeData = PromptPay::generatePayload($idNumber, $amount);

        $qrCodeSvg = QrCode::format('svg') 
                            ->size(300)
                            ->errorCorrection('H')
                            ->generate($qrCodeData);

        return response($qrCodeSvg, 200)->header('Content-Type', 'image/svg+xml');
    }
}
