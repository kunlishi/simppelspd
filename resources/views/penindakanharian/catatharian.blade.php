<x-layout></x-layout>

<div class="p-4 sm:ml-64 mt-9">
    <div class="flex justify-center">
        <div
            class="block max-w-sm p-6  bg-yellow-300 border rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 w-full">
            <h3 class="text-2xl text-center font-bold dark:text-white">Pencatatan Penindakan Harian</h3>
            <p class="text-center text-gray-800 dark:text-gray-400 italic opacity-50">(Mohon isi data pelanggaran di
                bawah ini)
            </p>

            {{-- Form --}}
            <form class="max-w-sm mx-auto pt-6" action="{{ route('penindakan-harian.store') }}" method="POST"
                id="yourForm">
                @csrf
                <div class="mb-5">
                    <label for="nim"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIM</label>
                    <input type="text" id="nim" name="nim" placeholder="Masukkan Nim" required
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>
                <div class="mb-5">
                    <label for="nama_mahasiswa"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Mahasiswa</label>
                    <input type="text" id="nama_mahasiswa" name="nama_mahasiswa" readonly
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>
                <div class="mb-5">
                    <label for="kelas"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
                    <input type="text" id="kelas" name="kelas" readonly
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>
                {{-- <div class="mb-5">
                    <label for="tingkat"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tingkat</label>
                    <input type="text" id="tingkat" name="tingkat" readonly
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div> --}}
                <div class="mb-5">
                    <label for="pelanggaran"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pelanggaran</label>
                    <select
                        class="pelanggaran shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        name="pelanggaran[]" multiple="multiple" id="pelanggaran" required
                        style="background-color: white; color: black;">
                        @foreach ($pelanggarans as $pelanggaran)
                            <option value="{{ $pelanggaran->kodePelanggaran }}"
                                {{ in_array($pelanggaran->kodePelanggaran, old('violation', [])) ? 'selected' : '' }}>
                                {{ $pelanggaran->namaPelanggaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" id="tahun_akademik" name="tahun_akademik">
                <div class="flex justify-end">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-900 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert untuk Notifikasi Sukses
        @if (session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                timer: 2000, // Waktu dalam milidetik (3 detik)
                showConfirmButton: false, // Tidak ada tombol "OK"
                allowOutsideClick: false, // Mencegah klik di luar untuk menutup
                allowEscapeKey: false // Mencegah popup ditutup dengan tombol ESC
            });
        @endif

        // Event handler untuk form submit
        document.getElementById('yourForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah pengiriman form secara langsung

            // Tampilkan notifikasi konfirmasi "Apakah Anda Yakin?"
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded ml-2',
                    cancelButton: 'bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan popup loading
                    Swal.fire({
                        title: 'Mengirim Data...',
                        text: 'Harap tunggu, data sedang diproses.',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading(); // Menampilkan animasi loading
                        }
                    });

                    // Kirim form setelah konfirmasi
                    e.target.submit();
                }
            });
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#nim').on('input', function() {
            const nim = $(this).val();

            // Hapus pesan error sebelumnya
            $('#nim-error').remove();

            if (nim.length === 9) { // Validasi NIM hanya jika panjangnya 9 karakter
                $.ajax({
                    url: '{{ route('get.mahasiswa') }}',
                    type: 'GET',
                    data: {
                        nim: nim
                    },
                    success: function(response) {
                        if (response) {
                            // Isi data jika data ditemukan
                            $('#nama_mahasiswa').val(response.nama);
                            $('#kelas').val(response.kelas);
                            $('#tingkat').val(response.tingkat);
                        } else {
                            // Kosongkan data dan tampilkan pesan error jika data tidak ditemukan
                            $('#nama_mahasiswa').val('');
                            $('#kelas').val('');
                            $('#tingkat').val('');
                            $('#nim').after(
                                '<span id="nim-error" style="color: red; font-size: 12px;">Data tidak ditemukan</span>'
                            );
                        }
                    },
                    error: function() {
                        // Kosongkan data dan tampilkan pesan error jika terjadi kesalahan
                        $('#nama_mahasiswa').val('');
                        $('#kelas').val('');
                        $('#tingkat').val('');
                        $('#nim').after(
                            '<span id="nim-error" style="color: red; font-size: 12px;">Terjadi kesalahan saat mengambil data</span>'
                        );
                    }
                });
            } else {
                // Kosongkan data jika NIM kurang dari 9 karakter
                $('#nama_mahasiswa').val('');
                $('#kelas').val('');
                $('#tingkat').val('');
            }
        });


        $('#pelanggaran').select2({
            theme: "classic",
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: "{{ route('pelanggaran.index') }}",
                processResults: function({
                    data
                }) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.kodePelanggaran,
                                text: item.namaPelanggaran
                            }
                        })
                    }
                }
            }
        });

        function getTahunAkademik() {
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const currentMonth = currentDate.getMonth() + 1; // Januari = 0, jadi tambahkan 1

            let tahunAkademik = '';
            if (currentMonth >= 8) { // Jika bulan >= Agustus
                tahunAkademik = `${currentYear}/${currentYear + 1}`;
            } else { // Jika bulan < Agustus
                tahunAkademik = `${currentYear - 1}/${currentYear}`;
            }

            return tahunAkademik;
        }

        // Set nilai tahun akademik di input
        $('#tahun_akademik').val(getTahunAkademik());
    });
</script>

<x-footer></x-footer>
