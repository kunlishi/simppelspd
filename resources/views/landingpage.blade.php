<x-layout></x-layout>

<style>
    /* CSS untuk animasi float up */
    span {
        color: #9f2a32;
        font-weight: bold;
    }

    h3 {
        color: #2a3f64;
    }

    h4 {
        color: rgb(42, 42, 42);
    }

    @keyframes floatUp {
        0% {
            transform: translateY(20px);
            /* Mulai dari posisi bawah */
            opacity: 0;
            /* Mulai dengan transparan */
        }

        100% {
            transform: translateY(0);
            /* Posisi akhir */
            opacity: 1;
            /* Menjadi sepenuhnya terlihat */
        }
    }

    .float-up-text {
        animation: floatUp 0.3s ease-out forwards;
        /* Durasi lebih cepat untuk teks */
    }

    .float-up-image {
        animation: floatUp 0.5s ease-out forwards;
        /* Durasi lebih lambat untuk gambar */
    }

    .custom-alert {
        position: fixed;
        top: 40px;
        left: 50%;
        /* Posisi horizontal di tengah */
        transform: translateX(-50%);
        /* Pindahkan setengah dari lebar elemen */
        z-index: 1050;
        /* Pastikan di atas elemen lain */
        padding: 15px;
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
        border-radius: 5px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        max-width: 90%;
        /* Sesuaikan dengan lebar layar, maksimal 90% */
        text-align: center;
        box-sizing: border-box;
        /* Pastikan padding dihitung dalam lebar elemen */
    }
</style>

<div class="ml-4 mr-4">
    <div class="p-4 sm:ml-64 mt-7">
        <h1 class="text-5xl text-center font-bold tracking-tight mb-4 float-up-text">Selamat datang di <span>SIMPPEL
                SPD</span>!</h1>
        <h3 class="text-3xl text-center font-bold tracking-tight mb-4 float-up-text">Sistem Pencatatan dan Pelaporan
            Pelanggaran SPD
        </h3>
        <br>

        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div id="successAlert" class="custom-alert">
                {{ session('success') }}
            </div>
        @endif

        <script>
            // Menghilangkan pesan setelah 3 detik
            setTimeout(() => {
                const alertBox = document.getElementById('successAlert');
                if (alertBox) {
                    alertBox.style.opacity = '0';
                    setTimeout(() => alertBox.remove(), 500); // Hapus elemen setelah animasi
                }
            }, 3000);
        </script>

        <h4 class="text-2xl font-bold tracking-tight mb-6 float-up-text" style="text-align: center;">Kenalan dengan SPD
        </h4>
        <div class="flex justify-center space-x-8 mb-4 w-full">
            <!-- Kartu 1 -->
            <div class="w-1/2 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <img class="rounded-t-lg" src="img/banner1.jpg" alt="" />

                <div class="p-5">
                    <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white"
                        style="color:#9f2a32">
                        Visi & Misi SPD
                    </h5>
                    <br>
                    <h5 class="mb-2 text-xl font-semibold tracking-tight text-gray-900 dark:text-white"
                        style="color:#2a3f64">
                        Visi
                    </h5>
                    <p class="text-gray-500 dark:text-gray-400 mb-5" style="text-align: justify;">
                        Menjalankan organisasi kemahasiswaan yang sinergis, konsisten, dan berwibawa sebagai role model
                        mahasiswa Politeknik Statistika STIS.
                    </p>
                    <h5 class="mb-2 text-xl font-semibold tracking-tight text-gray-900 dark:text-white"
                        style="color:#2a3f64">
                        Misi
                    </h5>
                    <ul class="text-gray-500 dark:text-gray-400 pl-5"
                        style="list-style-type: disc; text-align: justify;">
                        <li>Kedisiplinan bagi seluruh mahasiswa</li>
                        <li>Memberikan pemahaman peraturan kemahasiswaan kepada seluruh mahasiswa</li>
                        <li>Menjalin kerjasama dengan organisasi lain dalam program kerja yang tersusun rapi dan
                            terencana</li>
                        <li>Menanamkan sistem jangka panjang sebagai dasar penegakan kedisiplinan dan proses pembaharuan
                            mentalitas mahasiswa</li>
                    </ul>

                </div>
            </div>

            <!-- Kartu 2 -->
            <div class="w-1/2 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <img class="rounded-t-lg" src="img/banner2.jpg" alt="" />

                <div class="p-5">
                    <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white"
                        style="color:#9f2a32">
                        Panca Prasetya Satuan
                    </h5>
                    <br>
                    <p class="text-gray-500 dark:text-gray-400 mb-5" style="text-align: justify;">
                        Kami Satuan Penegak Disiplin Politeknik Statistika STIS:
                    </p>
                    <ol class="text-gray-500 dark:text-gray-400 pl-5"
                        style="list-style-type: decimal; text-align: justify;">
                        <li>Selalu setia dan taat sepenuhnya kepada Pancasila, UUD 1945, Janji Mahasiswa Politeknik
                            Statistika STIS, dan Panca Prasetya Satuan;</li>
                        <li>Selalu siap sedia mengiringi jalannya peraturan untuk tetap ditegakkan;</li>
                        <li>Menjunjung tinggi kehormatan, kewibawaan, dan martabat sebagai seorang mahasiswa kedinasan;
                        </li>
                        <li>Berperilaku arif dan bijaksana serta menjadi sosok yang tegas dan ksatria; dan</li>
                        <li>Menjalankan tugas dan perintah dengan penuh jiwa pengabdian.</li>
                    </ol>

                </div>
            </div>
        </div>



        <br><br>

        <h4 class="text-2xl font-bold tracking-tight mb-6 float-up-text" style="text-align: center;">Urusan-urusan dalam
            SPD</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1 -->
            <div
                class="max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <img class="rounded-t-lg" src="img/ikonurusan/Ikon DIKPER.png" />
                <div class="p-5">
                    <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white"
                        style="color:#9f2a32">DIKPER</h5>
                    <h5 class="mb-2 text-xl font-semibold tracking-tight text-gray-900 dark:text-white"
                        style="color:#2a3f64">Pendidikan, Pelatihan, dan Personalia</h5>
                    <br>
                    <hr><br>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Tugas DIKPER mencakup penyusunan,
                        perancangan, dan pelaksanaan program pendidikan serta pelatihan bagi Calon Anggota Satuan
                        Penegak Disiplin, termasuk pengawasan kegiatan, koordinasi antar pihak terkait, serta penyusunan
                        penilaian dan kegiatan internal bagi anggota satuan.</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div
                class="max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <img class="rounded-t-lg" src="img/ikonurusan/Ikon AKLH.png" />
                <div class="p-5">
                    <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white"
                        style="color:#9f2a32">AKLH</h5>
                    <h5 class="mb-2 text-xl font-semibold tracking-tight text-gray-900 dark:text-white"
                        style="color:#2a3f64">Administrasi, Keuangan, Logistik, dan Humas</h5>
                    <br>
                    <hr><br>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Tugas AKLH mencakup pengelolaan
                        administrasi, keuangan, logistik, dan hubungan masyarakat secara terpusat oleh tim terkait, yang
                        bertujuan untuk melayani berbagai keperluan dan melakukan kontrol terhadap tata laksana kegiatan
                        SPD agar sesuai dengan peraturan yang berlaku, serta menjaga hubungan antara lingkungan luar
                        dengan internal SPD.</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div
                class="max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <img class="rounded-t-lg" src="img/ikonurusan/Ikon PROK.png" />
                <div class="p-5">
                    <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white"
                        style="color:#9f2a32">PROK</h5>
                    <h5 class="mb-2 text-xl font-semibold tracking-tight text-gray-900 dark:text-white"
                        style="color:#2a3f64">Keprotokoleran</h5>
                    <br>
                    <hr><br>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Tugas PROK mencakup persiapan dan
                        pelaksanaan apel, upacara, serta kegiatan resmi keprotokoleran lainnya, mengadakan pelatihan
                        terkait keprotokoleran untuk anggota SPD, dan menaikkan serta menurunkan bendera Merah Putih di
                        kampus.</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div
                class="max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <img class="rounded-t-lg" src="img/ikonurusan/Ikon AOPP.png" />
                <div class="p-5">
                    <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white"
                        style="color:#9f2a32">AOPP</h5>
                    <h5 class="mb-2 text-xl font-semibold tracking-tight text-gray-900 dark:text-white"
                        style="color:#2a3f64">Absensi, Operasi, Pelaporan, dan Pengamanan</h5>
                    <br>
                    <hr><br>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Tugas AOPP mencakup pencatatan
                        kehadiran, rekap absensi dan pelanggaran, serta penyusunan laporan dan jadwal operasi. Selain
                        itu, mengawasi kelancaran kegiatan, evaluasi, koordinasi dengan kampus, dan pengelolaan jadwal
                        piket, kerapian anggota, serta laporan untuk BAAK.</p>
                </div>
            </div>
        </div>


        <br><br>


        <h4 class="text-2xl font-bold tracking-tight mb-6 float-up-text" style="text-align: center;">Galeri</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (5).jpg" alt="Image 1">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (11).jpg" alt="Image 2">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (12).jpg" alt="Image 3">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (9).jpg" alt="Image 4">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (6).jpg" alt="Image 5">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (10).jpg" alt="Image 6">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (2).jpg" alt="Image 7">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (4).jpg" alt="Image 8">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (3).jpg" alt="Image 9">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (7).jpg" alt="Image 10">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (1).jpg" alt="Image 11">
            </div>
            <div>
                <img class="w-full h-48 object-cover rounded-lg transition-all duration-300 cursor-pointer filter grayscale hover:grayscale-0"
                    src="img/galeri/image (8).jpg" alt="Image 12">
            </div>
        </div>
    </div>
</div>

<x-footer></x-footer>
