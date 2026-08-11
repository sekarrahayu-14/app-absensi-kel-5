<?php
session_start();
// Uncomment jika ingin proteksi login:
// if (empty($_SESSION['logged_in'])) { header('Location: ../login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Absensi Siswa</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">

  <!-- Navbar -->
  <nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <h1 class="text-xl font-bold text-slate-800">📋 Dashboard Absensi</h1>
    <div class="flex gap-4 text-sm font-medium">
      <a href="dashboard.php" class="text-indigo-600 border-b-2 border-indigo-600 pb-1">Absensi Siswa</a>
      <a href="kehadiran_guru.php" class="text-slate-500 hover:text-indigo-600 pb-1">Kehadiran Guru</a>
    </div>
  </nav>

  <main class="max-w-6xl mx-auto px-4 py-8">

    <!-- Kartu Ringkasan -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8" id="summary-cards">
      <div class="bg-white rounded-xl shadow p-5">
        <p class="text-slate-500 text-sm">Total Siswa</p>
        <p class="text-3xl font-bold text-slate-800 mt-1" id="total-siswa">-</p>
      </div>
      <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-slate-500 text-sm">Hadir</p>
        <p class="text-3xl font-bold text-green-600 mt-1" id="count-hadir">-</p>
      </div>
      <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
        <p class="text-slate-500 text-sm">Izin / Sakit</p>
        <p class="text-3xl font-bold text-yellow-600 mt-1" id="count-izin">-</p>
      </div>
      <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500">
        <p class="text-slate-500 text-sm">Alpa</p>
        <p class="text-3xl font-bold text-red-600 mt-1" id="count-alpa">-</p>
      </div>
    </div>

    <!-- Filter -->
    <div class="flex flex-wrap items-center gap-3 mb-4">
      <label class="text-sm font-medium text-slate-600">Tanggal:</label>
      <input type="date" id="filter-tanggal" class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
      <button id="btn-filter" class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm px-4 py-1.5 rounded-lg transition">Terapkan</button>
      <button id="btn-reset" class="text-slate-500 text-sm hover:text-slate-700">Reset</button>
    </div>

    <!-- Tabel Kehadiran Siswa -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full text-sm text-left">
        <thead class="bg-slate-800 text-white">
          <tr>
            <th class="px-4 py-3">Tanggal</th>
            <th class="px-4 py-3">NIS</th>
            <th class="px-4 py-3">Nama Siswa</th>
            <th class="px-4 py-3">Kelas</th>
            <th class="px-4 py-3">Jam Masuk</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Keterangan</th>
          </tr>
        </thead>
        <tbody id="table-body" class="divide-y divide-slate-100">
          <tr><td colspan="7" class="text-center py-6 text-slate-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>

  </main>

<script>
const API = 'JsonViews.php';

function badgeStatus(status) {
  const map = {
    'Hadir': 'bg-green-100 text-green-700',
    'Izin': 'bg-yellow-100 text-yellow-700',
    'Sakit': 'bg-blue-100 text-blue-700',
    'Alpa': 'bg-red-100 text-red-700',
  };
  const cls = map[status] || 'bg-slate-100 text-slate-700';
  return `<span class="px-2 py-1 rounded-full text-xs font-medium ${cls}">${status}</span>`;
}

async function loadSummary() {
  const res = await fetch(`${API}?action=summary&jenis=siswa`);
  const json = await res.json();
  if (json.status === 'success') {
    document.getElementById('total-siswa').textContent = json.total_siswa ?? '-';
    document.getElementById('count-hadir').textContent = json.data.Hadir ?? 0;
    document.getElementById('count-izin').textContent = (json.data.Izin ?? 0) + (json.data.Sakit ?? 0);
    document.getElementById('count-alpa').textContent = json.data.Alpa ?? 0;
  }
}

async function loadTable(tanggal = '') {
  const tbody = document.getElementById('table-body');
  tbody.innerHTML = `<tr><td colspan="7" class="text-center py-6 text-slate-400">Memuat data...</td></tr>`;

  let url = `${API}?action=list&jenis=siswa`;
  if (tanggal) url += `&tanggal=${tanggal}`;

  const res = await fetch(url);
  const json = await res.json();

  if (json.status !== 'success' || json.data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-6 text-slate-400">Tidak ada data</td></tr>`;
    return;
  }

  tbody.innerHTML = json.data.map(row => `
    <tr class="hover:bg-slate-50">
      <td class="px-4 py-3">${row.tanggal ?? '-'}</td>
      <td class="px-4 py-3">${row.nis ?? '-'}</td>
      <td class="px-4 py-3 font-medium text-slate-700">${row.nama_siswa ?? '-'}</td>
      <td class="px-4 py-3">${row.kelas ?? '-'}</td>
      <td class="px-4 py-3">${row.jam_masuk ?? '-'}</td>
      <td class="px-4 py-3">${badgeStatus(row.status)}</td>
      <td class="px-4 py-3 text-slate-500">${row.keterangan ?? '-'}</td>
    </tr>
  `).join('');
}

document.getElementById('btn-filter').addEventListener('click', () => {
  loadTable(document.getElementById('filter-tanggal').value);
});
document.getElementById('btn-reset').addEventListener('click', () => {
  document.getElementById('filter-tanggal').value = '';
  loadTable();
});

loadSummary();
loadTable();
</script>

</body>
</html>