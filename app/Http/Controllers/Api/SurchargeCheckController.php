<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RemoteAreaSurcharge;
use Illuminate\Http\Request;

class SurchargeCheckController extends Controller
{
    /**
     * Check if a location has remote area surcharge
     */
    public function check(Request $request)
    {
        $request->validate([
            'country' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'partner_id' => 'nullable|exists:users,id',
        ]);

        $surcharge = RemoteAreaSurcharge::checkSurcharge(
            $request->country,
            $request->zip_code,
            $request->partner_id
        );

        if ($surcharge) {
            return response()->json([
                'success' => true,
                'is_remote' => true,
                'data' => [
                    'area_name' => $surcharge->area_name ?? 'Remote Area',
                    'surcharge_amount' => $surcharge->surcharge_amount,
                    'surcharge_percentage' => $surcharge->surcharge_percentage,
                    'zip_pattern' => $surcharge->zip_code_pattern,
                    'message' => $this->getSurchargeMessage($surcharge),
                    'warning' => $this->getWarningMessage($surcharge),
                    'match_type' => $this->getMatchType($surcharge->zip_code_pattern, $request->zip_code),
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'is_remote' => false,
            'message' => 'No remote area surcharge applies to this location.',
        ]);
    }

    /**
     * Get the match type for display
     */
    private function getMatchType($pattern, $zipCode)
    {
        if (strpos($pattern, ',') !== false) {
            return 'Multiple ranges';
        }
        if (strpos($pattern, '-') !== false) {
            return 'Range match';
        }
        if (strpos($pattern, '*') !== false) {
            return 'Wildcard match';
        }
        if ($pattern === $zipCode) {
            return 'Exact match';
        }
        if (strlen($pattern) < strlen($zipCode)) {
            if (strpos($zipCode, $pattern) === 0) {
                return 'Prefix match';
            }
            if (substr($zipCode, -strlen($pattern)) === $pattern) {
                return 'Suffix match';
            }
        }
        return 'Pattern match';
    }

    /**
     * Get surcharge message for display
     */
    private function getSurchargeMessage($surcharge)
    {
        $pattern = $surcharge->zip_code_pattern;
        $area = $surcharge->area_name ?? 'Remote Area';
        
        $message = "📍 This location is in a {$area} (Zip pattern: {$pattern}).";
        
        if ($surcharge->surcharge_amount > 0) {
            $message .= " Additional charge of $" . number_format($surcharge->surcharge_amount, 2) . " will apply.";
        } elseif ($surcharge->surcharge_percentage > 0) {
            $message .= " Additional charge of " . number_format($surcharge->surcharge_percentage, 1) . "% will apply.";
        }
        
        return $message;
    }

    /**
     * Get warning message for display
     */
    private function getWarningMessage($surcharge)
    {
        $area = $surcharge->area_name ?? 'Remote Area';
        $pattern = $surcharge->zip_code_pattern;
        return "⚠️ This address is in a {$area} (Zip pattern: {$pattern}). Please confirm with the customer about additional delivery charges.";
    }

    /**
     * Bulk check multiple zip codes
     */
    public function bulkCheck(Request $request)
    {
        $request->validate([
            'locations' => 'required|array',
            'locations.*.country' => 'required|string',
            'locations.*.zip_code' => 'required|string',
            'partner_id' => 'nullable|exists:users,id',
        ]);

        $results = [];
        foreach ($request->locations as $location) {
            $surcharge = RemoteAreaSurcharge::checkSurcharge(
                $location['country'],
                $location['zip_code'],
                $request->partner_id
            );

            $results[] = [
                'country' => $location['country'],
                'zip_code' => $location['zip_code'],
                'is_remote' => $surcharge ? true : false,
                'surcharge' => $surcharge ? [
                    'area_name' => $surcharge->area_name ?? 'Remote Area',
                    'surcharge_amount' => $surcharge->surcharge_amount,
                    'surcharge_percentage' => $surcharge->surcharge_percentage,
                    'zip_pattern' => $surcharge->zip_code_pattern,
                ] : null,
            ];
        }

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }
}