<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function Illuminate\Log\log;

class OrderController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        $orders = Transaction::with(['bike.bikeMerk', 'bike.bikeType', 'bike.bikeColor', 'bike.bikeCapacity', 'customer', 'vendor'])
            ->where('customer_id', $user->id)
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

    public function show($id)
    {
        
        $user = Auth::user();
        try {
            $order = $order = Transaction::where('transactions.id', $id)
                ->where('transactions.customer_id', $user->id)
                ->join('bikes', 'transactions.bike_id', '=', 'bikes.id')
                ->join('bike_merks', 'bikes.bike_merk_id', '=', 'bike_merks.id')
                ->join('bike_types', 'bikes.bike_type_id', '=', 'bike_types.id')
                ->select(
                    'transactions.id',
                    'transactions.bike_id',
                    'transactions.customer_id',
                    'transactions.vendor_id',
                    'bikes.price as bike_price',
                    'bikes.photo as bike_photo',
                    'bike_merks.name as bike_merk',
                    'bike_types.name as bike_type',
                    'transactions.start_date',
                    'transactions.end_date',
                    DB::raw('TIMESTAMPDIFF(DAY, transactions.start_date, transactions.end_date) as duration'),
                    'transactions.total',
                    'transactions.total_tax',
                    'transactions.status',
                    'transactions.final_total as grand_total',
                    'transactions.pickup_type',
                    'transactions.delivery_fee',
                    'transactions.delivery_address'
                )
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'message' => 'Order details retrieved successfully',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Order not found or you do not have permission to view this order.',
            ])->setStatusCode(404);
        }

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
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) ?: 1;

        $bike = \App\Models\Bike::findOrFail($id);
        $bike->availability_status = 'rented';
        $bike->save();
        $deliveryFee = $data['delivery_fee'] ?? 0;

        $marginValue = getSetting('app_margin');
        $marginType = getSetting('app_margin_type'); // 'percentage' atau 'flat'
        $taxPercent = getSetting('app_tax');

        $basePrice = $bike->price * $days;

        // Hitung margin
        $margin = $marginType === 'percentage'
            ? ($basePrice * ($marginValue / 100))
            : $marginValue;

        // Hitung total sebelum pajak
        $total = $basePrice + $margin;

        // Hitung pajak
        $totalTax = $total * ($taxPercent / 100);

        // Hitung biaya pengantaran
        $deliveryFee = $request->pickup_type === 'delivery'
            ? ($request->delivery_fee ?? 0)
            : 0;

        $finalTotal = $total + $totalTax + $deliveryFee;


        $order = Transaction::create([
            'bike_id' => $bike->id,
            'customer_id' => $user->id,
            'vendor_id' => $bike->user_id, // misalnya bike punya relasi user (vendor)
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'total'            => $basePrice + $deliveryFee,
            'total_tax'        => $totalTax,
            'final_total'      => $finalTotal,
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
