<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
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
        $templates = ContractLatter::where('vendor_id', Auth::id())->latest()->get();
        return view('pages.contracts.index', compact('templates'));
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
        $request->validate([
            'name' => 'required|string|max:100',
            'file' => 'required|mimes:pdf|max:20480', // max 20 MB
        ]);

        $path = $request->file('file')
                        ->store('vendor-templates', 'public'); // disimpan di storage/app/public/contracts

        ContractLatter::create([
            'vendor_id' => Auth::id(),
            'name'      => $request->name,
            'file_path' => $path,
        ]);

        return redirect()->route('admin-vendor.contracts.index')
                         ->with('success', 'Template kontrak berhasil di‑upload.');
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
        $contract = ContractLatter::findOrFail($id);
        return view('pages.contracts.edit', compact('contract'));
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
    public function destroy($id)
    {
        $contract = ContractLatter::findOrFail($id);
        Storage::disk('public')->delete($contract->file_path);
        $contract->delete();

        return back()->with('success', 'Template kontrak dihapus.');
    }

    private function authorizeVendor(ContractTemplate $contract)
    {
        abort_unless($contract->vendor_id === Auth::id(), 403);
    }
}
