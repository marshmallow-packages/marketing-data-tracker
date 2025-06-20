<?php

namespace Marshmallow\MarketingData\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Marshmallow\MarketingData\Facades\MarketingDataTracker;

class StoreMarketingCookiesController
{
    public function __invoke(Request $request): JsonResponse
    {
        $marketing_cookies = request('marketing_cookies', []);
        ray('Storing marketing cookies', $marketing_cookies);
        // Validate the request
        $request->validate([
            'marketing_cookies' => 'required|array',
        ]);

        $marketing_cookies = $request->input('marketing_cookies', []);

        if (empty($marketing_cookies)) {
            return response()->json([
                'success' => false,
                'message' => 'No cookies provided'
            ], 400);
        }

        try {
            // Filter cookies based on configured marketing cookies
            $allowed_cookies = MarketingDataTracker::getMarketingDataCookies();
            $filtered_cookies = $this->filterRelevantCookies($marketing_cookies, $allowed_cookies);

            if (empty($filtered_cookies)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No relevant marketing cookies found',
                    'cookies_stored' => 0
                ]);
            }

            // Store cookies in session using the existing marketing data system
            $session_key = 'mm_cookie_values';
            $existing_cookies = session()->get($session_key, []);

            // Merge with existing cookies, new ones take precedence
            $updated_cookies = array_merge($existing_cookies, $filtered_cookies);

            // Store in session
            session()->put($session_key, $updated_cookies);

            return response()->json([
                'success' => true,
                'message' => 'Marketing cookies stored successfully',
                'cookies_stored' => count($filtered_cookies),
                'total_cookies' => count($updated_cookies)
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing marketing cookies: ' . $e->getMessage(), [
                'cookies' => $marketing_cookies,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store marketing cookies'
            ], 500);
        }
    }

    /**
     * Filter cookies to only include those configured for marketing tracking
     */
    private function filterRelevantCookies(array $all_cookies, array $allowed_patterns): array
    {
        $filtered = [];

        foreach ($all_cookies as $cookie_name => $cookie_value) {
            foreach ($allowed_patterns as $pattern) {
                // Handle wildcard patterns (e.g., '_ga*', '_gcl*')
                if (str_ends_with($pattern, '*')) {
                    $prefix = rtrim($pattern, '*');
                    if (str_starts_with($cookie_name, $prefix)) {
                        $filtered[$cookie_name] = $cookie_value;
                        break;
                    }
                } elseif ($cookie_name === $pattern) {
                    // Exact match
                    $filtered[$cookie_name] = $cookie_value;
                    break;
                }
            }
        }

        return $filtered;
    }
}
