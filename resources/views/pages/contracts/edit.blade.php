@extends('layouts.app')
@section('title', 'Tambah Template Kontrak')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
  <h1 class="text-xl font-semibold mb-4">Upload Template PDF</h1>

  <form action="{{ route('admin-vendor.contracts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-4">
      <label class="block mb-1 font-medium">Nama Template</label>
      <input type="text" name="name" value="{{ old('name') }}"
             class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror">
      @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mb-6">
      <label class="block mb-1 font-medium">File PDF</label>
      <input type="file" name="file" accept="application/pdf"
             class="w-full border rounded px-3 py-2 @error('file') border-red-500 @enderror">
      @error('file') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="text-right">
      <button class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded">Upload</button>
    </div>
  </form>
</div>
@endsection
