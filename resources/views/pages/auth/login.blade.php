<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Vendor</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100">

  <div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
      <h2 class="text-2xl font-semibold text-center mb-6">Login Vendor</h2>

      @if ($errors->any())
        <div class="mb-4 text-sm text-red-600">
          <ul>
            @foreach ($errors->all() as $error)
              <li>- {{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('vendor.login') }}" method="POST">
        @csrf
        
        <div class="mb-4">
          <label class="block mb-1">Email</label>
          <input type="email" name="email" class="w-full border px-3 py-2 rounded" required>
        </div>

        <div class="mb-6">
          <label class="block mb-1">Password</label>
          <input type="password" name="password" class="w-full border px-3 py-2 rounded" required>
        </div>

        <button type="submit" class="w-full bg-teal-500 hover:bg-teal-600 text-white py-2 rounded">
          Login
        </button>
      </form>

      <p class="mt-4 text-sm text-center text-gray-600">
        Belum punya akun? <a href="{{ route('vendor.register') }}" class="text-teal-600 hover:underline">Daftar di sini</a>
      </p>
    </div>
  </div>

</body>
</html>
