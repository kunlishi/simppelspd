<x-layout>
    <x-slot:title>Jadwal Apel Mahasiswa</x-slot:title>

    <div class="p-4 sm:ml-0">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Jadwal Apel Mahasiswa</h2>
                <p class="text-sm text-gray-500">Daftar agenda apel rutin dan khusus Politeknik Statistika STIS</p>
            </div>
            
            @if(Auth::user()->role == 'admin')
            <a href="{{ route('apel.create') }}" class="flex items-center px-5 py-2.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 rounded-lg transition-colors">
                <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7v14"/>
                </svg>
                Tambah Jadwal
            </a>
            @endif
        </div>

        @if(session('success'))
        <div id="alert-3" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
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
            <div class="flex flex-col p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow dark:bg-gray-800 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ \Carbon\Carbon::parse($apel->tanggal_apel)->format('d F Y') }}
                    </span>
                    <div class="text-gray-400">
                        <i class="fas fa-ellipsis-h"></i>
                    </div>
                </div>

                <h5 class="mb-5 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $apel->nama_apel }}
                </h5>
                
                <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap gap-2">
                    @if(Auth::user()->role == 'admin')
                    <a href="{{ route('apel.edit', $apel->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-yellow-400 rounded-lg hover:bg-yellow-500 transition-colors">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    
                    <form action="{{ route('apel.delete', $apel->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini? Seluruh data presensi terkait akan ikut terhapus.')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
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
                <p class="text-sm text-gray-500 mb-6">Jadwal yang Anda buat akan muncul di sini dalam bentuk kartu.</p>
                @if(Auth::user()->role == 'admin')
                <a href="{{ route('apel.create') }}" class="text-blue-600 font-semibold hover:underline">
                    Buat Jadwal Pertama Anda &rarr;
                </a>
                @endif
            </div>
            @endforelse
        </div>
    </div>
</x-layout>