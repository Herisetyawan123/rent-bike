@extends('layouts.app')

@section('title', 'Transaksi Saya')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
  <h1 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Transaksi</h1>

  @if ($transactions->isEmpty())
    <div class="text-center text-gray-500">
      Belum ada transaksi.
    </div>
  @else
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
          <tr>
            <th class="px-4 py-2">Customer</th>
            <th class="px-4 py-2">Motor</th>
            <th class="px-4 py-2">Tgl Sewa</th>
            <th class="px-4 py-2">Tgl Kembali</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2 text-right">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @foreach ($transactions as $trx)
            <tr>
              <td class="px-4 py-2">{{ $trx->customer->name }}</td>
              <td class="px-4 py-2">{{ $trx->bike->merk->name }} - {{ $trx->bike->type->name }}</td>
              <td class="px-4 py-2">{{ $trx->start_date->format('d M Y') }}</td>
              <td class="px-4 py-2">{{ $trx->end_date->format('d M Y') }}</td>
              <td class="px-4 py-2">
                <span class="inline-block px-2 py-1 rounded text-white
                  @if($trx->status == 'paid') bg-green-500
                  @elseif($trx->status == 'cancelled') bg-red-500
                  @else bg-yellow-500 @endif">
                  {{ ucwords(str_replace('_', ' ', $trx->status)) }}
                </span>
              </td>
              <td class="px-4 py-2 text-right">Rp {{ number_format($trx->final_total, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
