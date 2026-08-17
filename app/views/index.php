<?php
require_once __DIR__ . '/../../config/Auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Absensi Siswa - XII PPLG</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-4 md:p-8">
<div class="max-w-6xl mx-auto">

    <header class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="text-center sm:text-left">
            <h1 class="text-2xl md:text-3xl font-bold text-blue-900">Dashboard Absensi Siswa</h1>
            <p class="text-slate-500 text-sm md:text-base">Pantau kehadiran siswa secara real-time, mudah dan akurat."</p>
        </div>
        <div class="flex items-center gap-2">
        <a href="tambah-siswa.php" class="inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-medium px-4 py-2.5 rounded-lg transition-colors text-sm shadow-sm">
            <span class="text-base leading-none">＋</span> Tambah Siswa
        </a>
        <a href="logout.php" class="inline-flex items-center justify-center bg-slate-700 hover:bg-slate-800 text-white font-medium px-4 py-2.5 rounded-lg transition-colors text-sm shadow-sm">
            Keluar
        </a>
        </div>
    </header>

    <!-- Filter tanggal -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 bg-white border border-blue-100 rounded-xl px-4 py-3 shadow-sm">
        <span class="text-sm font-medium text-blue-800">📅 Data kehadiran untuk tanggal:</span>
        <input type="date" id="filterTanggal" class="border border-blue-200 rounded-lg px-3 py-1.5 text-sm outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    <!-- Ringkasan kehadiran -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-8" id="summaryCards"></div>

    <!-- Tabel siswa + input kehadiran (full width) -->
    <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-blue-100">
        <h2 class="text-lg md:text-xl font-semibold mb-4 text-blue-900">Daftar Siswa & Kehadiran Hari Ini</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-blue-50 text-blue-700 border-b">
                        <th class="p-3 font-semibold whitespace-nowrap">NIS</th>
                        <th class="p-3 font-semibold whitespace-nowrap">Nama</th>
                        <th class="p-3 font-semibold whitespace-nowrap">Kelas</th>
                        <th class="p-3 font-semibold whitespace-nowrap">Status</th>
                        <th class="p-3 font-semibold whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="siswaTableBody">
                    <!-- Data dimuat melalui JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
const API_SISWA = '../../api/siswa.php';
const API_KEHADIRAN = '../../api/kehadiran.php';

const statusColor = {
    'Hadir': 'bg-blue-100 text-blue-700',
    'Sakit': 'bg-blue-50 text-blue-500',
    'Izin': 'bg-slate-100 text-slate-600',
    'Alpha': 'bg-red-100 text-red-700',       // satu-satunya warna merah di badge status
    'Belum Absen': 'bg-slate-50 text-slate-400',
};

document.getElementById('filterTanggal').valueAsDate = new Date();

async function loadSummary(tanggal) {
    const res = await fetch(`${API_KEHADIRAN}?action=summary&tanggal=${tanggal}`);
    const result = await res.json();
    if (result.status !== 'success') return;

    const d = result.data;
    const cards = [
        { label: 'Hadir', value: d.Hadir, color: 'bg-blue-700', icon: '✅' },
        { label: 'Sakit', value: d.Sakit, color: 'bg-blue-400', icon: '🤒' },
        { label: 'Izin', value: d.Izin, color: 'bg-slate-400', icon: '📄' },
        { label: 'Alpha', value: d.Alpha, color: 'bg-red-500', icon: '⛔' }, // satu-satunya kartu merah
    ];
    document.getElementById('summaryCards').innerHTML = cards.map(c => `
        <div class="rounded-xl p-3 md:p-4 text-white shadow-sm ${c.color}">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-medium opacity-90">${c.label}</span>
                <span>${c.icon}</span>
            </div>
            <p class="text-xl md:text-2xl font-bold">${c.value}</p>
        </div>
    `).join('');
}

async function loadKehadiran(tanggal) {
    try {
        const response = await fetch(`${API_KEHADIRAN}?tanggal=${tanggal}`);
        const result = await response.json();
        const tableBody = document.getElementById('siswaTableBody');
        tableBody.innerHTML = '';

        if (result.status === 'success' && result.data.length > 0) {
            result.data.forEach(s => {
                tableBody.innerHTML += `
                <tr class="border-b hover:bg-blue-50/40 text-slate-700">
                    <td class="p-3 font-medium whitespace-nowrap">${s.nis}</td>
                    <td class="p-3 whitespace-nowrap">${s.nama}</td>
                    <td class="p-3 whitespace-nowrap"><span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-full font-medium">${s.kelas}</span></td>
                    <td class="p-3 whitespace-nowrap">
                        <select onchange="setKehadiran(${s.id_siswa}, this.value)" class="text-xs border border-slate-300 rounded-lg px-2 py-1 outline-none ${statusColor[s.status] || ''}">
                            <option value="" disabled ${s.status === 'Belum Absen' ? 'selected' : ''}>Belum Absen</option>
                            <option value="Hadir" ${s.status === 'Hadir' ? 'selected' : ''}>Hadir</option>
                            <option value="Sakit" ${s.status === 'Sakit' ? 'selected' : ''}>Sakit</option>
                            <option value="Izin" ${s.status === 'Izin' ? 'selected' : ''}>Izin</option>
                            <option value="Alpha" ${s.status === 'Alpha' ? 'selected' : ''}>Alpha</option>
                        </select>
                    </td>
                    <td class="p-3 whitespace-nowrap">
                        <button onclick="hapusSiswa(${s.id_siswa})" class="text-red-600 hover:text-white hover:bg-red-500 border border-red-200 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                            Hapus
                        </button>
                    </td>
                </tr>`;
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-slate-500">Belum ada data siswa.</td></tr>`;
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

async function setKehadiran(idSiswa, status) {
    if (!status) return;
    await fetch(API_KEHADIRAN, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_siswa: idSiswa, status })
    });
    const tanggal = document.getElementById('filterTanggal').value;
    loadSummary(tanggal);
    loadKehadiran(tanggal);
}

async function hapusSiswa(id) {
    if (!confirm('Yakin ingin menghapus data siswa ini?')) return;
    try {
        const response = await fetch(`${API_SISWA}?id=${id}`, { method: 'DELETE' });
        const result = await response.json();
        if (response.ok) {
            const tanggal = document.getElementById('filterTanggal').value;
            loadSummary(tanggal);
            loadKehadiran(tanggal);
        } else {
            alert(result.message || 'Gagal menghapus data');
        }
    } catch (error) {
        console.error('Error deleting data:', error);
    }
}

document.getElementById('filterTanggal').addEventListener('change', (e) => {
    loadSummary(e.target.value);
    loadKehadiran(e.target.value);
});

// Load awal
const tanggalAwal = document.getElementById('filterTanggal').value;
loadSummary(tanggalAwal);
loadKehadiran(tanggalAwal);
</script>
</body>
</html>