<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Vendor</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100">

  <div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-2xl">
      <h2 class="text-2xl font-semibold text-center mb-6">Daftar Sebagai Vendor</h2>

      @if ($errors->any())
        <div class="mb-4 text-sm text-red-600">
          <ul>
            @foreach ($errors->all() as $error)
              <li>- {{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('vendor.register') }}" method="POST">
        @csrf

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block mb-1">Nama Lengkap</label>
            <input type="text" name="name" class="w-full border px-3 py-2 rounded" required>
          </div>

          <div>
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="w-full border px-3 py-2 rounded" required>
          </div>

          <div>
            <label class="block mb-1">No. HP</label>
            <input type="text" name="phone" class="w-full border px-3 py-2 rounded" required>
          </div>

          <div>
            <label class="block mb-1">Password</label>
            <input type="password" name="password" class="w-full border px-3 py-2 rounded" required>
          </div>

          <div>
            <label class="block mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="w-full border px-3 py-2 rounded" required>
          </div>

          <div>
            <label class="block mb-1">Nama Bisnis</label>
            <input type="text" name="business_name" class="w-full border px-3 py-2 rounded" required>
          </div>

          <div>
            <label class="block mb-1">Kontak PIC</label>
            <input type="text" name="contact_person_name" class="w-full border px-3 py-2 rounded" required>
          </div>

          <div class="col-span-2">
            <label class="block mb-1">Alamat Bisnis</label>
            <textarea name="business_address" rows="2" class="w-full border px-3 py-2 rounded" required></textarea>
          </div>

          <div>
            <label class="block mb-1">NIK</label>
            <input type="text" name="national_id" class="w-full border px-3 py-2 rounded" required>
          </div>
        </div>

        <button type="submit" class="mt-6 w-full bg-teal-500 hover:bg-teal-600 text-white py-2 rounded">
          Daftar
        </button>
      </form>

      <p class="mt-4 text-sm text-center text-gray-600">
        Sudah punya akun? <a href="{{ route('vendor.login') }}" class="text-teal-600 hover:underline">Login di sini</a>
      </p>
    </div>
  </div>

</body>
</html>
