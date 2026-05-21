<x-layout></x-layout>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="p-4 sm:ml-64 mt-9">
    <div class="flex justify-center">
        <div class="block max-w-2xl p-6 bg-yellow-300 border rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 w-full">
            <h3 class="text-2xl text-center font-bold dark:text-white mb-2">Edit Jadwal Apel</h3>
            
            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-700 dark:text-red-400" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('apel.update', $apel->id) }}" method="POST" id="apelForm">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama / Jenis Apel</label>
                        <input type="text" name="nama_apel" required value="{{ old('nama_apel', $apel->nama_apel) }}"
                            class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pelaksanaan</label>
                        {{-- Format tanggal ke Y-m-d agar input date HTML bisa membacanya --}}
                        <input type="date" name="tanggal_apel" required value="{{ old('tanggal_apel', \Carbon\Carbon::parse($apel->tanggal_apel)->format('Y-m-d')) }}"
                            class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Batas Waktu Tepat Waktu</label>
                        <input type="time" name="waktu_apel" required value="{{ old('waktu_apel', \Carbon\Carbon::parse($apel->waktu_apel)->format('H:i')) }}"
                            class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <hr class="h-px my-6 bg-gray-200 border-0 dark:bg-gray-700">
                <h4 class="text-md font-bold dark:text-white mb-3">Penugasan & Peserta</h4>

                <div class="mb-5">
                    <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Kelas Peserta Apel <span class="text-red-500">*</span></label>
                    <div class="mb-2 flex flex-wrap gap-2">
                        <span class="text-xs text-gray-600 dark:text-gray-400 w-full italic">Pilih Cepat Tingkat:</span>
                        <button type="button" onclick="selectTingkat('1')" class="px-3 py-1 text-xs font-bold bg-white border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded">Tk 1</button>
                        <button type="button" onclick="selectTingkat('2')" class="px-3 py-1 text-xs font-bold bg-white border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded">Tk 2</button>
                        <button type="button" onclick="selectTingkat('3')" class="px-3 py-1 text-xs font-bold bg-white border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded">Tk 3</button>
                        <button type="button" onclick="selectTingkat('4')" class="px-3 py-1 text-xs font-bold bg-white border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white rounded">Tk 4</button>
                        <button type="button" onclick="clearKelas()" class="px-3 py-1 text-xs font-bold bg-red-100 text-red-600 hover:bg-red-200 border border-red-200 rounded">Reset</button>
                    </div>

                    <select class="select2-multiple shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        name="kelas_ids[]" id="kelas_ids" multiple="multiple" required style="background-color: white; color: black;">
                        @foreach($kelas as $k)
                            {{-- Cek apakah ID kelas ada di dalam array $selectedKelas --}}
                            <option value="{{ $k->id }}" {{ (collect(old('kelas_ids', $selectedKelas))->contains($k->id)) ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('apel.index') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600">Batal</a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-900 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">Perbarui Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function selectTingkat(tingkat) {
        let values = $('#kelas_ids').val() || [];
        $('#kelas_ids option').each(function() {
            if ($(this).text().trim().startsWith(tingkat)) {
                if (!values.includes($(this).val())) values.push($(this).val());
            }
        });
        $('#kelas_ids').val(values).trigger('change');
    }

    function clearKelas() {
        $('#kelas_ids').val(null).trigger('change');
    }

    $(document).ready(function() {
        $('.select2-multiple').select2({
            theme: "classic",
            placeholder: "Klik untuk memilih...",
            allowClear: true,
            width: '100%'
        });

        document.getElementById('apelForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if($('#kelas_ids').val().length === 0) {
                Swal.fire('Peringatan', 'Anda harus memilih minimal satu kelas peserta apel!', 'warning');
                return;
            }

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded ml-2',
                    cancelButton: 'bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memperbarui Data...',
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