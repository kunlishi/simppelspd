<x-layout>
    <x-slot:title>Jadwal Apel Mahasiswa</x-slot:title>

    <div class="p-4 sm:ml-64 mt-9">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Jadwal Apel Mahasiswa</h2>
                <p class="text-sm text-gray-500">Daftar agenda apel rutin dan khusus Politeknik Statistika STIS</p>
            </div>
            
            @if(Auth::user()->role == 'admin')
            <a href="{{ route('apel.create') }}" class="flex items-center px-5 py-2.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 rounded-lg transition-colors shadow-md">
                <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7v14"/>
                </svg>
                Tambah Jadwal
            </a>
            @endif
        </div>

        {{-- Pesan Sukses --}}
        @if(session('success'))
        <div id="alert-3" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 shadow-sm" role="alert">
            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($apels as $apel)
            <div class="flex flex-col p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow dark:bg-gray-800 dark:border-gray-700 relative">
                
                {{-- Status Hari Ini --}}
                @if(\Carbon\Carbon::parse($apel->tanggal_apel)->isToday())
                    <span class="absolute top-0 right-0 -mt-2 -mr-2 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                    </span>
                @endif

                <div class="flex items-center justify-between mb-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded dark:bg-blue-200 dark:text-blue-800">
                        {{--<i class="far fa-users me-1"></i> --}}
                        {{-- Menghitung jumlah kelas yang ikut apel dari relasi pivot --}}
                        {{ $apel->kelasPeserta ? $apel->kelasPeserta->count() : 0 }} Kelas Peserta
                    </span>
                </div>

                <h5 class="mb-3 text-xl font-bold tracking-tight text-gray-900 dark:text-white line-clamp-2">
                    {{ $apel->nama_apel }}
                </h5>

                <div class="space-y-2 mt-1 mb-4 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ \Carbon\Carbon::parse($apel->tanggal_apel)->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Mulai: <span class="font-bold ml-1 {{ \Carbon\Carbon::parse($apel->tanggal_apel)->isToday() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                            {{ $apel->waktu_apel ? \Carbon\Carbon::parse($apel->waktu_apel)->format('H:i') : '--:--' }}
                        </span>
                    </div>
                    {{-- Info Petugas --}}
                    <div class="flex items-center mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span class="text-xs truncate">
                            @if($apel->petugasSpd && $apel->petugasSpd->count() > 0)
                                Petugas: {{ $apel->petugasSpd->pluck('nama_anggota')->implode(', ') }}
                            @else
                                <i class="text-gray-400">Belum ada petugas ditugaskan</i>
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-2">
                    @if(Auth::user()->role == 'admin')
                    {{-- Arahkan tombol lihat laporan menggunakan apel_id --}}
                    <a href="{{ route('presensi.reportAdmin', ['apel_id' => $apel->id]) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        <i class="fas fa-file-alt me-1"></i> Laporan
                    </a>

                    {{-- Tombol Edit (Jika ada fitur edit) --}}
                        @php
                            $tz = 'Asia/Jakarta';
                            $waktu_target = \Carbon\Carbon::parse($apel->tanggal_apel->format('Y-m-d') . ' ' . $apel->waktu_apel, $tz);
                            $isLocked = now($tz) >= $waktu_target->subMinutes(90);
                        @endphp

                        @if($isLocked)
                            {{-- <button disabled class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-gray-400 rounded-lg cursor-not-allowed shadow-sm" title="Sudah dikunci (H-2 Jam)">
                                <i class="fas fa-lock me-1"></i> Edit
                            </button> --}}
                        @else
                            <a href="{{ route('apel.edit', $apel->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-yellow-400 rounded-lg hover:bg-yellow-500 transition-colors shadow-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        @endif
                    
                    {{-- Hapus Jadwal Apel --}}
                    <button type="button" onclick="confirmDelete('{{ $apel->id }}', '{{ $apel->nama_apel }}')" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                        <i class="fas fa-trash me-1"></i> Hapus
                    </button>

                    {{-- Form Hapus Hidden --}}
                    <form id="delete-form-{{ $apel->id }}" action="{{ route('apel.delete', $apel->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full flex flex-col items-center justify-center p-12 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl dark:bg-gray-800 dark:border-gray-700">
                <div class="p-3 bg-gray-100 rounded-full mb-4 dark:bg-gray-700">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada jadwal apel</h3>
                <p class="text-sm text-gray-500 mb-6 text-center max-w-md">Jadwal yang Anda buat akan muncul di sini. Silakan buat jadwal baru beserta penugasan kelasnya.</p>
                @if(Auth::user()->role == 'admin')
                <a href="{{ route('apel.create') }}" class="text-blue-600 font-semibold hover:underline">
                    Buat Jadwal Pertama Anda &rarr;
                </a>
                @endif
            </div>
            @endforelse
        </div>
    </div>
    <script>
        function confirmDelete(id, nama) {
            Swal.fire({
                title: 'Hapus Jadwal?',
                text: "Anda akan menghapus " + nama + ". Data presensi yang terkait juga mungkin akan terhapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>   
    {{-- Pastikan ini ada untuk menampilkan feedback setelah redirect --}}
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{{ session('error') }}",
        });
    </script>
    @endif
</x-layout>