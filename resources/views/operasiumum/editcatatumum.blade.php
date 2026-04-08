@include('components.layout')

<div class="p-4 sm:ml-64 mt-16">
    <div class="flex justify-center">
        <div
            class="block max-w-sm p-6 bg-yellow-300 border rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 w-full">
            <h3 class="text-center text-2xl font-bold dark:text-white">Edit Detail Pelanggaran</h3>
            <p class="text-center text-gray-800 dark:text-gray-400 italic opacity-50">(Mohon isi data pelanggaran di
                bawah ini)</p>

            {{-- Notifikasi Sukses --}}
            @if (session('success'))
                <div id="success-message"
                    class="flex items-center p-4 mb-4 text-sm text-green-800 bg-green-50 rounded-lg dark:bg-green-800 dark:text-green-200 border border-green-300 shadow">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707a1 1 0 00-1.414-1.414L9 11.586 7.707 10.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <script>
                    setTimeout(() => {
                        document.getElementById('success-message').classList.add('opacity-0', 'transition-opacity',
                            'duration-500');
                        setTimeout(() => document.getElementById('success-message').remove(), 500);
                    }, 3000);
                </script>
            @endif
            {{-- Notifikasi Error --}}
            @if ($errors->any())
                <div id="error-message"
                    class="flex items-center p-4 mb-4 text-sm text-red-800 bg-red-50 rounded-lg dark:bg-red-800 dark:text-red-200 border border-red-300 shadow">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.707-5.707a1 1 0 011.414-1.414L9 10.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-2-2z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <script>
                    // Menghilangkan notifikasi error setelah 5 detik
                    setTimeout(() => {
                        document.getElementById('error-message').classList.add('opacity-0', 'transition-opacity',
                            'duration-500');
                        setTimeout(() => document.getElementById('error-message').remove(), 500);
                    }, 5000);
                </script>
            @endif

            {{-- Form --}}
            <form class="max-w-sm mx-auto pt-6" action="{{ route('operasi-umum.update', $operasiUmum->id) }}"
                method="POST">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label for="nim"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIM</label>
                    <input type="text" id="nim" name="nim" readonly
                        value="{{ old('nim', $operasiUmum->nim ?? '') }}"
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>
                <div class="mb-5">
                    <label for="nama_mahasiswa"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Mahasiswa</label>
                    <input type="text" id="nama_mahasiswa" name="nama_mahasiswa" readonly
                        value="{{ old('nama_mahasiswa', $operasiUmum->nama ?? '') }}"
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>
                <div class="mb-5">
                    <label for="kelas"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
                    <input type="text" id="kelas" name="kelas" readonly
                        value="{{ old('kelas', $operasiUmum->kelas ?? '') }}"
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>
                {{-- <div class="mb-5">
                    <label for="tingkat"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tingkat</label>
                    <input type="text" id="tingkat" name="tingkat" readonly
                        value="{{ old('kelas', $operasiUmum->tingkat ?? '') }}"
                        class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div> --}}
                <div class="mb-5">
                    <label for="pelanggaran" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis
                        Pelanggaran</label>
                    <select
                        class="pelanggaran shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        name="pelanggaran" multiple="multiple" id="pelanggaran" required
                        style="background-color: white; color: black;">
                        @foreach ($pelanggarans as $pelanggaran)
                            <option value="{{ $pelanggaran->namaPelanggaran }}" @selected(old('pelanggaran', $selectedPelanggaran) == $pelanggaran->namaPelanggaran)>
                                {{ $pelanggaran->namaPelanggaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" id="tahun_akademik" name="tahun_akademik"
                    value="{{ old('tahun_akademik', $operasiUmum->tahun_akademik ?? '') }}">
                <div class="flex justify-end">
                    <button type="button" id="confirmButton"
                        class="text-white bg-blue-700 hover:bg-blue-900 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-700 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Simpan perubahan
                    </button>
                </div>

                {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
                <script>
                    document.getElementById('confirmButton').addEventListener('click', function(e) {
                        Swal.fire({
                            title: 'Apakah Anda Yakin?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, simpan perubahan',
                            cancelButtonText: 'Batal',
                            cancelButtonText: 'Batal',
                            customClass: {
                                confirmButton: 'bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded ml-2',
                                cancelButton: 'bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const form = document.querySelector('form');
                                if (form) {
                                    // Submit form
                                    form.submit();

                                    // Tampilkan notifikasi berhasil setelah submit
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Perubahan telah disimpan.',
                                        icon: 'success',
                                        timer: 2000, // Durasi popup
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Alihkan ke halaman tabel laporan rutin (ganti URL sesuai kebutuhan)
                                        window.location.href = '/laporan-umum';
                                    });
                                }
                            }
                        });
                    });
                </script>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    $(document).ready(function() {
        // Inisialisasi Select2 dengan multiple
        $('#pelanggaran').select2({
            theme: "classic",
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: "{{ route('pelanggaran.edit') }}",
                dataType: 'json',
                processResults: function(data) {
                    console.log(data);
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.kodePelanggaran,
                                text: item.namaPelanggaran
                            }
                        })
                    };
                }
            }
        });

        // Pre-select values berdasarkan nilai lama (old values atau selectedPelanggaran)
        var selectedPelanggaran = @json($selectedPelanggaran); // Ambil nilai yang sudah dipilih
        if (selectedPelanggaran && selectedPelanggaran.length > 0) {
            // Pastikan bahwa selectedPelanggaran adalah array
            $('#pelanggaran').val(selectedPelanggaran).trigger('change');
        }
    });
</script>

<x-footer></x-footer>
