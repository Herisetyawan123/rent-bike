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
        $browser = request()->get('browse');
        if($browser && $browser === 'true') 
        {
            return $this->browse();
        }

        $search = request()->get('search');

        $query = Bike::with(['bikeMerk', 'bikeType', 'bikeColor', 'bikeCapacity'])->latest();
        $query->where('availability_status', 'available');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('bikeMerk', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('bikeType', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('bikeColor', function ($q) use ($search) {
                        $q->where('color', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('bikeCapacity', function ($q) use ($search) {
                        $q->where('capacity', 'like', '%' . $search . '%');
                    });
            });
        }

        $bikes = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Success get bikes',
            'data' => $bikes,
            'error' => null,
        ]);
    }

    private function browse()
    {
        try {
            $featured = Bike::with(['bikeMerk', 'bikeType', 'bikeColor', 'bikeCapacity'])
                ->where('availability_status', 'available')
                ->latest()
                ->take(5)
                ->get();
                
                $newest = Bike::with(['bikeMerk', 'bikeType', 'bikeColor', 'bikeCapacity'])
                ->where('availability_status', 'available')
                ->latest()
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
        $bike = Bike::with(['bikeMerk', 'bikeType', 'bikeColor', 'bikeCapacity', 'addOns'])->find($id);

        if (!$bike) {
            return response()->json([
                'success' => false,
                'message' => 'Bike not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $bike
        ], 200);
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
