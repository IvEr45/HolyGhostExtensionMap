<?php

namespace App\Http\Controllers;

use App\Models\Polygon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class PolygonController extends Controller
{
    /**
     * Get all polygons.
     */
    public function index()
    {
        return Polygon::all();
    }

    /**
     * Store a newly created polygon in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'coordinates' => 'required|string',
            'house_number' => 'required|string',
            'residents' => 'required|string',
        ]);

        try {
            // Create using the model
            $polygon = Polygon::create([
                'coordinates' => $validated['coordinates'],
                'house_number' => $validated['house_number'],
                'residents' => $validated['residents'],
            ]);

            return redirect()->back()->with('success', 'Polygon saved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save polygon: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified polygon in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'house_number' => 'required|string',
            'residents' => 'required|string',
            'color' => 'required|string'
        ]);

        try {
            $polygon = Polygon::findOrFail($id);
            
            $polygon->update([
                'house_number' => $validated['house_number'],
                'residents' => $validated['residents'],
                'color' => $validated['color']
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Property information updated successfully',
                'data' => [
                    'house_number' => $polygon->house_number,
                    'residents' => $polygon->residents
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update property information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a polygon from storage.
     */
    public function destroy($id)
    {
        try {
            $polygon = Polygon::findOrFail($id);
            $polygon->delete();
            
            return response()->json(['success' => true, 'message' => 'Polygon deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete polygon: ' . $e->getMessage()], 500);
        }
    }
    public function updateCoordinates(Request $request, $id): JsonResponse
{
    $request->validate([
        'coordinates' => 'required|string'
    ]);

    try {
        $polygon = Polygon::findOrFail($id);
        $polygon->coordinates = $request->input('coordinates');
        $polygon->save();

        return response()->json(['success' => true, 'message' => 'Coordinates updated successfully.']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Failed to update coordinates: ' . $e->getMessage()], 500);
    }
}

}