<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ContractClause;
use App\Models\ContractLatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clauses = ContractClause::where('vendor_id', Auth::id())->orderBy('order')->get();
        return view('pages.contracts.index', compact('clauses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.contracts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'order' => 'nullable|integer|min:1',
        ]);

        ContractClause::create([
            'vendor_id' => Auth::id(),
            'content' => $validated['content'],
            'order' => $validated['order'] ?? $this->getNextOrder(),
        ]);

        return redirect()->route('admin-vendor.contracts.index')
                        ->with('success', 'Syarat kontrak berhasil ditambahkan.');
    }

    private function getNextOrder()
    {
        $lastClause = ContractClause::where('vendor_id', Auth::id())->orderBy('order', 'desc')->first();
        return $lastClause ? $lastClause->order + 1 : 1;
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
    public function edit($id)
    {
        $clause = ContractClause::where('vendor_id', Auth::id())->findOrFail($id);
        return view('pages.contracts.edit', compact('clause'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'order' => 'nullable|integer|min:1',
        ]);

        $clause = ContractClause::where('vendor_id', Auth::id())->findOrFail($id);
        $clause->update($validated);

        return redirect()->route('admin-vendor.contracts.index')->with('success', 'Syarat kontrak diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $clause = ContractClause::where('vendor_id', Auth::id())->findOrFail($id);
        $clause->delete();

        return redirect()->route('admin-vendor.contracts.index')->with('success', 'Syarat kontrak dihapus.');
    }
}
