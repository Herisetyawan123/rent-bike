<div class="space-y-10 text-sm text-gray-700 dark:text-gray-100">

    {{-- National ID --}}
    <div class="space-y-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            📄 KTP (ID Card)
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-2">
                <p class="text-gray-600 dark:text-gray-300 font-medium">Front</p>
                <div class="border rounded-lg overflow-hidden shadow-sm">
                    <img src="{{ $documents['national_id_front'] }}"
                         alt="KTP Front"
                         class="w-full h-auto object-cover" />
                </div>
            </div>
            <div class="space-y-2">
                <p class="text-gray-600 dark:text-gray-300 font-medium">Back</p>
                <div class="border rounded-lg overflow-hidden shadow-sm">
                    <img src="{{ $documents['national_id_back'] }}"
                         alt="KTP Back"
                         class="w-full h-auto object-cover" />
                </div>
            </div>
        </div>
    </div>

    {{-- Driver License --}}
    <div class="space-y-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            🚗 SIM (Driver License)
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-2">
                <p class="text-gray-600 dark:text-gray-300 font-medium">Front</p>
                <div class="border rounded-lg overflow-hidden shadow-sm">
                    <img src="{{ $documents['driving_license_front'] }}"
                         alt="SIM Front"
                         class="w-full h-auto object-cover" />
                </div>
            </div>
            <div class="space-y-2">
                <p class="text-gray-600 dark:text-gray-300 font-medium">Back</p>
                <div class="border rounded-lg overflow-hidden shadow-sm">
                    <img src="{{ $documents['driving_license_back'] }}"
                         alt="SIM Back"
                         class="w-full h-auto object-cover" />
                </div>
            </div>
        </div>
    </div>

    {{-- Selfie --}}
    <div class="space-y-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            🤳 Selfie with KTP
        </h3>
        <div class="border rounded-lg overflow-hidden shadow-sm max-w-xs">
            <img src="{{ $documents['selfie_with_id'] }}"
                 alt="Selfie with KTP"
                 class="w-full h-auto object-cover" />
        </div>
    </div>

    {{-- Info Data --}}
    <div class="space-y-2 text-sm text-gray-800 dark:text-gray-200">
        <p><span class="font-semibold">National ID Number:</span> {{ $renter->national_id }}</p>
        <p><span class="font-semibold">Driver License Number:</span> {{ $renter->driver_license_number }}</p>
        <p><span class="font-semibold">Date of Birth:</span> {{ $renter->birth_date }}</p>
        <p><span class="font-semibold">Address:</span> {{ $renter->address }}</p>
        <p><span class="font-semibold">Current Address:</span> {{ $renter->current_address }}</p>
    </div>

    {{-- Approval Notes Form --}}
    <div class="space-y-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            📝 Admin Notes
        </h3>
        <form method="POST" action="" class="space-y-4">
            @csrf

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Catatan Approval / Rejection
                </label>
                <textarea id="notes" name="notes" rows="4"
                          class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:ring-primary-500 focus:border-primary-500 resize-none"
                          placeholder="Masukkan catatan jika perlu..."></textarea>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" name="action" value="approve"
                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                    ✅ Approve
                </button>
                <button type="submit" name="action" value="reject"
                        class="inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                    ❌ Reject
                </button>
            </div>
        </form>
    </div>
</div>
