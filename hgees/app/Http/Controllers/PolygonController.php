<?php

namespace App\Http\Controllers;

use App\Models\Polygon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class PolygonController extends Controller
{
    /**
     * Store a newly created polygon in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'coordinates' => 'required|string',
        ]);

        try {
            // Create using the model instead of direct DB insertion
            $polygon = Polygon::create([
                'coordinates' => $validated['coordinates'],
            ]);

            return redirect()->back()->with('success', 'Polygon saved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save polygon: ' . $e->getMessage());
        }
    }
}