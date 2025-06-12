<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        $orders = Transaction::with(['bike.bikeMerk', 'bike.bikeType', 'bike.bikeColor', 'bike.bikeCapacity', 'customer', 'vendor'])
            ->where('customer_id', $user->id) // Bisa diganti jadi vendor_id kalau role vendor
            ->latest()
            ->get()
                    ->map(function ($order) {
            return [
                'id' => $order->id,
                'photo' => $order->bike->photo ?? null,
                'year' => $order->bike->year ?? null,
                'price' => $order->bike->price ?? null,
                'merk' => $order->bike->bikeMerk->name ?? null,
                'type' => $order->bike->bikeType->name ?? null,
                'capacity' => $order->bike->bikeCapacity->description . ' (' . $order->bike->bikeCapacity->capacity . 'cc)',
                'price_total' => $order->final_total,
                'license_plate' => $order->bike->license_plate ?? null,
                'transaction_status' => $order->status,
                'vendor_name' => $order->vendor->name ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'List of Orders',
            'data' => $orders
        ]);
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_type' => 'required|in:pickup_self,delivery',
        ]);
        $user = auth()->user();
        // hitung lama sewa
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) ?: 1;
        $bike = \App\Models\Bike::findOrFail($id);
        $bike->availability_status = 'rented';
        $bike->save();
        $deliveryFee = $data['delivery_fee'] ?? 0;

        $totalTax = getSetting('app_tax');
        $margin = getSetting('app_margin');
        $marginType = getSetting('app_margin');

        $endDate = $end;
        $finalTotal = (($bike->price * $days) + $deliveryFee);

        if($marginType == "percentage")
        {
            $finalTotal = $finalTotal * ((100 + floatval($margin)) / 100);
        }else{
            $finalTotal += $margin;
        }
        $totalTxAmount = $finalTotal * ($totalTax / 100);
        $finalTotal = $finalTotal + $totalTxAmount;

        $order = Transaction::create([
            'bike_id' => $bike->id,
            'customer_id' => $user->id,
            'vendor_id' => $bike->user_id, // misalnya bike punya relasi user (vendor)
            'start_date' => $start,
            'end_date' => $end,
            'total' => $finalTotal,
            'final_total' => $finalTotal,
            'pickup_type' => $request->pickup_type,
            'delivery_fee' => $deliveryFee,
            'delivery_address' => $request->pickup_type === 'delivery' ? $user->address : null,
            'status' => 'payment_pending',
        ]);

        return response()->json([
            'message' => 'Order created',
            'data' => $order->load('bike'),
        ]);
    }
}
