<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiParserController extends Controller
{
    /**
     * Parse a raw WhatsApp message into structured order data using Gemini AI.
     */
    public function parse(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:5|max:2000',
        ]);

        $gemini = new GeminiService();

        if (!$gemini->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'Gemini API key is not configured. Please add GEMINI_API_KEY to your .env file.',
            ], 422);
        }

        $result = $gemini->parseWhatsAppOrder(
            $request->input('message'),
            Auth::id()
        );

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }
}
