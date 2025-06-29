@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow p-6 rounded-lg">
  <h2 class="text-xl font-semibold mb-4">Form Tambah Transaksi</h2>

  {{-- Tampilkan semua error secara global (opsional) --}}
  @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded mb-6">
      <ul class="list-disc list-inside text-sm">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin-vendor.transactions.store') }}" method="POST">
    @csrf

    {{-- Pilih Motor --}}
    <div class="mb-4">
      <label class="block mb-1 font-medium">Pilih Motor</label>
      <select name="bike_id" class="w-full border px-3 py-2 rounded @error('bike_id') border-red-500 @enderror">
        <option value="">-- Pilih Motor --</option>
        @foreach($bikes as $bike)
          <option value="{{ $bike->id }}" {{ old('bike_id') == $bike->id ? 'selected' : '' }}>
            {{ $bike->bikeMerk->name ?? '-' }} - {{ $bike->license_plate }}
          </option>
        @endforeach
      </select>
      @error('bike_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Pilih Customer --}}
    <div class="mb-4">
      <label class="block mb-1 font-medium">Pilih Customer</label>
      <select name="customer_id" class="w-full border px-3 py-2 rounded @error('customer_id') border-red-500 @enderror">
        <option value="">-- Pilih Customer --</option>
        @foreach($customers as $cust)
          <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
            {{ $cust->name }} ({{ $cust->email }})
          </option>
        @endforeach
      </select>
      @error('customer_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Tanggal Mulai --}}
    <div class="mb-4">
      <label class="block mb-1 font-medium">Tanggal Mulai</label>
      <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
             class="w-full border px-3 py-2 rounded @error('start_date') border-red-500 @enderror">
      @error('start_date')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Tanggal Selesai --}}
    <div class="mb-4">
      <label class="block mb-1 font-medium">Tanggal Selesai</label>
      <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
             class="w-full border px-3 py-2 rounded @error('end_date') border-red-500 @enderror">
      @error('end_date')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Jenis Pengambilan --}}
    <div class="mb-4">
      <label class="block mb-1 font-medium">Jenis Pengambilan</label>
      <select name="pickup_type" class="w-full border px-3 py-2 rounded @error('pickup_type') border-red-500 @enderror">
        <option value="pickup_self" {{ old('pickup_type') === 'pickup_self' ? 'selected' : '' }}>Ambil Sendiri</option>
        <option value="delivery" {{ old('pickup_type') === 'delivery' ? 'selected' : '' }}>Diantar</option>
      </select>
      @error('pickup_type')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Alamat Pengantaran --}}
    <div class="mb-4">
      <label class="block mb-1 font-medium">Alamat Pengantaran (jika diantar)</label>
      <textarea name="delivery_address" rows="3"
                class="w-full border px-3 py-2 rounded @error('delivery_address') border-red-500 @enderror">{{ old('delivery_address') }}</textarea>
      @error('delivery_address')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Biaya Pengantaran --}}
    <div class="mb-6">
      <label class="block mb-1 font-medium">Biaya Pengantaran (jika diantar)</label>
      <input type="number" step="1000" name="delivery_fee" value="{{ old('delivery_fee', 0) }}"
             class="w-full border px-3 py-2 rounded @error('delivery_fee') border-red-500 @enderror">
      @error('delivery_fee')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Tombol Submit --}}
    <div class="text-right">
      <button type="submit"
              class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded shadow">
        Simpan Transaksi
      </button>
    </div>
  </form>
</div>
@endsection
