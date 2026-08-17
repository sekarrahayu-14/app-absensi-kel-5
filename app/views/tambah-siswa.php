<?php
require_once __DIR__ . '/../../config/Auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Siswa - Absensi Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

    <!-- Header Navigation -->
    <header class="bg-indigo-600 text-white shadow-lg">
        <div class="max-w-4xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-user-plus text-2xl"></i>
                <h1 class="text-xl font-bold tracking-wide">Tambah Siswa Baru</h1>
            </div>
            <a href="index.php" class="bg-indigo-700 hover:bg-indigo-800 text-xs px-3 py-2 rounded-lg font-medium transition duration-200 flex items-center space-x-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Absensi</span>
            </a>
        </div>
    </header>

    <main class="max-w-xl mx-auto w-full px-6 py-10 flex-grow">

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-2">Formulir Data Siswa</h2>
            <p class="text-sm text-slate-500 mb-6">Lengkapi data siswa di bawah ini untuk didaftarkan ke sistem.</p>

            <!-- Alert Message Container -->
            <div id="alertBox" class="hidden mb-6 p-4 rounded-xl text-sm font-medium"></div>

            <form id="formSiswa" class="space-y-5">
                <!-- NIS -->
                <div>
                    <label for="nis" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">NIS (Nomor Induk Siswa)</label>
                    <input type="text" id="nis" name="nis" required placeholder="Contoh: 1001"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <!-- Nama Siswa -->
                <div>
                    <label for="nama" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required placeholder="Masukkan nama siswa"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <!-- Kelas -->
                <div>
                    <label for="kelas" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Kelas</label>
                    <select id="kelas" name="kelas" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white">
                        <option value="">Pilih Kelas</option>
                        <option value="X">X (Sepuluh)</option>
                        <option value="XI">XI (Sebelas)</option>
                        <option value="XII">XII (Dua Belas)</option>
                    </select>
                </div>

                <div>
                    <label for="status_kehadiran" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Status Kehadiran</label>
                    <select id="status_kehadiran" name="status_kehadiran" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition bg-white">
                        <option value="h">Hadir</option>
                        <option value="a">Alpha</option>
                        <option value="s">Sakit</option>
                        <option value="i">Izin</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" id="btnSubmit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition duration-200 shadow-sm flex justify-center items-center space-x-2">
                        <i class="fa-solid fa-save"></i>
                        <span>Simpan Data Siswa</span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('formSiswa').addEventListener('submit', async function (e) {
            e.preventDefault();

            const btnSubmit = document.getElementById('btnSubmit');
            const alertBox = document.getElementById('alertBox');

            // Ambil data form — key harus sama persis dengan yang dibaca siswa.php
            const formData = {
                nis: document.getElementById('nis').value,
                nama: document.getElementById('nama').value,
                kelas: document.getElementById('kelas').value
                ,status_kehadiran: document.getElementById('status_kehadiran').value
            };

            // Loading State
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<i class="fa-solid fa-spinner animate-spin"></i> <span>Menyimpan...</span>`;

            try {
                // Kirim request ke Endpoint API Siswa
                const response = await fetch('../../api/siswa.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                alertBox.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'bg-rose-50', 'text-rose-700');

                // Cek apakah response berhasil (status 200-299) dan status field adalah 'success'
                if ((response.status === 200 || response.status === 201) && result.status === 'success') {
                    // Sukses
                    alertBox.classList.add('bg-emerald-50', 'text-emerald-700');
                    alertBox.innerHTML = `<i class="fa-solid fa-circle-check mr-2"></i> ${result.message}`;

                    // Reset Form
                    document.getElementById('formSiswa').reset();

                    // Balik ke dashboard supaya siswa baru langsung kelihatan di tabel
                    setTimeout(() => { window.location.href = 'index.php'; }, 900);
                } else {
                    // Gagal dari sistem (misalnya NIS duplikat, field kosong, dsb.)
                    alertBox.classList.add('bg-rose-50', 'text-rose-700');
                    alertBox.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-2"></i> ${result.message || 'Gagal menyimpan data.'}`;
                }
            } catch (error) {
                // Error Jaringan/Koneksi
                console.error('Error:', error);
                alertBox.classList.remove('hidden');
                alertBox.classList.add('bg-rose-50', 'text-rose-700');
                alertBox.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-2"></i> Gagal menghubungi server API.`;
            } finally {
                // Kembalikan tombol ke keadaan semula
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<i class="fa-solid fa-save"></i> <span>Simpan Data Siswa</span>`;
            }
        });
    </script>
</body>
</html>