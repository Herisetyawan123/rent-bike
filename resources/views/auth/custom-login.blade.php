<!DOCTYPE html>
<html>
<head>
    <title>Custom Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div style="color:red">{{ $message }}</div>
        @enderror

        <label>Password:</label>
        <input type="password" name="password" required>
        @error('password')
            <div style="color:red">{{ $message }}</div>
        @enderror

        <label>
            <input type="checkbox" name="remember"> Remember Me
        </label>

        <button type="submit">Login</button>
    </form>
</body>
</html>
