<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Zxing\QrReader;
use Zxing\QrReader as QrDecoder;
use Illuminate\Support\Facades\Log;

class PaymentSlipService
{
    /**
     * Extract the full raw QR Code data
     */
    public function extractQRCode($imagePath)
    {
        try {
            $qrcode = new QrDecoder($imagePath);
            return $qrcode->text(); // Return full raw QR code data
        } catch (\Exception $e) {
            Log::error("⚠️ QR Code Extraction Failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Store QR Data & Check for Duplicates
     */
    // public function storeQRCodeData($orderId, $imagePath)
    // {
    //     $refQR = $this->extractQRCode($imagePath);

    //     if (!$refQR) {
    //         Log::warning("⚠️ No QR code detected in image: " . $imagePath);
    //         return [
    //             'status' => false,
    //             'message' => 'QR Code not found in slip',
    //             'refqr' => null,
    //             'referror' => 1
    //         ];
    //     }

    //     // Step 1: Check for duplicate refQR in database
    //     $duplicate = DB::table('orders')->where('refqr', $refQR)->exists();

    //     // Step 2: Set referror flag if duplicate found
    //     $refError = $duplicate ? 1 : 0;

    //     // Debugging logs
    //     Log::info("✅ Extracted QR: " . $refQR);
    //     Log::info("🔎 Duplicate Found: " . ($duplicate ? 'Yes' : 'No'));

    //     // Step 3: Update the order with the QR data
    //     $updated = DB::table('orders')->where('id', $orderId)->update([
    //         'refqr' => $refQR,
    //         'referror' => $refError,
    //     ]);

    //     if (!$updated) {
    //         Log::error("❌ Failed to update order ID: {$orderId} with QR data.");
    //     }

    //     return [
    //         'status' => true,
    //         'message' => $duplicate ? 'Duplicate slip detected!' : 'Slip stored successfully',
    //         'refqr' => $refQR,
    //         'referror' => $refError
    //     ];
    // }

    public function storeQRCodeData($orderId, $imagePath)
    {
        $refQR = $this->extractQRCode($imagePath);
    
        // Step 1: Initialize referror as default (valid slip = 0)
        $refError = 0;
    
        // Step 2: If QR Code is not detected, set referror = 2
        if (!$refQR) {
            Log::warning("⚠️ No QR code detected in image: " . $imagePath);
            
            DB::table('orders')->where('id', $orderId)->update([
                'refqr' => null,
                'referror' => 2, // QR Code Not Found
            ]);
    
            return [
                'status' => false,
                'message' => 'QR Code not found in slip',
                'refqr' => null,
                'referror' => 2
            ];
        }
    
        // Step 3: Check for duplicate refQR in database
        $duplicate = DB::table('orders')->where('refqr', $refQR)->exists();
    
        // Step 4: If duplicate is found, set referror = 1
        if ($duplicate) {
            $refError = 1; // Duplicate QR found
        }
    
        // Step 5: Update the order with the QR data
        $updated = DB::table('orders')->where('id', $orderId)->update([
            'refqr' => $refQR,
            'referror' => $refError, // 0 = Valid, 1 = Duplicate, 2 = QR Not Found
        ]);
    
        if (!$updated) {
            Log::error("❌ Failed to update order ID: {$orderId} with QR data.");
        }
    
        return [
            'status' => true,
            'message' => $duplicate ? 'Duplicate slip detected!' : 'Slip stored successfully',
            'refqr' => $refQR,
            'referror' => $refError
        ];
    }
    
}
