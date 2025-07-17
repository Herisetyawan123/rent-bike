@extends('layouts.app')

@section('title', 'Transaksi Saya')
@push('styles')
{{-- Alpine (ukuran sangat kecil, cukup untuk modal) --}}
<script defer src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@section('content')
<div class="bg-white p-6 rounded-lg shadow-md" x-data="statusModal()">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-gray-800">Daftar Transaksi</h1>
    <a href="{{ route('admin-vendor.transactions.create') }}"
        class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded text-sm shadow transition">
        <i class="fad fa-plus mr-1"></i> Tambah Transaksi
    </a>
  </div>

  @if ($transactions->isEmpty())
    <div class="text-center text-gray-500">
      Belum ada transaksi.
    </div>
  @else
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm text-left text-gray-700">
        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
          <tr>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Action</th>
            <th class="px-4 py-2">Customer</th>
            <th class="px-4 py-2">Motor</th>
            <th class="px-4 py-2">Tgl Sewa</th>
            <th class="px-4 py-2">Tgl Kembali</th>
            <th class="px-4 py-2">Harga / Hari</th>
            <th class="px-4 py-2">Pajak</th>
            {{-- <th class="px-4 py-2">Admin</th> --}}
            <th class="px-4 py-2">Harga Pengantaran</th>
            <th class="px-4 py-2 text-right">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @foreach ($transactions as $trx)
            <tr>
              <td class="px-4 py-2">
                <button @click="open({{ $trx->id }}, '{{ $trx->status }}')" class="text-xs px-2 py-1 rounded
                  text-white font-medium
                  @if($trx->status == 'paid') bg-green-500
                  @elseif($trx->status == 'cancelled') bg-red-500
                  @else bg-yellow-500 @endif">
                  {{ ucwords(str_replace('_', ' ', $trx->status)) }}
                </button>
              </td>
              <td class="px-4 py-2">
                <a href="{{ route('admin-vendor.transactions.contract', $trx) }}"
                  class="text-indigo-600 hover:underline" target="_blank">
                  <i class="fas fa-file-pdf mr-1"></i> Kontrak
                </a>
              </td>
              <td class="px-4 py-2">{{ $trx->customer->name }}</td>
              <td class="px-4 py-2">{{ $trx->bike->bikeMerk->name }} - {{ $trx->bike->bikeType->name }}</td>
              <td class="px-4 py-2">{{ \Carbon\Carbon::parse($trx->start_date)->format('d M Y') }}</td>
              <td class="px-4 py-2">{{ \Carbon\Carbon::parse($trx->end_date)->format('d M Y') }}</td>
              <td class="px-4 py-2">Rp {{ number_format($trx->bike->price, 0, ',', '.') }}</td>
              <td class="px-4 py-2">Rp {{ number_format($trx->total_tax, 0, ',', '.') }}</td>
              {{-- <td class="px-4 py-2">Rp {{ number_format($trx->final_total - ($trx->final_total - $trx->delivery_fee - $trx->total_tax), 0, ',', '.') }}</td> --}}
              <td class="px-4 py-2">Rp {{ number_format($trx->delivery_fee, 0, ',', '.') }}</td>
              <td class="px-4 py-2 text-right">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  <!-- ===== Modal update status ===== -->
  <div
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center z-50 bg-black/50">

    <div
      @click.outside="close"
      x-show="show"
      x-transition:enter="transition transform ease-out duration-200"
      x-transition:enter-start="scale-90 opacity-0"
      x-transition:enter-end="scale-100 opacity-100"
      x-transition:leave="transition transform ease-in duration-150"
      x-transition:leave-start="scale-100 opacity-100"
      x-transition:leave-end="scale-90 opacity-0"
      class="bg-white w-[480px] p-6 rounded-xl shadow-2xl">

      <h2 class="text-lg font-semibold mb-4">Ubah Status Transaksi</h2>

      <form :action="`/admin-vendor/transactions/${id}`" method="POST">
        @csrf
        @method('PUT')

        <select name="status" x-model="currentStatus"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-teal-400">
          <template x-for="opt in statuses" :key="opt">
            <option :value="opt" x-text="label(opt)"></option>
          </template>
        </select>

        <div class="text-right mt-4">
          <button type="button" @click="close"
                  class="mr-2 px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">Batal</button>
          <button type="submit"
                  class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded text-sm shadow">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script>
  function statusModal() {
    return {
      show: false,
      id: null,
      currentStatus: '',
      statuses: [
        'payment_pending',
        'paid',
        'awaiting_pickup',
        'being_delivered',
        'in_use',
        'cancelled',
        'completed'
      ],
      open(id, status) {
        this.id = id;
        this.currentStatus = status;
        this.show = true;
      },
      close() {
        this.show = false;
        this.id = null;
        this.currentStatus = '';
      },
      label(status) {
        return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
      }
    }
  }
  </script>
@endpush
