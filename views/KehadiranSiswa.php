
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kehadiran Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 md:p-8">

    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-800/80 backdrop-blur border border-slate-700/50 p-6 rounded-2xl shadow-xl">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i data-lucide="layout-dashboard" class="text-indigo-400"></i>
                    Dashboard Kehadiran Siswa
                </h1>
                <p class="text-slate-400 text-sm mt-1">Kelola data absensi harian siswa secara real-time</p>
            </div>
            <a href="JsonViews.php" target="_blank" class="inline-flex items-center gap-2 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 px-4 py-2.5 rounded-xl font-medium text-sm transition">
                <i data-lucide="code"></i>
                API Data JSON
            </a>
        </div>

        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-slate-800/50 border border-slate-700/40 p-4 rounded-xl">
                <p class="text-xs text-slate-400 font-medium">Hadir</p>
                <p class="text-2xl font-bold text-emerald-400 mt-1"><?= $totalHadir ?></p>
            </div>
            <div class="bg-slate-800/50 border border-slate-700/40 p-4 rounded-xl">
                <p class="text-xs text-slate-400 font-medium">Izin</p>
                <p class="text-2xl font-bold text-amber-400 mt-1"><?= $totalIzin ?></p>
            </div>
            <div class="bg-slate-800/50 border border-slate-700/40 p-4 rounded-xl">
                <p class="text-xs text-slate-400 font-medium">Sakit</p>
                <p class="text-2xl font-bold text-blue-400 mt-1"><?= $totalSakit ?></p>
            </div>
            <div class="bg-slate-800/50 border border-slate-700/40 p-4 rounded-xl">
                <p class="text-xs text-slate-400 font-medium">Alpha</p>
                <p class="text-2xl font-bold text-rose-400 mt-1"><?= $totalAlpha ?></p>
            </div>
        </div>

        <!-- Section Absensi Direct Form -->
        <form action="../controller/PresensiController.php" method="POST" class="bg-slate-800/80 backdrop-blur border border-slate-700/50 rounded-2xl p-6 shadow-xl space-y-6">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-700/60 pb-5">
                <div>
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <i data-lucide="user-check" class="text-indigo-400"></i>
                        Form Input Absensi
                    </h2>
                    <p class="text-slate-400 text-xs mt-0.5">Pilih tanggal dan tentukan status kehadiran siswa</p>
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-slate-300 font-medium flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i> Tanggal:
                    </label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-sm text-slate-100 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Tabel Absensi -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Siswa</th>
                            <th class="px-4 py-3">Kelas / Jurusan</th>
                            <th class="px-4 py-3">Status Kehadiran</th>
                            <th class="px-4 py-3 rounded-r-lg">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        <?php foreach ($siswaData as $index => $siswa): ?>
                        <tr class="hover:bg-slate-700/20 transition">
                            <td class="px-4 py-4">
                                <input type="hidden" name="nis[]" value="<?= htmlspecialchars($siswa['nis']) ?>">
                                <div class="font-medium text-white"><?= htmlspecialchars($siswa['nama_siswa']) ?></div>
                                <div class="text-xs text-slate-400">NIS: <?= htmlspecialchars($siswa['nis']) ?></div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="bg-slate-700/60 text-slate-300 px-2.5 py-1 rounded-md text-xs font-medium">
                                    <?= htmlspecialchars($siswa['kelas']) ?> - <?= htmlspecialchars($siswa['jurusan']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <select name="status_kehadiran[]" class="bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-lg p-2.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                    <option value="Hadir" <?= ($siswa['status_kehadiran'] ?? 'Hadir') === 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                    <option value="Izin" <?= ($siswa['status_kehadiran'] ?? '') === 'Izin' ? 'selected' : '' ?>>Izin</option>
                                    <option value="Sakit" <?= ($siswa['status_kehadiran'] ?? '') === 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                                    <option value="Alpha" <?= ($siswa['status_kehadiran'] ?? '') === 'Alpha' ? 'selected' : '' ?>>Alpha</option>
                                </select>
                            </td>
                            <td class="px-4 py-4">
                                <input type="text" name="keterangan[]" value="<?= htmlspecialchars($siswa['keterangan'] ?? '-') ?>" placeholder="Catatan opsional..." class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-lg px-3 py-2 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Action Button -->
            <div class="flex justify-end pt-4 border-t border-slate-700/60">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-6 py-2.5 rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Presensi
                </button>
            </div>
        </form>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>