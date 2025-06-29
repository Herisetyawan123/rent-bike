@extends('layouts.app')
@section('title', 'Syarat Kontrak')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between mb-4">
    <h1 class="text-xl font-semibold">Syarat Kontrak</h1>
    <a href="{{ route('admin-vendor.contracts.create') }}"
       class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded text-sm">Tambah Syarat</a>
  </div>

  @if(session('success'))
    <p class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</p>
  @endif

  @if($clauses->isEmpty())
    <p class="text-gray-500">Belum ada syarat kontrak.</p>
  @else
    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-100 text-gray-600">
          <th class="px-4 py-2 w-12">#</th>
          <th class="px-4 py-2">Isi Syarat</th>
          <th class="px-4 py-2 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @foreach($clauses as $i => $clause)
          <tr>
            <td class="px-4 py-2">{{ $clause->order ?? $i + 1 }}</td>
            <td class="px-4 py-2">{{ $clause->content }}</td>
            <td class="px-4 py-2 text-right space-x-2">
              <a href="{{ route('admin-vendor.contracts.edit', $clause) }}" class="text-blue-600">Edit</a>
              <form action="{{ route('admin-vendor.contracts.destroy', $clause) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button onclick="return confirm('Hapus syarat ini?')" class="text-red-600">Hapus</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
