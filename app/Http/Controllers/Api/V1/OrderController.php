<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContractClause;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
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
            $transaction = Transaction::with([
                'bike.bikeMerk',
                'bike.bikeType',
                'bike.bikeColor',
                'bike.bikeCapacity',
                'vendor',
                'vendor.area',
                'customer'
            ])
            ->where('id', $id)
            ->where('customer_id', $user->id)
            ->firstOrFail();

            $bike = $transaction->bike;

            $response = [
                'id' => $transaction->id,
                'contract_url' => url("/api/v1/contract/{$transaction->id}/download"),
                'bike' => [
                    'name' => $bike->bikeMerk->name ?? '',
                    'merk' => $bike->bikeMerk->name ?? '',
                    'color' => $bike->bikeColor->color ?? '',
                    'type' => $bike->bikeType->name ?? '',
                    'capacity' => $bike->bikeCapacity->capacity ?? '',
                    'photo' => asset("storage/".$bike->photo),
                    'license_plate' => $bike->license_plate,
                    'price' => 'Rp ' . number_format($bike->price, 0, ',', '.')
                ],
                'renter' => [
                    'name' => $transaction->customer->name,
                    'phone' => $transaction->customer->phone,
                ],
                'rental_info' => [
                    'start_date' => $transaction->start_date,
                    'end_date' => $transaction->end_date,
                    'duration' => now()->parse($transaction->start_date)->diffInDays($transaction->end_date) . ' hari',
                    'tujuan' => $transaction->tujuan ?? null,
                    'keperluan' => $transaction->keperluan ?? null
                ],
                'biaya' => [
                    'sewa' => 'Rp ' . number_format($transaction->total, 0, ',', '.'),
                    'delivery_fee' => 'Rp ' . number_format($transaction->delivery_fee, 0, ',', '.'),
                    'total' => 'Rp ' . number_format($transaction->final_total, 0, ',', '.')
                ],
                'status' => ucfirst(str_replace('_', ' ', $transaction->status)),
                'vendor' => [
                    'business_name' => $transaction->vendor->business_name ?? '',
                    'contact_person_name' => $transaction->vendor->contact_person_name ?? '',
                    'phone' => $transaction->vendor->phone ?? '',
                    'address' => $transaction->vendor->business_address ?? '',
                    'area' => $transaction->vendor->area->name ?? '',
                    'tax_id' => $transaction->vendor->tax_id ?? '',
                    'location' => [
                        'lat' => $transaction->vendor->latitude,
                        'lng' => $transaction->vendor->longitude,
                    ],
                    'photo_attachment' => !isset($transaction->vendor->photo_attachment) ? asset("img/default.png") : asset("storage/".$transaction->vendor->photo_attachment),
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Checkout detail retrieved successfully',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Checkout detail not found or unauthorized',
            ], 404);
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

    public function downloadContract($id)
    {
        $transaction = Transaction::with(['bike.bikeMerk', 'bike.bikeType', 'customer', 'vendor'])->findOrFail($id);
        $clauses = ContractClause::where('vendor_id', $transaction->vendor_id)->pluck('content')->toArray();

        $start = \Carbon\Carbon::parse($transaction->start_date);
        $end = \Carbon\Carbon::parse($transaction->end_date);
        $durasi = $start->diffInDays($end) + 1;
        // dd($clauses)
        $pdf = Pdf::loadView('pages.contracts.print.template', [
            'tanggal' => now()->translatedFormat('d F'),
            'tahun' => now()->format('Y'),
            'vendor' => $transaction->vendor,
            'customer' => $transaction->customer,
            'bike' => $transaction->bike,
            'durasi' => $durasi,
            'start_date' => $start->format('d-m-Y'),
            'batal_hari' => 3, // bisa ubah sesuai kebijakan
            'clauses' => $clauses, // ⬅️ ini penting
        ]);

        $customer = str_replace(' ', '_', strtolower($transaction->customer->name));
        $tanggal  = \Carbon\Carbon::parse($transaction->start_date)->format('Ymd');

        $filename = "kontrak-{$customer}-{$tanggal}-{$transaction->id}.pdf";

        return $pdf->download($filename);
    }

}
