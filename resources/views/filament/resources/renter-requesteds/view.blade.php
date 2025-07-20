<x-filament::page>
    <div class="space-y-6">

        <x-filament::section>
            <x-slot name="title">Data Diri</x-slot>
            <x-slot name="description">Informasi dasar dari user</x-slot>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><strong>Nama:</strong> {{ $record->user?->name }}</div>
                <div><strong>Alamat:</strong> {{ $record->address }}</div>
                <div><strong>No. KTP:</strong> {{ $record->national_id }}</div>
                <div><strong>No. SIM:</strong> {{ $record->driver_license_number }}</div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="title">Dokumen Identitas</x-slot>
            <x-slot name="description">File gambar dari dokumen</x-slot>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="font-medium mb-1">KTP Depan</p>
                    <img src="{{ $record->user?->getFirstMediaUrl('national_id_front') }}" class="rounded-lg border shadow w-48">
                </div>
                <div>
                    <p class="font-medium mb-1">KTP Belakang</p>
                    <img src="{{ $record->user?->getFirstMediaUrl('national_id_back') }}" class="rounded-lg border shadow w-48">
                </div>

                <div>
                    <p class="font-medium mb-1">SIM Depan</p>
                    <img src="{{ $record->user?->getFirstMediaUrl('driver_license_front') }}" class="rounded-lg border shadow w-48">
                </div>
                <div>
                    <p class="font-medium mb-1">SIM Belakang</p>
                    <img src="{{ $record->user?->getFirstMediaUrl('driver_license_back') }}" class="rounded-lg border shadow w-48">
                </div>

                <div class="col-span-2">
                    <p class="font-medium mb-1">Selfie dengan KTP</p>
                    <img src="{{ $record->user?->getFirstMediaUrl('selfie_with_id') }}" class="rounded-lg border shadow w-48">
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament::page>
