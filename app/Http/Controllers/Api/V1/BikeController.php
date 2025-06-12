<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use Illuminate\Http\Request;

class BikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         try {
            $featured = Bike::with(['bikeMerk', 'bikeType', 'bikeColor', 'bikeCapacity'])
                ->where('availability_status', 'available')
                ->latest()
                ->take(5)
                ->get();

            $newest = Bike::with(['bikeMerk', 'bikeType', 'bikeColor', 'bikeCapacity'])
                ->latest()
                ->take(10)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Success get bikes',
                'data' => [
                    'featured' => $featured,
                    'newest' => $newest
                ],
                'error' => null
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to get bikes',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
