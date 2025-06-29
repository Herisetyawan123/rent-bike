@extends('layouts.app')

@section('title', 'Tambah Motor')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
  <h1 class="text-xl font-semibold text-gray-700 mb-6">Tambah Motor</h1>

  {{-- Error Alert --}}
  @if ($errors->any())
    <div class="mb-6 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
      <strong>Ups! Ada kesalahan saat mengisi form:</strong>
      <ul class="list-disc list-inside mt-2">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin-vendor.motors.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Merk -->
      <div>
        <label for="bike_merk_id" class="block mb-1 text-sm font-medium text-gray-700">Merk</label>
        <select name="bike_merk_id" id="bike_merk_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
          @foreach ($merks as $merk)
            <option value="{{ $merk->id }}">{{ $merk->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- Tipe -->
      <div>
        <label for="bike_type_id" class="block mb-1 text-sm font-medium text-gray-700">Tipe</label>
        <select name="bike_type_id" id="bike_type_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
          @foreach ($types as $type)
            <option value="{{ $type->id }}">{{ $type->name }}</option>
          @endforeach
        </select>
      </div>

      <!-- Warna -->
      <div>
        <label for="bike_color_id" class="block mb-1 text-sm font-medium text-gray-700">Warna</label>
        <select name="bike_color_id" id="bike_color_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
          @foreach ($colors as $color)
            <option value="{{ $color->id }}">{{ $color->color }}</option>
          @endforeach
        </select>
      </div>

      <!-- Kapasitas -->
      <div>
        <label for="bike_capacity_id" class="block mb-1 text-sm font-medium text-gray-700">Kapasitas</label>
        <select name="bike_capacity_id" id="bike_capacity_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
          @foreach ($capacities as $capacity)
            <option value="{{ $capacity->id }}">{{ $capacity->capacity }} CC</option>
          @endforeach
        </select>
      </div>

      <!-- Tahun -->
      <div>
        <label for="year" class="block mb-1 text-sm font-medium text-gray-700">Tahun</label>
        <input type="number" name="year" id="year"
               class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" />
      </div>

      <!-- Plat Nomor -->
      <div>
        <label for="license_plate" class="block mb-1 text-sm font-medium text-gray-700">Plat Nomor</label>
        <input type="text" name="license_plate" id="license_plate"
               class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" />
      </div>

      <!-- Harga -->
      <div>
        <label for="price" class="block mb-1 text-sm font-medium text-gray-700">Harga Sewa</label>
        <input type="number" name="price" id="price"
               class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500" />
      </div>

      <!-- Upload Foto -->
      <div>
        <label for="photo" class="block mb-1 text-sm font-medium text-gray-700">Foto Motor</label>
        <input type="file" name="photo" id="photo" accept="image/*"
               class="w-full text-sm text-gray-700 file:bg-teal-600 file:text-white file:px-4 file:py-2 file:rounded file:border-0 hover:file:bg-teal-700" />
      </div>
    </div>

    <!-- Deskripsi -->
    <div>
      <label for="description" class="block mb-1 text-sm font-medium text-gray-700">Deskripsi</label>
      <textarea name="description" id="description" rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"></textarea>
    </div>

    <!-- Submit -->
    <div class="pt-4">
      <button type="submit"
              class="bg-teal-600 text-white px-6 py-2 rounded shadow hover:bg-teal-700 transition">
        <i class="fas fa-save mr-1"></i> Simpan
      </button>
    </div>
  </form>
</div>
@endsection
