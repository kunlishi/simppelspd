<x-layout></x-layout>

<div class="p-4 sm:ml-64 mt-9">
    <div class="flex justify-center">
        <div class="block max-w-sm p-6 bg-yellow-300 border rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 w-full">
            <h3 class="text-2xl text-center font-bold dark:text-white">Pencatatan Jadwal Apel Baru</h3>
            <p class="text-center text-gray-800 dark:text-gray-400 italic opacity-50">(Mohon isi data jadwal apel di bawah ini)</p>

            {{-- Form --}}
            <form class="max-w-sm mx-auto pt-6" action="/apel-baru" method="POST" id="apelForm">
                @csrf
                <div class="mb-5">
                    <label for="nama_apel" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama / Jenis Apel</label>
                    <input type="text" id="nama_apel" name="nama_apel" placeholder="Masukkan Nama Apel" required
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                </div>

                <div class="mb-5">
                    <label for="tingkat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tingkat</label>
                    <input type="number" id="tingkat" name="tingkat" placeholder="Masukkan Tingkat Apel (0-4)" min="0" max="4" required
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                </div>

                <div class="mb-5">
                    <label for="tanggal_apel" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pelaksanaan</label>
                    <input type="date" id="tanggal_apel" name="tanggal_apel" required
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                </div>

                <div class="mb-5">
                    <label for="waktu_apel" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu Pelaksanaan</label>
                    <input type="time" id="waktu_apel" name="waktu_apel" required
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600">
                </div>

                <div class="flex justify-end gap-2">
                    <a href="/daftar-apel" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">Batal</a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-900 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        document.getElementById('apelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Pastikan nama dan tanggal apel sudah benar!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                didOpen: () => {
                    const confirmBtn = document.querySelector('.swal2-confirm');
                    const cancelBtn = document.querySelector('.swal2-cancel');
                    
                    confirmBtn.onmouseover = () => {
                        confirmBtn.style.backgroundColor = '#0f52ba';
                    };
                    confirmBtn.onmouseout = () => {
                        confirmBtn.style.backgroundColor = '#3085d6';
                    };
                    
                    cancelBtn.onmouseover = () => {
                        cancelBtn.style.backgroundColor = '#ff5555';
                    };
                    cancelBtn.onmouseout = () => {
                        cancelBtn.style.backgroundColor = '#d33';
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan Data...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    e.target.submit();
                }
            });
        });
    });
</script>
<x-footer></x-footer>