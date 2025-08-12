<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Models\Bike;
use App\Models\BikeCapacity;
use App\Models\BikeColor;
use App\Models\BikeMerk;
use App\Models\BikeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MotorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $motors = Bike::with(['bikeMerk', 'bikeType', 'bikeColor', 'bikeCapacity'])->where("user_id", Auth::user()->id)
        ->where('status', 'accepted')
        ->get();
        return view('pages.motor.index', compact('motors'));
    }


    public function indexRequested()
    {
        $motors = Bike::with(['bikeMerk', 'bikeType', 'bikeColor', 'bikeCapacity'])->where("user_id", Auth::user()->id)
        ->where('status', 'requested')
        ->get();
        return view('pages.motor.index', compact('motors'));
    }
    
    /**
     * Display a listing of the resource.
     */
    public function draft()
    {
        $motors = Bike::with(['bikeMerk', 'bikeType', 'bikeColor', 'bikeCapacity'])
                        ->where("user_id", Auth::user()->id)
                        ->where('status', 'requested')
                        ->get();

        return view('pages.motor.index', compact('motors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.motor.create', [
            'merks' => BikeMerk::all(),
            'types' => BikeType::all(),
            'colors' => BikeColor::all(),
            'capacities' => BikeCapacity::all(),
            'add_ons' => AddOn::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bike_merk_id' => 'required|exists:bike_merks,id',
            'bike_type_id' => 'required|exists:bike_types,id',
            'bike_color_id' => 'required|exists:bike_colors,id',
            'bike_capacity_id' => 'required|exists:bike_capacities,id',
            'year' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'license_plate' => 'required|string|unique:bikes,license_plate',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable|string',
        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $filename = uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('rent-bike-photos', $filename, 'public');
        }

        $bike = new Bike();
        $bike->user_id = Auth::id();
        $bike->bike_merk_id = $request->bike_merk_id;
        $bike->bike_type_id = $request->bike_type_id;
        $bike->bike_color_id = $request->bike_color_id;
        $bike->bike_capacity_id = $request->bike_capacity_id;
        $bike->year = $request->year;
        $bike->license_plate = $request->license_plate;
        $bike->price = $request->price;
        $bike->availability_status = 'available';
        $bike->status = 'requested';
        $bike->photo = $photoPath;
        $bike->description = $request->description;
        $bike->save();
        if ($request->has('add_on')) {
            $addon = $request->add_on;
            $bike->addOns()->attach([
                $addon['id'] => ['price' => $addon['price']],
            ]);
        }
        return redirect()->route('admin-vendor.motors.index')
            ->with('success', 'Motor berhasil ditambahkan!');
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
        $bike = Bike::with("addOns")->findOrFail($id);

        // Load data untuk select-option
        $merks = BikeMerk::all();
        $types = BikeType::all();
        $colors = BikeColor::all();
        $capacities = BikeCapacity::all();
         $addOns = AddOn::all();

        return view('pages.motor.edit', compact(
            'bike',
            'merks',
            'types',
            'colors',
            'capacities',
            'addOns',
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bike = Bike::findOrFail($id);

        $request->validate([
            'bike_merk_id'     => 'required|exists:bike_merks,id',
            'bike_type_id'     => 'required|exists:bike_types,id',
            'bike_color_id'    => 'required|exists:bike_colors,id',
            'bike_capacity_id' => 'required|exists:bike_capacities,id',
            'year'             => 'required|numeric|min:2000|max:' . (date('Y') + 1),
            'license_plate'    => 'required|string|max:20',
            'price'            => 'required|numeric|min:0',
            'description'      => 'nullable|string',
            'photo'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cek & handle upload file
        if ($request->hasFile('photo')) {
            // Hapus foto lama kalau ada
            if ($bike->photo && Storage::exists($bike->photo)) {
                Storage::delete($bike->photo);
            }

            $photoPath = $request->file('photo')->store(
                'rent-bike-photos', 'public'
            );

            $bike->photo = $photoPath;
        }

        // Update data lain
        $bike->update([
            'bike_merk_id'     => $request->bike_merk_id,
            'bike_type_id'     => $request->bike_type_id,
            'bike_color_id'    => $request->bike_color_id,
            'bike_capacity_id' => $request->bike_capacity_id,
            'year'             => $request->year,
            'license_plate'    => $request->license_plate,
            'price'            => $request->price,
            'description'      => $request->description,
        ]);

            // ✅ Update add_ons
        if ($request->has('add_on')) {
            $attachData = [];

            foreach ($request->add_on as $addOn) {
                $attachData[$addOn['id']] = ['price' => $addOn['price']];
            }

            // Sinkronisasi add_on + price
            $bike->addOns()->sync($attachData);
        } else {
            // Kalau nggak ada add_on yang dicentang, hapus semua relasinya
            $bike->addOns()->detach();
        }

        return redirect()->route('admin-vendor.motors.index')
                        ->with('success', 'Motor berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $motor = Bike::findOrFail($id);

        // Hapus file foto dari storage kalau ada
        if ($motor->photo && Storage::disk('public')->exists($motor->photo)) {
            Storage::disk('public')->delete($motor->photo);
        }

        // Hapus record dari database
        $motor->delete();

        return redirect()->route('admin-vendor.motors.index')
            ->with('success', 'Motor berhasil dihapus.');
    }
}
