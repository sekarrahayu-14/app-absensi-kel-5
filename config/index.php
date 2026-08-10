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
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Absensi Siswa</title>

    <link
        href="[cdn.jsdelivr.net](https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css)"
        rel="stylesheet"
    >

    <link
        href="[cdn.jsdelivr.net](https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css)"
        rel="stylesheet"
    >

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --sidebar-width: 260px;
            --body-bg: #f4f7fb;
        }

        body {
            min-height: 100vh;
            background: var(--body-bg);
            color: #1f2937;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1030;
            width: var(--sidebar-width);
            padding: 24px 16px;
            overflow-y: auto;
            color: white;
            background: linear-gradient(
                160deg,
                var(--primary-color),
                var(--primary-dark)
            );
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
            padding: 0 12px;
        }

        .brand-icon {
            display: grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            font-size: 22px;
            background: rgba(255, 255, 255, 0.18);
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
            padding: 12px 14px;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.8);
            transition: 0.2s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.16);
        }

        .main-content {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            padding: 28px;
        }

        .mobile-header {
            display: none;
        }

        .page-title {
            font-size: 1.65rem;
            font-weight: 700;
        }

        .card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.06);
        }

        .summary-card {
            position: relative;
            overflow: hidden;
        }

        .summary-icon {
            display: grid;
            place-items: center;
            width: 50px;
            height: 50px;
            border-radius: 15px;
            font-size: 22px;
        }

        .bg-soft-primary {
            color: #4338ca;
            background: #e0e7ff;
        }

        .bg-soft-success {
            color: #15803d;
            background: #dcfce7;
        }

        .bg-soft-warning {
            color: #a16207;
            background: #fef3c7;
        }

        .bg-soft-danger {
            color: #b91c1c;
            background: #fee2e2;
        }

        .student-avatar {
            display: grid;
            flex-shrink: 0;
            place-items: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            color: var(--primary-color);
            font-weight: 700;
            background: #e0e7ff;
        }

        .status-group {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .status-group .btn {
            min-width: 74px;
            border-radius: 10px;
        }

        .table > :not(caption) > * > * {
            padding: 14px 12px;
            vertical-align: middle;
        }

        .table thead th {
            white-space: nowrap;
            color: #64748b;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            background: #f8fafc;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            top: 50%;
            left: 14px;
            color: #94a3b8;
            transform: translateY(-50%);
        }

        .search-box input {
            padding-left: 40px;
        }

        .btn-primary {
            border-color: var(--primary-color);
            background: var(--primary-color);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            border-color: var(--primary-dark);
            background: var(--primary-dark);
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                z-index: 1020;
                display: none;
                background: rgba(15, 23, 42, 0.55);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 20px 16px;
            }

            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 22px;
            }
        }

        @media (max-width: 767.98px) {
            .page-title {
                font-size: 1.35rem;
            }

            .attendance-table thead {
                display: none;
            }

            .attendance-table,
            .attendance-table tbody,
            .attendance-table tr,
            .attendance-table td {
                display: block;
                width: 100%;
            }

            .attendance-table tbody {
                display: grid;
                gap: 14px;
            }

            .attendance-table tr {
                padding: 16px;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                background: white;
            }

            .attendance-table td {
                padding: 5px 0;
                border: 0;
            }

            .attendance-table td:first-child {
                display: none;
            }

            .status-group {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                margin-top: 10px;
            }

            .status-group .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>

            <div>
                <div class="fw-bold fs-5">EduHadir</div>
                <small class="text-white-50">Sistem Absensi</small>
            </div>
        </div>

        <nav class="nav flex-column">
            <a href="#" class="nav-link">
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>

            <a href="#" class="nav-link active">
                <i class="bi bi-calendar2-check-fill"></i>
                Absensi Siswa
            </a>

            <a href="#" class="nav-link">
                <i class="bi bi-people-fill"></i>
                Data Siswa
            </a>

            <a href="#" class="nav-link">
                <i class="bi bi-journal-text"></i>
                Rekap Absensi
            </a>

            <a href="#" class="nav-link">
                <i class="bi bi-gear-fill"></i>
                Pengaturan
            </a>
        </nav>

        <div class="position-absolute bottom-0 start-0 w-100 p-3">
            <div class="rounded-4 p-3 bg-white bg-opacity-10">
                <small class="d-block text-white-50">Masuk sebagai</small>
                <strong>Administrator</strong>
            </div>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <div class="mobile-header">
            <button
                type="button"
                class="btn btn-light shadow-sm"
                id="menuButton"
                aria-label="Buka menu"
            >
                <i class="bi bi-list fs-4"></i>
            </button>

            <span class="fw-bold text-primary">EduHadir</span>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="page-title mb-1">Absensi Siswa</h1>
                <p class="text-secondary mb-0">
                    Kelola kehadiran siswa dengan mudah.
                </p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-semibold">Admin Sekolah</div>
                    <small class="text-secondary">
                        <?= escape(date('d M Y')); ?>
                    </small>
                </div>

                <div class="student-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>
        </div>

        <?php if ($pesan !== ''): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= escape($pesan); ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup"
                ></button>
            </div>
        <?php endif; ?>

        <section class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="summary-icon bg-soft-primary">
                            <i class="bi bi-people-fill"></i>
                        </div>

                        <div>
                            <small class="text-secondary">Total Siswa</small>
                            <h3 class="mb-0" id="totalCount">
                                <?= count($daftarSiswa); ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="summary-icon bg-soft-success">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>

                        <div>
                            <small class="text-secondary">Hadir</small>
                            <h3 class="mb-0" id="hadirCount">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="summary-icon bg-soft-warning">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>

                        <div>
                            <small class="text-secondary">Izin/Sakit</small>
                            <h3 class="mb-0" id="izinSakitCount">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="summary-icon bg-soft-danger">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>

                        <div>
                            <small class="text-secondary">Alpa</small>
                            <h3 class="mb-0" id="alpaCount">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="row g-3 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="kelas" class="form-label fw-semibold">
                            Kelas
                        </label>

                        <select class="form-select" id="kelas" name="kelas">
                            <?php
                            $daftarKelas = [
                                'X RPL 1',
                                'XI RPL 1',
                                'XII RPL 1',
                                'XII RPL 2'
                            ];
                            ?>

                            <?php foreach ($daftarKelas as $itemKelas): ?>
                                <option
                                    value="<?= escape($itemKelas); ?>"
                                    <?= $kelas === $itemKelas ? 'selected' : ''; ?>
                                >
                                    <?= escape($itemKelas); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tanggal" class="form-label fw-semibold">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="tanggal"
                            name="tanggal"
                            value="<?= escape($tanggal); ?>"
                        >
                    </div>

                    <div class="col-12 col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <form method="post" id="attendanceForm">
            <input
                type="hidden"
                name="kelas"
                value="<?= escape($kelas); ?>"
            >

            <input
                type="hidden"
                name="tanggal"
                value="<?= escape($tanggal); ?>"
            >

            <div class="card">
                <div class="card-header border-0 bg-white p-3 p-md-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-lg-5">
                            <h2 class="h5 fw-bold mb-1">
                                Daftar Kehadiran
                            </h2>

                            <small class="text-secondary">
                                <?= escape($kelas); ?>
                                -
                                <?= escape(date('d-m-Y', strtotime($tanggal))); ?>
                            </small>
                        </div>

                        <div class="col-12 col-sm-7 col-lg-4">
                            <div class="search-box">
                                <i class="bi bi-search"></i>

                                <input
                                    type="search"
                                    class="form-control"
                                    id="studentSearch"
                                    placeholder="Cari nama atau NIS..."
                                >
                            </div>
                        </div>

                        <div class="col-12 col-sm-5 col-lg-3">
                            <button
                                type="button"
                                class="btn btn-outline-success w-100"
                                id="markAllPresent"
                            >
                                <i class="bi bi-check2-all me-2"></i>
                                Semua Hadir
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4 pt-0">
                    <div class="table-responsive">
                        <table class="table attendance-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">No.</th>
                                    <th>Data Siswa</th>
                                    <th>NIS</th>
                                    <th style="min-width: 350px;">Status Kehadiran</th>
                                </tr>
                            </thead>

                            <tbody id="studentTableBody">
                                <?php foreach ($daftarSiswa as $index => $siswa): ?>
                                    <tr
                                        class="student-row"
                                        data-search="<?= escape(
                                            strtolower(
                                                $siswa['nama'] . ' ' . $siswa['nis']
                                            )
                                        ); ?>"
                                    >
                                        <td><?= $index + 1; ?></td>

                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="student-avatar">
                                                    <?= escape(inisial($siswa['nama'])); ?>
                                                </div>

                                                <div>
                                                    <div class="fw-semibold">
                                                        <?= escape($siswa['nama']); ?>
                                                    </div>

                                                    <small class="text-secondary">
                                                        <?= escape($siswa['kelas']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="badge text-bg-light border px-3 py-2">
                                                <?= escape($siswa['nis']); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <div
                                                class="status-group"
                                                role="group"
                                                aria-label="Status <?= escape($siswa['nama']); ?>"
                                            >
                                                <?php
                                                $statusList = [
                                                    'Hadir' => 'success',
                                                    'Izin'  => 'primary',
                                                    'Sakit' => 'warning',
                                                    'Alpa'  => 'danger',
                                                ];
                                                ?>

                                                <?php foreach ($statusList as $status => $warna): ?>
                                                    <?php
                                                    $idStatus = $siswa['nis'] . '-' . strtolower($status);
                                                    ?>

                                                    <input
                                                        type="radio"
                                                        class="btn-check attendance-radio"
                                                        name="status[<?= escape($siswa['nis']); ?>]"
                                                        id="<?= escape($idStatus); ?>"
                                                        value="<?= escape($status); ?>"
                                                        autocomplete="off"
                                                        <?= $status === 'Hadir' ? 'checked' : ''; ?>
                                                    >

                                                    <label
                                                        class="btn btn-outline-<?= escape($warna); ?> btn-sm"
                                                        for="<?= escape($idStatus); ?>"
                                                    >
                                                        <?= escape($status); ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="text-center text-secondary py-5 d-none"
                        id="emptyState"
                    >
                        <i class="bi bi-search fs-1 d-block mb-2"></i>
                        Siswa tidak ditemukan.
                    </div>
                </div>

                <div class="card-footer border-0 bg-white p-3 p-md-4">
                    <div
                        class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3"
                    >
                        <small class="text-secondary">
                            Pastikan seluruh status siswa sudah benar.
                        </small>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-floppy-fill me-2"></i>
                            Simpan Absensi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <script
        src="[cdn.jsdelivr.net](https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js)"
    ></script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const menuButton = document.getElementById('menuButton');
        const searchInput = document.getElementById('studentSearch');
        const emptyState = document.getElementById('emptyState');
        const markAllButton = document.getElementById('markAllPresent');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        }

        menuButton.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);

        function updateSummary() {
            const selectedStatuses = document.querySelectorAll(
                '.attendance-radio:checked'
            );

            let hadir = 0;
            let izinSakit = 0;
            let alpa = 0;

            selectedStatuses.forEach((radio) => {
                if (radio.value === 'Hadir') {
                    hadir++;
                }

                if (radio.value === 'Izin' || radio.value === 'Sakit') {
                    izinSakit++;
                }

                if (radio.value === 'Alpa') {
                    alpa++;
                }
            });

            document.getElementById('hadirCount').textContent = hadir;
            document.getElementById('izinSakitCount').textContent = izinSakit;
            document.getElementById('alpaCount').textContent = alpa;
        }

        document.querySelectorAll('.attendance-radio').forEach((radio) => {
            radio.addEventListener('change', updateSummary);
        });

        markAllButton.addEventListener('click', () => {
            document.querySelectorAll('.student-row').forEach((row) => {
                if (row.style.display !== 'none') {
                    const hadirRadio = row.querySelector(
                        '.attendance-radio[value="Hadir"]'
                    );

                    if (hadirRadio) {
                        hadirRadio.checked = true;
                    }
                }
            });

            updateSummary();
        });

        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.student-row');
            let visibleRows = 0;

            rows.forEach((row) => {
                const content = row.dataset.search;
                const isVisible = content.includes(keyword);

                row.style.display = isVisible ? '' : 'none';

                if (isVisible) {
                    visibleRows++;
                }
            });

            emptyState.classList.toggle('d-none', visibleRows !== 0);
        });

        updateSummary();
    </script>
</body>
</html>