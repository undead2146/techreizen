<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CityController extends Controller
{
    /**
     * Search for cities based on a query string.
     */
    public function search(Request $request)
    {
        try {
            $query = trim($request->input('query', ''));
            if (strlen($query) < 2) {
                return response()->json([]);
            }
            $queryLower = mb_strtolower($query);

            // Search for cities
            $cities = Cities::query()
                ->where(function ($q) use ($queryLower) {
                    $q->whereRaw('LOWER(plaatsnaam) LIKE ?', ["%{$queryLower}%"])
                      ->orWhere('postcode', 'LIKE', "%{$queryLower}%");
                })
                ->orderByRaw("CASE WHEN LOWER(plaatsnaam) = ? THEN 1
                             WHEN LOWER(plaatsnaam) LIKE ? THEN 2
                             WHEN postcode = ? THEN 3
                             WHEN postcode LIKE ? THEN 4
                             ELSE 5 END",
                    [$queryLower, "{$queryLower}%", $query, "{$query}%"])
                ->limit(20)
                ->get(['provincie', 'plaatsnaam', 'postcode']);

            return response()->json($cities);
        } catch (\Exception $e) {
            Log::error('City search error: ' . $e->getMessage());
            return response()->json(['error' => 'Er is een fout opgetreden'], 500);
        }
    }

    /**
     * Store a newly created city in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'plaatsnaam' => 'required|string|min:2|max:255',
                'postcode' => 'required|string|max:10',
                'provincie' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $plaatsnaam = trim($request->input('plaatsnaam'));
            $postcode = trim($request->input('postcode'));
            $provincie = trim($request->input('provincie') ?? '');
            $provincie = $provincie === '' ? null : $provincie;

            // Check if city already exists
            $existingCity = Cities::where(function($query) use ($plaatsnaam) {
                    $query->whereRaw('LOWER(plaatsnaam) = ?', [mb_strtolower($plaatsnaam)]);
                })
                ->where('postcode', $postcode)
                ->first();

            if ($existingCity) {
                // Return HTTP 200 and existing city (instead of 201) to indicate it wasn't newly created
                return response()->json($existingCity, 200);
            }

            // Create the city
            $city = new Cities();
            $city->plaatsnaam = $plaatsnaam;
            $city->postcode = $postcode;
            $city->provincie = $provincie;
            $city->save();

            // Return HTTP 201 to indicate a new resource was created
            return response()->json($city, 201);
        } catch (\Exception $e) {
            Log::error('City creation error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Er is een fout opgetreden bij het toevoegen van de gemeente'
            ], 500);
        }
    }
}
