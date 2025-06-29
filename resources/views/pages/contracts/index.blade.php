@extends('layouts.app')
@section('title', 'Template Kontrak')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between mb-4">
    <h1 class="text-xl font-semibold">Template Kontrak</h1>
    <a href="{{ route('admin-vendor.contracts.create') }}"
       class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded text-sm">Tambah Template</a>
  </div>

  @if(session('success'))
    <p class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</p>
  @endif

  @if($templates->isEmpty())
    <p class="text-gray-500">Belum ada template.</p>
  @else
    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-100 text-gray-600">
          <th class="px-4 py-2">Nama Template</th>
          <th class="px-4 py-2">File</th>
          <th class="px-4 py-2 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @foreach($templates as $tpl)
          <tr>
            <td class="px-4 py-2">{{ $tpl->name }}</td>
            <td class="px-4 py-2">
              <a href="{{ asset('storage/'.$tpl->file_path) }}" target="_blank"
                 class="text-indigo-600 hover:underline">Lihat PDF</a>
            </td>
            <td class="px-4 py-2 text-right space-x-2">
              {{-- <a href="{{ route('admin-vendor.contracts.edit', $tpl) }}" class="text-blue-600">Edit</a> --}}
              <form action="{{ route('admin-vendor.contracts.destroy', $tpl) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button onclick="return confirm('Hapus template?')" class="text-red-600">Hapus</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
