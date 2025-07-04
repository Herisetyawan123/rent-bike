@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="mx-auto max-w-4xl p-6 bg-white shadow rounded">
    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 text-green-600 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin-vendor.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- USERS ------------------------------------------------------------------}}
        <h2 class="text-lg font-bold">Data Akun</h2>

        <div>
            <label class="block mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded p-2">
            @error('name') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded p-2">
            @error('email') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block mb-1">Telepon</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full border rounded p-2">
            @error('phone') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block mb-1">Foto Profil</label>
            <input type="file" name="photo" class="w-full">
            @if($user->photo) <img src="{{ asset("storage/".$user->photo) }}" alt="foto" class="h-16 mt-2"> @endif
            @error('photo') <div class="text-red-600">{{ $message }}</div> @enderror
        </div>

        {{-- VENDOR ------------------------------------------------------------------}}
        @if($vendor)
            <hr class="my-6">
            <h2 class="text-lg font-bold">Data Vendor</h2>

            <div>
                <label class="block mb-1">Nama Usaha</label>
                <input type="text" name="business_name" value="{{ old('business_name', $vendor->business_name) }}" class="w-full border rounded p-2">
                @error('business_name') <div class="text-red-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1">Nama PIC</label>
                <input type="text" name="contact_person_name" value="{{ old('contact_person_name', $vendor->contact_person_name) }}" class="w-full border rounded p-2">
                @error('contact_person_name') <div class="text-red-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1">Area</label>
                <select name="area_id" class="w-full border rounded p-2">
                    <option value="">-- Pilih Area --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id', $vendor->area_id) == $area->id ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
                @error('area_id') <div class="text-red-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1">NPWP</label>
                <input type="text" name="tax_id" value="{{ old('tax_id', $vendor->tax_id) }}" class="w-full border rounded p-2">
                @error('tax_id') <div class="text-red-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1">Alamat Usaha</label>
                <textarea name="business_address" rows="3" class="w-full border rounded p-2">{{ old('business_address', $vendor->business_address) }}</textarea>
                @error('business_address') <div class="text-red-600">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 hidden">
                <div>
                    <label class="block mb-1">Latitude</label>
                    <input type="text" name="latitude" value="{{ old('latitude', $vendor->latitude) }}" class="w-full border rounded p-2">
                    @error('latitude') <div class="text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block mb-1">Longitude</label>
                    <input type="text" name="longitude" value="{{ old('longitude', $vendor->longitude) }}" class="w-full border rounded p-2">
                    @error('longitude') <div class="text-red-600">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="block mb-1">Lampiran Foto Tempat Usaha</label>
                <input type="file" name="photo_attachment" class="w-full">
                @if($vendor->photo_attachment) <a href="{{ asset("storage/".$vendor->photo_attachment) }}" target="_blank" class="text-blue-600 underline">Lihat</a> @endif
                @error('photo_attachment') <div class="text-red-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1">NIK Pemilik</label>
                <input type="text" name="national_id" value="{{ old('national_id', $vendor->national_id) }}" class="w-full border rounded p-2">
                @error('national_id') <div class="text-red-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block mb-1">Dokumen Legal (PDF/JPG)</label>
                <input type="file" name="legal_documents" class="w-full">
                @if($vendor->legal_documents) <a href="{{ asset("storage/".$vendor->legal_documents) }}" target="_blank" class="text-blue-600 underline">Lihat</a> @endif
                @error('legal_documents') <div class="text-red-600">{{ $message }}</div> @enderror
            </div>
        @endif

        {{-- SUBMIT ------------------------------------------------------------------}}
        <div class="pt-4">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
