
<?php
session_start();

$error = "";

// Konfigurasi user contoh (idealnya ambil dari database)
$valid_username = "admin";
$valid_password = "admin123";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } elseif ($username === $valid_username && $password === $valid_password) {
        $_SESSION["logged_in"] = true;
        $_SESSION["username"] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-md">
    <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl shadow-2xl p-8">

      <div class="text-center mb-8">
        <div class="mx-auto w-16 h-16 bg-indigo-500 rounded-full flex items-center justify-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-white">Selamat Datang</h1>
        <p class="text-slate-300 text-sm mt-1">Silakan masuk ke akun Anda</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="bg-red-500/20 border border-red-400 text-red-100 text-sm rounded-lg px-4 py-2 mb-4">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" class="space-y-5">

        <div>
          <label for="username" class="block text-sm font-medium text-slate-200 mb-1">Username</label>
          <input
            type="text"
            name="username"
            id="username"
            placeholder="Masukkan username"
            class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
            required
          >
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-slate-200 mb-1">Password</label>
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Masukkan password"
            class="w-full px-4 py-2.5 rounded-lg bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
            required
          >
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center gap-2 text-slate-300">
            <input type="checkbox" name="remember" class="rounded border-white/30 bg-white/10 text-indigo-500 focus:ring-indigo-400">
            Ingat saya
          </label>
          <a href="#" class="text-indigo-300 hover:text-indigo-200">Lupa password?</a>
        </div>

        <button
          type="submit"
          class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2.5 rounded-lg transition duration-200 shadow-lg shadow-indigo-500/30"
        >
          Masuk
        </button>

      </form>

      <p class="text-center text-slate-400 text-sm mt-6">
        Belum punya akun?
        <a href="#" class="text-indigo-300 hover:text-indigo-200 font-medium">Daftar sekarang</a>
      </p>

    </div>
  </div>

</body>
</html>