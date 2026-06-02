<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
    }

    /**
     * Check if the Gemini API key is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Parse a raw WhatsApp message into structured order data.
     */
    public function parseWhatsAppOrder(string $rawText, int $vendorId): array
    {
        if (!$this->isConfigured()) {
            return ['error' => 'Gemini API key is not configured. Please add GEMINI_API_KEY to your .env file.'];
        }

        // Get vendor's products for context
        $products = Product::where('vendor_id', $vendorId)
            ->where('is_available', true)
            ->get(['id', 'name', 'price', 'category'])
            ->toArray();

        $productList = collect($products)->map(function ($p) {
            return "- ID: {$p['id']}, Name: {$p['name']}, Price: RM {$p['price']}, Category: {$p['category']}";
        })->join("\n");

        // Known delivery locations
        $locations = [
            'Block A - Main Lobby',
            'Block B - Cafeteria',
            'Library Entrance',
            'Dewan Kuliah Utama',
            'Counter',
        ];

        // Known time slots
        $timeSlots = [
            'Morning (8:00 - 10:00)',
            'Lunch (12:00 - 14:00)',
            'Evening (17:00 - 19:00)',
        ];

        $prompt = <<<PROMPT
You are an order parser for a campus food delivery service called BOMS. Parse the following WhatsApp message into a structured order.

VENDOR'S AVAILABLE PRODUCTS:
{$productList}

KNOWN DELIVERY LOCATIONS (pick the closest match or return empty if unclear):
- Block A - Main Lobby
- Block B - Cafeteria
- Library Entrance
- Dewan Kuliah Utama
- Counter

KNOWN TIME SLOTS (pick the closest match or return empty if unclear):
- Morning (8:00 - 10:00)
- Lunch (12:00 - 14:00)
- Evening (17:00 - 19:00)

RAW WHATSAPP MESSAGE:
"{$rawText}"

Respond with ONLY valid JSON (no markdown, no backticks), using this exact structure:
{
  "customer_name": "string or empty if not found",
  "items": [
    {
      "product_id": "number or null if no match",
      "name": "string - the product name from the menu, or the raw item name if no match",
      "qty": "number - default 1 if not specified",
      "price": "number - from the product list, or 0 if unknown"
    }
  ],
  "delivery_location": "string - one of the known locations or empty",
  "time_slot": "string - one of the known time slots or empty",
  "notes": "string - any special instructions or additional context"
}

Rules:
1. Match food items to the vendor's product list using fuzzy matching (e.g. "nasi ayam" matches "Nasi Ayam Goreng").
2. Extract quantities mentioned (e.g. "2 nasi ayam" = qty 2).
3. Use the product prices from the menu, not any prices mentioned in the message.
4. If a location like "block B" or "library" is mentioned, map it to the closest known location.
5. If a time like "1 petang" or "lunch" is mentioned, map it to the closest time slot.
6. Extract the customer's name if they introduce themselves.
7. Put any remaining context into notes.
PROMPT;

        try {
            $response = Http::timeout(15)->post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 1024,
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return ['error' => 'Failed to connect to Gemini API. Please check your API key.'];
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Clean potential markdown code fences
            $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
            $text = preg_replace('/```\s*$/m', '', $text);
            $text = trim($text);

            $parsed = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gemini returned invalid JSON', ['raw' => $text]);
                return ['error' => 'Could not parse the AI response. Please try again or enter the order manually.'];
            }

            return ['success' => true, 'data' => $parsed];

        } catch (\Exception $e) {
            Log::error('Gemini service exception', ['message' => $e->getMessage()]);
            return ['error' => 'AI service is temporarily unavailable. Please enter the order manually.'];
        }
    }
}
