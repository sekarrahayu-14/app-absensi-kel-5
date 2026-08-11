<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kehadiran Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">📊 Dashboard Kehadiran Siswa</h1>
                    <p class="text-gray-600 mt-1">Sistem Absensi Online - MVC API</p>
                </div>
                <div class="flex gap-4">
                    <a href="/app-absensi-kel-5/index.php?action=exportCsv&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" 
                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                        📥 Export CSV
                    </a>
                    <a href="/app-absensi-kel-5/index.php?action=absensi" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">
                        ➕ Absensi Hari Ini
                    </a>
                </div>
            </div>

            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="text-sm text-green-600 font-semibold">Total Hadir</div>
                    <div class="text-2xl font-bold text-green-700"><?= $statistik['hadir'] ?? 0 ?></div>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="text-sm text-yellow-600 font-semibold">Sakit</div>
                    <div class="text-2xl font-bold text-yellow-700"><?= $statistik['sakit'] ?? 0 ?></div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="text-sm text-blue-600 font-semibold">Izin</div>
                    <div class="text-2xl font-bold text-blue-700"><?= $statistik['izin'] ?? 0 ?></div>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="text-sm text-red-600 font-semibold">Alfa</div>
                    <div class="text-2xl font-bold text-red-700"><?= $statistik['alfa'] ?? 0 ?></div>
                </div>
            </div>

            <!-- Filter -->
            <div class="mb-6">
                <form method="GET" class="flex gap-4 items-end">
                    <input type="hidden" name="action" value="dashboard">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                        <select name="bulan" class="border rounded-lg px-3 py-2 w-32">
                            <?php for($i=1; $i<=12; $i++): ?>
                                <option value="<?= $i ?>" <?= $i == $bulan ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0,0,0,$i,1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <select name="tahun" class="border rounded-lg px-3 py-2 w-32">
                            <?php for($i=date('Y')-2; $i<=date('Y'); $i++): ?>
                                <option value="<?= $i ?>" <?= $i == $tahun ? 'selected' : '' ?>>
                                    <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Tampilkan
                    </button>
                </form>
            </div>

            <!-- Tabel Rekap -->
            <?php if (empty($rekap)): ?>
                <div class="text-center py-8 text-gray-500">
                    <p>Belum ada data kehadiran untuk periode ini.</p>
                </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">NIS</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Kelas</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Jurusan</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-green-600">Hadir</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-yellow-600">Sakit</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-blue-600">Izin</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-red-600">Alfa</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Total</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rekap as $row): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($row['nis']) ?></td>
                            <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($row['nama_siswa']) ?></td>
                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($row['kelas']) ?></td>
                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($row['jurusan']) ?></td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-green-600"><?= $row['hadir'] ?? 0 ?></td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-yellow-600"><?= $row['sakit'] ?? 0 ?></td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-blue-600"><?= $row['izin'] ?? 0 ?></td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-red-600"><?= $row['alfa'] ?? 0 ?></td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-gray-600"><?= $row['total'] ?? 0 ?></td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="lihatDetail('<?= htmlspecialchars($row['nis']) ?>')" 
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium transition">
                                    📋 Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">📋 Detail Kehadiran Siswa</h2>
                <button onclick="tutupModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="detailContent"></div>
        </div>
    </div>

    <script>
        async function lihatDetail(nis) {
            try {
                const response = await fetch(`/app-absensi-kel-5/index.php?action=getKehadiran&nis=${nis}`);
                const result = await response.json();
                
                const content = document.getElementById('detailContent');
                if (result.status === 'error') {
                    content.innerHTML = `<p class="text-red-500">${result.message}</p>`;
                } else if (result.data.length === 0) {
                    content.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada data kehadiran</p>';
                } else {
                    let html = `
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left text-sm font-semibold">Tanggal</th>
                                    <th class="px-3 py-2 text-left text-sm font-semibold">Status</th>
                                    <th class="px-3 py-2 text-left text-sm font-semibold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    result.data.forEach(item => {
                        const statusColor = {
                            'Hadir': 'text-green-600',
                            'Sakit': 'text-yellow-600',
                            'Izin': 'text-blue-600',
                            'Alfa': 'text-red-600'
                        }[item.status_kehadiran] || 'text-gray-600';
                        
                        html += `
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-3 py-2 text-sm">${item.tanggal}</td>
                                <td class="px-3 py-2 text-sm font-medium ${statusColor}">${item.status_kehadiran}</td>
                                <td class="px-3 py-2 text-sm">${item.keterangan || '-'}</td>
                            </tr>
                        `;
                    });
                    html += '</tbody></table>';
                    content.innerHTML = html;
                }
                document.getElementById('detailModal').classList.remove('hidden');
            } catch(error) {
                alert('Gagal mengambil data: ' + error.message);
            }
        }

        function tutupModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Close modal on click outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) tutupModal();
        });
    </script>
</body>
</html>