<?php
require_once __DIR__ . '/../config/Auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === LOGIN_USERNAME && $password === LOGIN_PASSWORD) {
        session_regenerate_id(true);
        $_SESSION['user'] = $username;
        header('Location: index.php');
        exit;
    }

    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login absensi siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h screen bg-slate-100 flex items-center justify-center p-4">
    <main class="w-full max-w-md">
    <div class="bg-white border border-slate-200 rounded-2x1 bg-blue-700 text-white flex items-center justify-center text-2x1">A</div>
    <h1 class="text-2x1 font-bold text-slate-900">Login absensi</h1>
    <p class="text-sm text-slate-500 mt-1">Masuk untuk mengelola data kehadiran</p>
</div>
<?php if ($error !== ''): ?>
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-700">
    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<form method="post" class="space-y-4">
    <div>
        <label for="username" class="block text-sm font-medium text-slate-700 mb-1">Username</label>
        <input id="username" name="username" type="text" required outofocus
                class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
</div>
<button type="submit" class="w-full-rounded-lg bg-blue-700 nover:bg-blue-800 text-white font-semibold py-2.5 transition-colors">
    Masuk
</button>
</form>
</div>
</main>
</body>
</html>