<x-layout>
    <div class="p-4 sm:ml-64 mt-9">

        {{-- Dynamic Alert Notification --}}
        <div id="notification-alert" class="hidden flex items-center p-4 mb-4 rounded-lg shadow-lg" role="alert">
            <svg id="alert-icon" class="flex-shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <div class="ms-3 text-sm font-medium">
                <span id="alert-message-text">-</span>
            </div>
            <button type="button" onclick="closeNotification()" class="ms-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 inline-flex items-center justify-center h-8 w-8" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>

        @if(session('message'))
        <div id="alert-message" class="flex items-center p-4 mb-4 text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <div class="ms-3 text-sm font-medium">
                {{ session('message') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-blue-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-message" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
        @endif

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
                    <form id="scanForm" action="{{ route('presensi.store', $apel->id) }}" method="POST">
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
        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, type = 'success') {
            const alert = document.getElementById('notification-alert');
            const msgText = document.getElementById('alert-message-text');
            const alertIcon = document.getElementById('alert-icon');
            const closeBtn = alert.querySelector('button[onclick]');

            msgText.textContent = message;
            alert.classList.remove('hidden', 'bg-blue-50', 'text-blue-800', 'dark:bg-gray-800', 'dark:text-blue-400', 'bg-red-50', 'text-red-800', 'dark:text-red-400');
            closeBtn.classList.remove('bg-blue-50', 'text-blue-500', 'hover:bg-blue-200', 'dark:bg-gray-800', 'dark:text-blue-400', 'dark:hover:bg-gray-700', 'bg-red-50', 'text-red-500', 'hover:bg-red-200', 'dark:text-red-400', 'dark:hover:bg-red-700');
            
            if (type === 'success') {
                alert.classList.add('bg-blue-50', 'text-blue-800', 'dark:bg-gray-800', 'dark:text-blue-400');
                closeBtn.classList.add('bg-blue-50', 'text-blue-500', 'hover:bg-blue-200', 'dark:bg-gray-800', 'dark:text-blue-400', 'dark:hover:bg-gray-700');
            } else if (type === 'error') {
                alert.classList.add('bg-red-50', 'text-red-800', 'dark:bg-gray-800', 'dark:text-red-400');
                closeBtn.classList.add('bg-red-50', 'text-red-500', 'hover:bg-red-200', 'dark:bg-gray-800', 'dark:text-red-400', 'dark:hover:bg-red-700');
            }

            // Auto close setelah 5 detik
            setTimeout(() => {
                closeNotification();
            }, 5000);
        }

        function closeNotification() {
            document.getElementById('notification-alert').classList.add('hidden');
        }

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

        document.getElementById('scanForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');

            submitBtn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }    
            })
            .then(response => response.json())
            .then(data=>{
                if (data.status === 'success') {
                    // Update Tabel Riwayat secara dinamis tanpa reload
                    const tbody = document.querySelector('#res table tbody');
                    const newRow = `
                        <tr class="border-b">
                            <td class="px-4 py-3 font-mono text-xs">${data.data.waktu}</td>
                            <td class="px-4 py-3 font-bold">${data.data.nim}</td>
                            <td class="px-4 py-3">${data.data.nama}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black ${data.data.status === 'HADIR' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'}">
                                    ${data.data.status}
                                </span>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('afterbegin', newRow); // Tambah baris baru di paling atas
                    document.getElementById('scan_nim').value = ''; // Reset input scan
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'Gagal mencatat presensi.', 'error');
                    document.getElementById('scan_nim').value = '';
                }
            })
            .catch(error=>{
                console.error('Error:', error);
                showNotification(error.message || 'Gagal mencatat presensi. Silakan coba lagi.', 'error');
                document.getElementById('scan_nim').value = '';
            })
            .finally(()=>{
                submitBtn.disabled = false;
            })
        });
    </script>
</x-layout>

<x-footer></x-footer>