@extends('layouts.app')

@section('title', 'Daftar Motor')

@section('content')
  <div class="grid grid-cols-1 gap-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold text-gray-700">Daftar Motor</h1>
      <a href="{{ route('admin-vendor.motors.create') }}"
         class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded text-sm shadow transition">
        <i class="fad fa-plus mr-1"></i> Tambah Motor
      </a>
    </div>

    <!-- Table Wrapper with Scroll -->
    <div class="bg-white shadow rounded-lg overflow-auto">
      <table class="min-w-full text-sm text-left text-gray-600">
        <thead class="bg-gray-100 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 font-semibold">#</th>
            <th class="px-6 py-3 font-semibold text-right">Aksi</th>
            <th class="px-6 py-3 font-semibold">Merk</th>
            <th class="px-6 py-3 font-semibold">Tipe</th>
            <th class="px-6 py-3 font-semibold">Warna</th>
            <th class="px-6 py-3 font-semibold">Kapasitas</th>
            <th class="px-6 py-3 font-semibold">Tahun</th>
            <th class="px-6 py-3 font-semibold">Plat Nomor</th>
            <th class="px-6 py-3 font-semibold">Harga</th>
            <th class="px-6 py-3 font-semibold">Ketersediaan</th>
            <th class="px-6 py-3 font-semibold">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($motors as $motor)
            <tr class="border-b hover:bg-gray-50">
              <td class="px-6 py-4">{{ $loop->iteration }}</td>
              <td class="px-6 py-4 text-right space-x-2 flex gap-5">
                <a href="{{ route('admin-vendor.motors.edit', $motor->id) }}"
                   class="text-indigo-600 hover:text-indigo-800 text-sm">
                  <i class="fad fa-edit mr-1"></i> Edit
                </a>
                <form action="{{ route('admin-vendor.motors.destroy', $motor->id) }}" method="POST" class="inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          onclick="return confirm('Yakin ingin hapus?')"
                          class="text-red-500 hover:text-red-700 text-sm">
                    <i class="fad fa-trash mr-1"></i> Hapus
                  </button>
                </form>
              </td>
              <td class="px-6 py-4">{{ $motor->bikeMerk->name ?? '-' }}</td>
              <td class="px-6 py-4">{{ $motor->bikeType->name ?? '-' }}</td>
              <td class="px-6 py-4">{{ $motor->bikeColor->color ?? '-' }}</td>
              <td class="px-6 py-4">{{ $motor->bikeCapacity->capacity ?? '-' }} cc</td>
              <td class="px-6 py-4">{{ $motor->year }}</td>
              <td class="px-6 py-4">{{ $motor->license_plate }}</td>
              <td class="px-6 py-4">Rp {{ number_format($motor->price, 0, ',', '.') }}</td>
              <td class="px-6 py-4">
                @if($motor->availability_status === 'available')
                  <span class="text-green-600 bg-green-100 px-2 py-1 rounded text-xs">Tersedia</span>
                @else
                  <span class="text-red-600 bg-red-100 px-2 py-1 rounded text-xs">Disewa</span>
                @endif
              </td>
              <td class="px-6 py-4">
                @if($motor->status === 'accepted')
                  <span class="text-green-600 bg-green-100 px-2 py-1 rounded text-xs">Aktif</span>
                @else
                  <span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded text-xs">Menunggu</span>
                @endif
              </td>
            </tr>
          @endforeach

          @if($motors->isEmpty())
            <tr>
              <td colspan="11" class="text-center text-gray-400 px-6 py-8">Belum ada data motor.</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
@endsection
