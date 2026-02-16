<x-layout>
    <div class="p-4 sm:ml-64 mt-9">
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="scanTab" data-tabs-toggle="#scanTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" 
                        id="cam-tab" 
                        data-tabs-target="#cam" 
                        type="button" 
                        role="tab" 
                        aria-controls="cam" 
                        aria-selected="true">Scanner</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" 
                        id="res-tab" 
                        data-tabs-target="#res" 
                        type="button" 
                        role="tab" 
                        aria-controls="res" 
                        aria-selected="false">Riwayat Anda ({{ count($myScans) }})</button>
                </li>
            </ul>
        </div>
        
        <div class="flex justify-center" id="scanTabContent">
            <div class="block max-w-sm p-6 bg-yellow-300 border rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 w-full" id="cam" role="tabpanel">
                <div id="reader" class="mx-auto w-full max-w-sm bg-white rounded-xl overflow-hidden border-4 border-white shadow-lg"></div>
                
                <div class="mt-6 max-w-sm mx-auto bg-white p-4 rounded-xl shadow">
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-gray-600">Hasil Scan:</p>
                        <p id="decoded_result" class="text-lg font-bold text-blue-700">-</p>
                    </div>
                    <form action="{{ route('presensi.store', $apel->id) }}" method="POST">
                        @csrf
                        <label class="block text-sm font-bold mb-2">Konfirmasi Hasil Scan:</label>
                        <div class="flex gap-2">

                            <input type="text" name="nim" id="scan_nim" readonly required
                                class="bg-gray-100 border border-gray-300 text-gray-900 text-lg font-bold rounded-lg block w-full p-2.5"
                                placeholder="Scanning...">
                            <button type="submit" class="bg-blue-700 text-white px-6 py-2 rounded-lg font-bold">KIRIM</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="hidden p-4 rounded-lg bg-white border" id="res" role="tabpanel">
                <h3 class="font-bold text-blue-700 mb-4 uppercase">Data Yang Baru Anda Scan:</h3>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase">
                            <tr><th class="px-4 py-3">Waktu</th><th class="px-4 py-3">NIM</th><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($myScans as $s)
                            <tr class="border-b">
                                <td class="px-4 py-3 font-mono text-xs">{{ $s->updated_at->format('H:i:s') }}</td>
                                <td class="px-4 py-3 font-bold">{{ $s->nim }}</td>
                                <td class="px-4 py-3">{{ $s->mahasiswa->nama ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $s->status == 'hadir' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ strtoupper($s->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 20, qrbox: { width: 275, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                let nimResult = decodedText;
                if (decodedText.includes(';')){
                    nimResult = decodedText.split(';')[0];
                }            
            
                document.getElementById('scan_nim').value = nimResult;
                document.getElementById('decoded_result').textContent = decodedText;
                
                if (navigator.vibrate) navigator.vibrate(100);
            }).catch(err => console.error("Kamera gagal aktif:", err));
        });
    </script>
</x-layout>

<x-footer></x-footer>