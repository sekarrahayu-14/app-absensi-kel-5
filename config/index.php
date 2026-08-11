<?php
// Data contoh. Nantinya dapat diganti dengan data dari database.
$daftarSiswa = [
    ['nis' => '24001', 'nama' => 'Ahmad Fauzan',  'kelas' => 'XII RPL 1'],
    ['nis' => '24002', 'nama' => 'Aisyah Putri',  'kelas' => 'XII RPL 1'],
    ['nis' => '24003', 'nama' => 'Budi Santoso',  'kelas' => 'XII RPL 1'],
    ['nis' => '24004', 'nama' => 'Citra Lestari', 'kelas' => 'XII RPL 1'],
    ['nis' => '24005', 'nama' => 'Dimas Saputra', 'kelas' => 'XII RPL 1'],
    ['nis' => '24006', 'nama' => 'Eka Rahmawati', 'kelas' => 'XII RPL 1'],
];

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$kelas   = $_GET['kelas'] ?? 'XII RPL 1';

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $kelas   = $_POST['kelas'] ?? 'XII RPL 1';
    $absensi = $_POST['status'] ?? [];

    /*
     * Proses penyimpanan ke database dapat ditambahkan di sini.
     * Contoh isi $absensi:
     * [
     *     '24001' => 'Hadir',
     *     '24002' => 'Sakit'
     * ]
     */

    $jumlahTersimpan = count($absensi);
    $pesan = "Absensi berhasil diproses untuk {$jumlahTersimpan} siswa.";
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function inisial(string $nama): string
{
    $bagian = preg_split('/\s+/', trim($nama));
    $hasil = '';

    foreach (array_slice($bagian, 0, 2) as $kata) {
        $hasil .= strtoupper(substr($kata, 0, 1));
    }

    return $hasil;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buku Absensi Siswa</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@500;700;900&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  :root{
    --ink:#1E2A3A;
    --ink-soft:#33465C;
    --paper:#F1EBD9;
    --paper-deep:#E7DFC7;
    --card:#FBF8EF;
    --rule:#D9CFAF;
    --margin-red:#AD4038;
    --stamp-green:#2F5233;
    --stamp-gold:#A6772E;
    --stamp-blue:#33587A;
    --stamp-red:#AD4038;
  }
  *{ font-family:'Inter',sans-serif; }
  body{
    background-color:var(--paper);
    background-image:
      repeating-linear-gradient(180deg, transparent, transparent 34px, rgba(30,42,58,0.05) 35px);
  }
  .font-display{ font-family:'Roboto Slab',serif; }
  .font-mono{ font-family:'JetBrains Mono',monospace; }

  .eyebrow{
    font-family:'JetBrains Mono',monospace;
    letter-spacing:.18em;
    text-transform:uppercase;
    font-size:11px;
  }

  .date-stamp{
    border:2px solid var(--ink);
    border-radius:6px;
    transform:rotate(2deg);
    color:var(--ink);
    font-family:'JetBrains Mono',monospace;
    letter-spacing:.08em;
  }

  .tally-card{
    background:var(--card);
    border:1px solid var(--rule);
    box-shadow:3px 3px 0 rgba(30,42,58,0.06);
    position:relative;
  }
  .tally-card::before{
    content:"";
    position:absolute; left:0; top:12px; bottom:12px; width:3px;
    background:var(--tally-color,var(--ink));
    border-radius:2px;
  }

  .stamp{
    display:inline-flex;
    align-items:center;
    gap:.35em;
    font-family:'JetBrains Mono',monospace;
    text-transform:uppercase;
    letter-spacing:.06em;
    font-size:11px;
    font-weight:700;
    padding:3px 10px 3px 8px;
    border:1.5px solid currentColor;
    border-radius:3px;
    transform:rotate(-1.5deg);
    background:color-mix(in srgb, currentColor 8%, transparent);
  }
  .stamp::before{
    content:"";
    width:5px; height:5px; border-radius:50%;
    background:currentColor;
  }
  .stamp-hadir{ color:var(--stamp-green); }
  .stamp-izin{ color:var(--stamp-gold); }
  .stamp-sakit{ color:var(--stamp-blue); }
  .stamp-alpa{ color:var(--stamp-red); }

  .ledger{
    background:var(--card);
    border:1px solid var(--rule);
    position:relative;
    padding-left:34px;
  }
  .ledger::before{
    content:"";
    position:absolute; left:26px; top:0; bottom:0; width:2px;
    background:var(--margin-red);
    opacity:.55;
  }
  .ledger table{ border-collapse:collapse; }
  .ledger thead th{
    font-family:'Roboto Slab',serif;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.06em;
    font-size:12px;
    color:var(--paper);
    background:var(--ink);
  }
  .ledger tbody tr{ border-bottom:1px dashed var(--rule); }
  .ledger tbody tr:hover{ background:rgba(173,64,56,0.05); }
  .ledger td, .ledger th{ padding:12px 16px; }

  .btn-stamp{
    font-family:'Roboto Slab',serif;
    font-weight:700;
    letter-spacing:.02em;
    background:var(--ink);
    color:var(--paper);
    border:1.5px solid var(--ink);
    transition:transform .12s ease, background .12s ease;
  }
  .btn-stamp:hover{ background:var(--ink-soft); transform:translateY(-1px); }

  .tab-pill{
    font-family:'JetBrains Mono',monospace;
    font-size:12px;
    letter-spacing:.05em;
    text-transform:uppercase;
  }
  .tab-pill.active{
    background:var(--ink);
    color:var(--paper);
  }

  .card-modal{
    background:var(--card);
    border:1px solid var(--rule);
    position:relative;
    padding-left:30px;
  }
  .card-modal::before{
    content:"";
    position:absolute; left:22px; top:0; bottom:0; width:2px;
    background:var(--margin-red);
    opacity:.55;
  }

  .field-input{
    background:var(--paper);
    border:1px solid var(--rule);
  }
  .field-input:focus{
    outline:none;
    border-color:var(--ink);
    box-shadow:0 0 0 3px rgba(30,42,58,0.1);
  }

  .link-hapus{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;
    letter-spacing:.03em;
    text-decoration:underline;
    text-decoration-style:dotted;
    text-underline-offset:3px;
  }

  .class-chip{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;
    font-weight:700;
    letter-spacing:.04em;
    background:var(--paper-deep);
    color:var(--ink-soft);
    padding:2px 8px;
    border-radius:3px;
  }

  @media (prefers-reduced-motion: reduce){
    .btn-stamp{ transition:none; }
  }
</style>
</head>
<body class="min-h-screen text-[var(--ink)]">

  <!-- Header band -->
  <header style="background:var(--ink)" class="px-6 py-5">
    <div class="max-w-6xl mx-auto flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-3">
        <span class="text-2xl">📖</span>
        <h1 class="font-display text-xl text-[var(--paper)] tracking-wide">Buku Absensi Siswa</h1>
      </div>
      <nav class="flex gap-1 bg-white/10 rounded-full p-1">
        <a href="dashboard.php" class="tab-pill active px-4 py-2 rounded-full transition">Absensi Siswa</a>
        <a href="kehadiran_guru.php" class="tab-pill px-4 py-2 rounded-full text-[var(--paper)]/70 hover:text-[var(--paper)] transition">Kehadiran Guru</a>
      </nav>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-10">

    <!-- Eyebrow + date stamp + class filter -->
    <div class="flex items-end justify-between flex-wrap gap-4 mb-8">
      <div>
        <p class="eyebrow text-[var(--margin-red)] mb-2">Register Harian &middot; Ruang Kelas</p>
        <h2 class="font-display text-3xl md:text-4xl">Absensi hari ini</h2>
      </div>
      <div class="flex items-center gap-3">
        <select id="filter-kelas" class="field-input rounded-md px-3 py-2 text-sm font-mono">
          <option value="">Semua Kelas</option>
        </select>
        <div class="date-stamp px-4 py-2 text-sm" id="date-stamp">—</div>
      </div>
    </div>

    <!-- Tally cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
      <div class="tally-card rounded-lg p-5 pl-6" style="--tally-color:var(--stamp-green)">
        <p class="eyebrow text-[var(--ink-soft)]">Hadir</p>
        <p class="font-mono font-bold text-4xl mt-2" style="color:var(--stamp-green)" id="count-hadir">–</p>
      </div>
      <div class="tally-card rounded-lg p-5 pl-6" style="--tally-color:var(--stamp-gold)">
        <p class="eyebrow text-[var(--ink-soft)]">Izin</p>
        <p class="font-mono font-bold text-4xl mt-2" style="color:var(--stamp-gold)" id="count-izin">–</p>
      </div>
      <div class="tally-card rounded-lg p-5 pl-6" style="--tally-color:var(--stamp-blue)">
        <p class="eyebrow text-[var(--ink-soft)]">Sakit</p>
        <p class="font-mono font-bold text-4xl mt-2" style="color:var(--stamp-blue)" id="count-sakit">–</p>
      </div>
      <div class="tally-card rounded-lg p-5 pl-6" style="--tally-color:var(--stamp-red)">
        <p class="eyebrow text-[var(--ink-soft)]">Alpa</p>
        <p class="font-mono font-bold text-4xl mt-2" style="color:var(--stamp-red)" id="count-alpa">–</p>
      </div>
    </div>

    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
      <h3 class="font-display text-lg">Daftar Absensi Siswa</h3>
      <button id="btn-open-modal" class="btn-stamp text-sm px-5 py-2.5 rounded-md">
        + Catat Absensi
      </button>
    </div>

    <!-- Ledger table -->
    <div class="ledger rounded-lg overflow-hidden">
      <table class="w-full text-sm text-left">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Status</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="table-body">
          <tr><td colspan="6" class="text-center py-8 italic text-[var(--ink-soft)]/70">Memuat catatan…</td></tr>
        </tbody>
      </table>
    </div>

  </main>

  <!-- Modal -->
  <div id="modal" class="fixed inset-0 bg-[var(--ink)]/50 hidden items-center justify-center px-4 z-10">
    <div class="card-modal rounded-lg shadow-xl w-full max-w-md p-6">
      <p class="eyebrow text-[var(--margin-red)] mb-1">Entri Baru</p>
      <h3 class="font-display text-xl mb-5">Catat Absensi Siswa</h3>
      <form id="form-absensi" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide text-[var(--ink-soft)] mb-1">Nama Siswa</label>
          <input type="text" name="nama_siswa" required
            class="field-input w-full rounded-md px-3 py-2 text-sm">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-[var(--ink-soft)] mb-1">Kelas</label>
            <input type="text" name="kelas" required placeholder="cth. 7A"
              class="field-input w-full rounded-md px-3 py-2 text-sm font-mono">
          </div>
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-[var(--ink-soft)] mb-1">Status</label>
            <select name="status" required
              class="field-input w-full rounded-md px-3 py-2 text-sm">
              <option value="Hadir">Hadir</option>
              <option value="Izin">Izin</option>
              <option value="Sakit">Sakit</option>
              <option value="Alpa">Alpa</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide text-[var(--ink-soft)] mb-1">Keterangan</label>
          <textarea name="keterangan" rows="2"
            class="field-input w-full rounded-md px-3 py-2 text-sm"></textarea>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" id="btn-close-modal" class="px-4 py-2 text-sm text-[var(--ink-soft)] hover:text-[var(--ink)]">Batal</button>
          <button type="submit" class="btn-stamp px-4 py-2 text-sm rounded-md">Simpan</button>
        </div>
      </form>
    </div>
  </div>

<script>
const API = 'JsonViews.php';
const modal = document.getElementById('modal');
let allRows = [];

const today = new Date();
document.getElementById('date-stamp').textContent = today.toLocaleDateString('id-ID', {
  weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
}).toUpperCase();

document.getElementById('btn-open-modal').addEventListener('click', () => {
  modal.classList.remove('hidden');
  modal.classList.add('flex');
});
document.getElementById('btn-close-modal').addEventListener('click', closeModal);
function closeModal() {
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  document.getElementById('form-absensi').reset();
}

function badgeStatus(status) {
  const map = {
    'Hadir': 'stamp-hadir',
    'Izin': 'stamp-izin',
    'Sakit': 'stamp-sakit',
    'Alpa': 'stamp-alpa',
  };
  const cls = map[status] || 'stamp-hadir';
  return `<span class="stamp ${cls}">${status}</span>`;
}

async function loadSummary(kelas = '') {
  const q = kelas ? `&kelas=${encodeURIComponent(kelas)}` : '';
  const res = await fetch(`${API}?action=summary&jenis=siswa${q}`);
  const json = await res.json();
  if (json.status === 'success') {
    document.getElementById('count-hadir').textContent = json.data.Hadir ?? 0;
    document.getElementById('count-izin').textContent = json.data.Izin ?? 0;
    document.getElementById('count-sakit').textContent = json.data.Sakit ?? 0;
    document.getElementById('count-alpa').textContent = json.data.Alpa ?? 0;
  }
}

function renderRows(rows) {
  const tbody = document.getElementById('table-body');
  if (rows.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 italic text-[var(--ink-soft)]/70">— belum ada catatan hari ini —</td></tr>`;
    return;
  }
  tbody.innerHTML = rows.map(row => `
    <tr>
      <td class="font-mono text-[13px]">${row.tanggal ?? '-'}</td>
      <td class="font-medium">${row.nama_siswa ?? '-'}</td>
      <td><span class="class-chip">${row.kelas ?? '-'}</span></td>
      <td>${badgeStatus(row.status)}</td>
      <td class="text-[var(--ink-soft)]">${row.keterangan ?? '-'}</td>
      <td>
        <button onclick="hapusData(${row.id_absensi})" class="link-hapus" style="color:var(--margin-red)">Hapus</button>
      </td>
    </tr>
  `).join('');
}

function populateKelasFilter(rows) {
  const select = document.getElementById('filter-kelas');
  const current = select.value;
  const kelasList = [...new Set(rows.map(r => r.kelas).filter(Boolean))].sort();
  select.innerHTML = `<option value="">Semua Kelas</option>` +
    kelasList.map(k => `<option value="${k}">${k}</option>`).join('');
  select.value = current;
}

async function loadTable() {
  const tbody = document.getElementById('table-body');
  tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 italic text-[var(--ink-soft)]/70">Memuat catatan…</td></tr>`;

  const tanggal = today.toISOString().slice(0, 10);
  const res = await fetch(`${API}?action=list&jenis=siswa&tanggal=${tanggal}`);
  const json = await res.json();

  if (json.status !== 'success') {
    renderRows([]);
    return;
  }

  allRows = json.data || [];
  populateKelasFilter(allRows);
  applyFilter();
}

function applyFilter() {
  const kelas = document.getElementById('filter-kelas').value;
  const filtered = kelas ? allRows.filter(r => r.kelas === kelas) : allRows;
  renderRows(filtered);
}

document.getElementById('filter-kelas').addEventListener('change', () => {
  applyFilter();
  loadSummary(document.getElementById('filter-kelas').value);
});

async function hapusData(id) {
  if (!confirm('Hapus data absensi ini?')) return;
  const res = await fetch(`${API}?action=hapus&id=${id}`);
  const json = await res.json();
  if (json.status === 'success') {
    loadSummary(document.getElementById('filter-kelas').value);
    loadTable();
  } else {
    alert('Gagal menghapus data');
  }
}

document.getElementById('form-absensi').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const payload = {
    jenis: 'siswa',
    nama_siswa: form.nama_siswa.value,
    kelas: form.kelas.value,
    status: form.status.value,
    keterangan: form.keterangan.value || null,
    tanggal: today.toISOString().slice(0, 10),
  };

  const res = await fetch(`${API}?action=tambah`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });
  const json = await res.json();

  if (json.status === 'success') {
    closeModal();
    loadSummary(document.getElementById('filter-kelas').value);
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