<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\ContractClause;
use App\Models\ContractLatter;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use setasign\Fpdi\Fpdi;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendorId = Auth::id(); // atau bisa juga dari request param
        $transactions = Transaction::with(['bike.bikeMerk', 'bike.bikeType', 'customer'])
            ->where('vendor_id', $vendorId)
            ->orderByDesc('created_at')
            ->get();

        return view('pages.transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bikes = Bike::with(['bikeMerk', 'bikeType', 'addOns']) // tambahkan relasi jika perlu
                ->where('user_id', Auth::id())
                ->get();

        // Ambil semua user yang merupakan customer
        $customers = User::role('renter')->get();

        return view('pages.transactions.create', compact('bikes', 'customers'));
    }

    // public function create()
    // {
    //     $bikes = Bike::with('bikeMerk', 'addOns')->get(); // Motor + Merk + Add-ons
    //     $customers = User::role('renter')->get();

    //     return view('pages.transactions.cashier', compact('bikes', 'customers'));
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'bike_id'          => 'required|exists:bikes,id',
            'customer_id'      => 'required|exists:users,id',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'pickup_type'      => 'required|in:pickup_self,delivery',
            'delivery_address' => 'nullable|string',
            'delivery_fee'     => 'nullable|numeric|min:0',
        ]);

        // Ambil data motor
        $bike = Bike::findOrFail($validated['bike_id']);
        $bike->availability_status = 'rented';
        $bike->save();
        
        // Hitung durasi hari
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDays($endDate) + 1;

        // Ambil settingan margin & tax
        $marginValue = getSetting('app_margin');
        $marginType = getSetting('app_margin_type'); // 'percentage' atau 'flat'
        $taxPercent = getSetting('app_tax');

        // Hitung base price
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
        $deliveryFee = $validated['pickup_type'] === 'delivery'
            ? ($validated['delivery_fee'] ?? 0)
            : 0;

        // Final total
        $finalTotal = $total + $totalTax + $deliveryFee;

        // Simpan transaksi
        $transaction = Transaction::create([
            'bike_id'          => $bike->id,
            'customer_id'      => $validated['customer_id'],
            'vendor_id'        => Auth::id(),
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'total'            => $basePrice + $deliveryFee,
            'total_tax'        => $totalTax,
            'final_total'      => $finalTotal,
            'paid_total'       => null,
            'pickup_type'      => $validated['pickup_type'],
            'delivery_address' => $validated['delivery_address'],
            'delivery_fee'     => $deliveryFee,
            'status'           => 'payment_pending',
        ]);

        return redirect()->route('admin-vendor.transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
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
        $transaction = Transaction::findOrFail($id);

        // Pastikan hanya vendor yang bersangkutan bisa update
        abort_unless($transaction->vendor_id === Auth::id(), 403);

        // Validasi hanya untuk status (kalau memang hanya status yang diupdate)
        $request->validate([
            'status' => 'required|in:payment_pending,paid,awaiting_pickup,being_delivered,in_use,cancelled,completed',
        ]);

        // Update status
        $transaction->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin-vendor.transactions.index')
                        ->with('success', 'Status transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function downloadContract($transactionId)
    {
        $transaction = Transaction::with(['bike.bikeMerk', 'bike.bikeType', 'customer', 'vendor'])->findOrFail($transactionId);
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

    public function downloadContract2(Transaction $transaction)
    {
        // pastikan vendor pemilik transaksi
        abort_unless($transaction->vendor_id === Auth::id(), 403);

        // ambil template PDF vendor
        $template = ContractLatter::where('vendor_id', $transaction->vendor_id)->firstOrFail();
        $srcPath  = storage_path('app/public/'.$template->file_path);

        // siapkan FPDI
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($srcPath);

        // loop semua halaman, lalu overlay teks di halaman 1
        for ($page = 1; $page <= $pageCount; $page++) {
            $tplIdx = $pdf->importPage($page);
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx);

            if ($page === 1) {            // tulis hanya di halaman pertama
                $pdf->SetFont('Helvetica', '', 12);
                $pdf->SetTextColor(0, 0, 0);

                // ==== KOORDINAT NYA CONTOH ====
                $pdf->SetXY(40, 120);                               // posisi [Nama]
                $pdf->Write(0, $transaction->customer->name);

                $pdf->SetXY(40, 130);                               // posisi [Alamat]
                $pdf->MultiCell(120, 5, $transaction->customer->address ?? '-');

                $pdf->SetXY(40, 140);                               // posisi [Tanggal]
                $pdf->Write(0, now()->translatedFormat('d F Y'));
            }
        }

        // kirim ke browser (inline)
        return response($pdf->Output('S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename=kontrak-'.$transaction->id.'.pdf',
        ]);
    }
}
