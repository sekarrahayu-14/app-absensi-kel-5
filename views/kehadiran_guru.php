
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kehadiran Guru</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">

  <!-- Navbar -->
  <nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <h1 class="text-xl font-bold text-slate-800">📋 Dashboard Absensi</h1>
    <div class="flex gap-4 text-sm font-medium">
      <a href="dashboard.php" class="text-slate-500 hover:text-indigo-600 pb-1">Absensi Siswa</a>
      <a href="kehadiran_guru.php" class="text-indigo-600 border-b-2 border-indigo-600 pb-1">Kehadiran Guru</a>
    </div>
  </nav>

  <main class="max-w-6xl mx-auto px-4 py-8">

    <!-- Kartu Ringkasan -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-slate-500 text-sm">Hadir</p>
        <p class="text-3xl font-bold text-green-600 mt-1" id="count-hadir">-</p>
      </div>
      <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
        <p class="text-slate-500 text-sm">Izin</p>
        <p class="text-3xl font-bold text-yellow-600 mt-1" id="count-izin">-</p>
      </div>
      <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
        <p class="text-slate-500 text-sm">Sakit</p>
        <p class="text-3xl font-bold text-blue-600 mt-1" id="count-sakit">-</p>
      </div>
      <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500">
        <p class="text-slate-500 text-sm">Alpa</p>
        <p class="text-3xl font-bold text-red-600 mt-1" id="count-alpa">-</p>
      </div>
    </div>

    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-slate-800">Daftar Kehadiran Guru Hari Ini</h2>
      <button id="btn-open-modal" class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg transition">
        + Input Kehadiran
      </button>
    </div>

    <!-- Tabel Kehadiran Guru -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <table class="w-full text-sm text-left">
        <thead class="bg-slate-800 text-white">
          <tr>
            <th class="px-4 py-3">Tanggal</th>
            <th class="px-4 py-3">Nama Guru</th>
            <th class="px-4 py-3">Jam Masuk</th>
            <th class="px-4 py-3">Jam Keluar</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Keterangan</th>
            <th class="px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody id="table-body" class="divide-y divide-slate-100">
          <tr><td colspan="7" class="text-center py-6 text-slate-400">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>

  </main>

  <!-- Modal Input Kehadiran Guru -->
  <div id="modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center px-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
      <h3 class="text-lg font-semibold text-slate-800 mb-4">Input Kehadiran Guru</h3>
      <form id="form-kehadiran" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Nama Guru</label>
          <input type="text" name="nama_guru" required
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Jam Masuk</label>
            <input type="time" name="jam_masuk"
              class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
            <select name="status" required
              class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
              <option value="Hadir">Hadir</option>
              <option value="Izin">Izin</option>
              <option value="Sakit">Sakit</option>
              <option value="Alpa">Alpa</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1">Keterangan</label>
          <textarea name="keterangan" rows="2"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" id="btn-close-modal" class="px-4 py-2 text-sm text-slate-500 hover:text-slate-700">Batal</button>
          <button type="submit" class="px-4 py-2 text-sm bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition">Simpan</button>
        </div>
      </form>
    </div>
  </div>

<script>
const API = 'JsonViews.php';
const modal = document.getElementById('modal');

document.getElementById('btn-open-modal').addEventListener('click', () => {
  modal.classList.remove('hidden');
  modal.classList.add('flex');
});
document.getElementById('btn-close-modal').addEventListener('click', closeModal);
function closeModal() {
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  document.getElementById('form-kehadiran').reset();
}

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
  const res = await fetch(`${API}?action=summary&jenis=guru`);
  const json = await res.json();
  if (json.status === 'success') {
    document.getElementById('count-hadir').textContent = json.data.Hadir ?? 0;
    document.getElementById('count-izin').textContent = json.data.Izin ?? 0;
    document.getElementById('count-sakit').textContent = json.data.Sakit ?? 0;
    document.getElementById('count-alpa').textContent = json.data.Alpa ?? 0;
  }
}

async function loadTable() {
  const tbody = document.getElementById('table-body');
  tbody.innerHTML = `<tr><td colspan="7" class="text-center py-6 text-slate-400">Memuat data...</td></tr>`;

  const today = new Date().toISOString().slice(0, 10);
  const res = await fetch(`${API}?action=list&jenis=guru&tanggal=${today}`);
  const json = await res.json();

  if (json.status !== 'success' || json.data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-6 text-slate-400">Belum ada data kehadiran guru hari ini</td></tr>`;
    return;
  }

  tbody.innerHTML = json.data.map(row => `
    <tr class="hover:bg-slate-50">
      <td class="px-4 py-3">${row.tanggal ?? '-'}</td>
      <td class="px-4 py-3 font-medium text-slate-700">${row.nama_guru ?? '-'}</td>
      <td class="px-4 py-3">${row.jam_masuk ?? '-'}</td>
      <td class="px-4 py-3">${row.jam_keluar ?? '-'}</td>
      <td class="px-4 py-3">${badgeStatus(row.status)}</td>
      <td class="px-4 py-3 text-slate-500">${row.keterangan ?? '-'}</td>
      <td class="px-4 py-3">
        <button onclick="hapusData(${row.id_kehadiran})" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
      </td>
    </tr>
  `).join('');
}

async function hapusData(id) {
  if (!confirm('Hapus data kehadiran ini?')) return;
  const res = await fetch(`${API}?action=hapus&id=${id}`);
  const json = await res.json();
  if (json.status === 'success') {
    loadSummary();
    loadTable();
  } else {
    alert('Gagal menghapus data');
  }
}

document.getElementById('form-kehadiran').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const payload = {
    jenis: 'guru',
    nama_guru: form.nama_guru.value,
    jam_masuk: form.jam_masuk.value || null,
    status: form.status.value,
    keterangan: form.keterangan.value || null,
    tanggal: new Date().toISOString().slice(0, 10),
  };

  const res = await fetch(`${API}?action=tambah`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });
  const json = await res.json();

  if (json.status === 'success') {
    closeModal();
    loadSummary();
    loadTable();
  } else {
    alert('Gagal menyimpan data: ' + (json.message || ''));
  }
});

loadSummary();
loadTable();
</script>

</body>
</html>