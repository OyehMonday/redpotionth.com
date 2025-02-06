<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function generatePromptPayQR($receiver, $amount = null)
    {
        // Construct PromptPay QR Code String
        $data = "0002010102122937A0000006770101110113" . $receiver . "530376";

        // Append Amount if Set
        if ($amount !== null) {
            $formattedAmount = number_format($amount, 2, '.', '');
            $data .= "5405" . $formattedAmount;
        }

        $data .= "5802TH";

        // Compute CRC16-CCITT Checksum
        $crc = strtoupper(dechex($this->crc16_ccitt($data . "6304")));
        $data .= "6304" . $crc;

        // Generate QR Code
        $qrCode = QrCode::create($data)->setSize(300)->setMargin(10);
        $writer = new PngWriter();
        $qrCodeImage = $writer->write($qrCode);

        return new Response($qrCodeImage->getString(), 200, ['Content-Type' => $qrCodeImage->getMimeType()]);
    }

    private function crc16_ccitt($str)
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($str); $i++) {
            $crc ^= ord($str[$i]) << 8;
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc <<= 1;
                }
            }
        }
        return $crc & 0xFFFF;
    }
}
