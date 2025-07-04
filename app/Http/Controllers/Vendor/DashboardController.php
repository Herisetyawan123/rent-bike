<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        $transaction_count = Transaction::where('vendor_id', $user->id)->count();
        $transaction_pending_count = Transaction::where('vendor_id', $user->id)->whereIn('status', [
             'payment_pending',
                'paid',
                'awaiting_pickup',
                'being_delivered',
                'in_use',
        ])->count();

        $bike_count = Bike::where('user_id', $user->id)->count();

        return view("pages.dashboard.index", compact('user', 'transaction_count', 'transaction_pending_count', 'bike_count'));
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
