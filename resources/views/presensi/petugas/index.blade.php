<x-layout>
    <div class="p-4 sm:ml-64 mt-9">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Pencatatan Presensi Apel</h2>
                <p class="text-sm text-gray-500 italic">Pilih jadwal apel di bawah ini untuk memulai pemindaian QR Code.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($apels as $apel)
                <a href="{{ route('presensi.scan', $apel->id) }}" class="group block p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md hover:bg-yellow-50 transition-all border-l-8 border-l-yellow-400">
                    <div class="flex justify-between items-start mb-4">
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded uppercase">
                            Tingkat {{ $apel->tingkat }}
                        </span>
                        <span class="text-gray-400 group-hover:text-yellow-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-black text-gray-900 mb-1 group-hover:text-blue-700 transition-colors">
                        {{ $apel->nama_apel }}
                    </h3>
                    
                    <div class="space-y-2 mt-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ \Carbon\Carbon::parse($apel->tanggal_apel)->translatedFormat('l, d F Y') }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Mulai: <span class="font-bold ml-1 text-red-600">{{ $apel->waktu_apel ?? '--:--' }}</span>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center text-xs font-bold text-blue-600 uppercase">
                        Mulai Scanning
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </div>
                </a>
            @empty
                <div class="col-span-full p-12 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                    <p class="text-gray-500 italic">Belum ada jadwal apel yang dibuat oleh Admin.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>