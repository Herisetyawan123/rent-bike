@extends('layouts.app')
@section('title', 'Edit Syarat Kontrak')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-xl mx-auto">
  <h1 class="text-2xl font-semibold mb-6">Edit Syarat Kontrak</h1>

  @if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
      <ul class="list-disc list-inside text-sm">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin-vendor.contracts.update', $clause->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-4">
      <label for="content" class="block font-medium text-sm text-gray-700 mb-1">Isi Syarat</label>
      <textarea name="content" id="content" rows="4" required
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-teal-400">{{ old('content', $clause->content) }}</textarea>
    </div>

    <div class="mb-4">
      <label for="order" class="block font-medium text-sm text-gray-700 mb-1">Urutan (Opsional)</label>
      <input type="number" name="order" id="order" value="{{ old('order', $clause->order) }}"
             class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-teal-400">
    </div>

    <div class="flex justify-end space-x-2">
      <a href="{{ route('admin-vendor.contracts.index') }}"
         class="px-4 py-2 border text-gray-700 rounded hover:bg-gray-100 text-sm">Batal</a>
      <button type="submit"
              class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded text-sm">
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>
@endsection
