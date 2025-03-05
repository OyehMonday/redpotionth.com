<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DebugController extends Controller
{
    /**
     * Show the debug page
     */
    public function showDebugPage()
    {
        return view('debug.regex');
    }

    /**
     * Process and extract the Reference Number using Regex
     */
    public function processDebug(Request $request)
    {
        $request->validate([
            'qr_raw_data' => 'required|string',
        ]);
    
        $qrRawData = $request->input('qr_raw_data');
    
        // Debug log to verify the raw QR data
        error_log("Raw QR Code Data: " . $qrRawData);
    
        // Step 1: Locate the reference number manually
        $extractedRefNumber = 'Not Found';
    
        // Find the starting position of the reference number (Pattern "15xxxxxxxxxxxxx")
        $pos = strpos($qrRawData, "15");
    
        if ($pos !== false) {
            // Step 2: Extract the next 15-20 characters after "15"
            $extractedRefNumber = substr($qrRawData, $pos, 20);
        }
    
        return view('debug.regex', compact('qrRawData', 'extractedRefNumber'));
    }
                
    
}
