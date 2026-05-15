<x-layout>
    <div class="p-4 sm:ml-64 mt-9">

        {{-- Menampilkan Info Jadwal Apel --}}
        <div class="mb-4 bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
            <h2 class="text-xl font-bold text-gray-800">{{ $apel->nama_apel }}</h2>
            <p class="text-sm text-gray-600">
                Jadwal: {{ \Carbon\Carbon::parse($apel->tanggal_apel)->translatedFormat('l, d F Y') }}
                @if($apel->waktu_apel)
                     <br>Batas Tepat Waktu: <span class="font-bold text-red-600">{{ \Carbon\Carbon::parse($apel->waktu_apel)->format('H:i') }}</span>
                @endif
            </p>
        </div>

        {{-- Dynamic Alert Notification --}}
        <div id="notification-alert" class="hidden flex items-center p-4 mb-4 rounded-lg shadow-lg transition-opacity duration-300" role="alert">
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

        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="scanTab" data-tabs-toggle="#scanTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" 
                        id="cam-tab" 
                        data-tabs-target="#cam" 
                        type="button" 
                        role="tab" 
                        aria-controls="cam" 
                        aria-selected="true">Scanner Barcode</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" 
                        id="res-tab" 
                        data-tabs-target="#res" 
                        type="button" 
                        role="tab" 
                        aria-controls="res" 
                        aria-selected="false">Riwayat Scan Anda (<span id="riwayat-count">{{ count($myScans) }}</span>)</button>
                </li>
            </ul>
        </div>
        
        <div class="flex justify-center" id="scanTabContent">
            {{-- Tab Scanner --}}
            <div class="block max-w-sm p-6 bg-yellow-300 border rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 w-full" id="cam" role="tabpanel">
                <div id="reader" class="mx-auto w-full max-w-sm bg-white rounded-xl overflow-hidden border-4 border-white shadow-lg"></div>
                
                <div class="mt-6 max-w-sm mx-auto bg-white p-4 rounded-xl shadow">
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-gray-600">Hasil Scan Mentah:</p>
                        <p id="decoded_result" class="text-sm font-bold text-blue-700 break-words">-</p>
                    </div>
                    <form id="scanForm" action="{{ route('presensi.store', $apel->id) }}" method="POST">
                        @csrf
                        
                        {{-- OPSI STATUS MANUAL (DISPENSASI) --}}
                        <div id="manual-status-container" class="hidden mb-4 p-3 bg-orange-100 border border-orange-200 rounded-lg">
                            <label class="block text-[10px] font-bold text-orange-800 mb-2 text-center uppercase">Batas waktu telah lewat. Tentukan status:</label>
                            <div class="flex gap-2">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="status_manual" value="hadir" class="hidden peer">
                                    <div class="text-center p-2 text-xs font-bold bg-white border border-green-500 text-green-600 rounded-lg peer-checked:bg-green-600 peer-checked:text-white transition-colors">HADIR (Dispensasi)</div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="status_manual" value="terlambat" checked class="hidden peer">
                                    <div class="text-center p-2 text-xs font-bold bg-white border border-orange-500 text-orange-600 rounded-lg peer-checked:bg-red-600 peer-checked:text-white transition-colors">TERLAMBAT</div>
                                </label>
                            </div>
                        </div>

                        <label class="block text-sm font-bold mb-2">Konfirmasi Pengiriman:</label>
                        <div class="flex gap-2">
                            <input type="text" name="nim" id="scan_nim" readonly required
                                class="bg-gray-100 border border-gray-300 text-gray-900 text-lg font-bold rounded-lg block w-full p-2.5 cursor-not-allowed"
                                placeholder="Arahkan barcode...">
                            <button type="submit" id="btnSubmitScan" class="bg-blue-700 hover:bg-blue-800 transition-colors text-white px-6 py-2 rounded-lg font-bold disabled:opacity-50">
                                KIRIM
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tab Riwayat Scan --}}
            <div class="hidden p-4 rounded-lg bg-white border w-full max-w-4xl" id="res" role="tabpanel">
                <h3 class="font-bold text-blue-700 mb-4 uppercase">Data Yang Baru Anda Scan:</h3>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3">Waktu Scan</th>
                                <th scope="col" class="px-4 py-3">NIM/NAS</th>
                                <th scope="col" class="px-4 py-3">Nama Lengkap</th>
                                <th scope="col" class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat-tbody">
                            @forelse($myScans as $s)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs whitespace-nowrap">{{ \Carbon\Carbon::parse($s->updated_at)->format('H:i:s') }}</td>
                                <td class="px-4 py-3 font-bold text-gray-900">{{ $s->nim }}</td>
                                <td class="px-4 py-3">{{ $s->mahasiswa->nama ?? ($s->petugasSpd->nama_anggota ?? 'Tidak Ditemukan') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-[10px] font-black 
                                        {{ $s->status == 'hadir' ? 'bg-green-100 text-green-700' : 
                                        ($s->status == 'terlambat' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ strtoupper($s->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr id="empty-row">
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">Belum ada riwayat presensi yang Anda lakukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Import library dari CDN --}}
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    <script>
        // Logika Waktu Dispensasi
        const jamApel = "{{ $apel->waktu_apel }}"; // Format H:i:s
        const tglApel = "{{ $apel->tanggal_apel }}"; // Format Y-m-d

        function checkLateStatus() {
            if (!jamApel || !tglApel) return;
            
            const now = new Date();
            // Validasi apakah hari ini adalah hari apel
            const todayStr = now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, '0') + "-" + String(now.getDate()).padStart(2, '0');
            
            if (todayStr === tglApel.split(' ')[0]) {
                const timeParts = jamApel.split(':');
                const apelTime = new Date();
                apelTime.setHours(timeParts[0], timeParts[1], timeParts[2] || 0);

                if (now > apelTime) {
                    document.getElementById('manual-status-container').classList.remove('hidden');
                } else {
                    document.getElementById('manual-status-container').classList.add('hidden');
                }
            }
        }

        // Cek setiap 1 detik
        setInterval(checkLateStatus, 1000);
        checkLateStatus(); // Cek langsung saat load

        // Fungsi Notifikasi
        function showNotification(message, type = 'success') {
            const alert = document.getElementById('notification-alert');
            const msgText = document.getElementById('alert-message-text');
            const closeBtn = alert.querySelector('button[onclick]');

            msgText.textContent = message;
            
            alert.className = 'flex items-center p-4 mb-4 rounded-lg shadow-lg transition-opacity duration-300';
            closeBtn.className = 'ms-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 inline-flex items-center justify-center h-8 w-8';
            
            if (type === 'success') {
                alert.classList.add('bg-green-50', 'text-green-800');
                closeBtn.classList.add('bg-green-50', 'text-green-500', 'hover:bg-green-200');
            } else if (type === 'error') {
                alert.classList.add('bg-red-50', 'text-red-800');
                closeBtn.classList.add('bg-red-50', 'text-red-500', 'hover:bg-red-200');
            } else {
                alert.classList.add('bg-yellow-50', 'text-yellow-800');
                closeBtn.classList.add('bg-yellow-50', 'text-yellow-500', 'hover:bg-yellow-200');
            }

            alert.style.display = 'flex';
            setTimeout(() => closeNotification(), 5000);
        }

        function closeNotification() {
            document.getElementById('notification-alert').style.display = 'none';
        }

        // Setup Scanner HTML5-QRCode
        document.addEventListener('DOMContentLoaded', function() {
            const html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 15, qrbox: { width: 250, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                const currentNim = document.getElementById('scan_nim').value;
                let nimResult = decodedText.includes(';') ? decodedText.split(';')[0] : decodedText;
                
                if (currentNim !== nimResult) {
                    document.getElementById('scan_nim').value = nimResult;
                    document.getElementById('decoded_result').textContent = decodedText;
                    
                    if (navigator.vibrate) navigator.vibrate(100);
                }
            }).catch(err => {
                console.warn("Kamera Error / Tidak Tersedia:", err);
                document.getElementById('decoded_result').innerHTML = "<span class='text-red-500'>Kamera tidak dapat diakses. Mohon periksa izin browser.</span>";
            });
        });

        // AJAX Form Submit
        document.getElementById('scanForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const submitBtn = document.getElementById('btnSubmitScan');
            const scanInput = document.getElementById('scan_nim');

            if(!scanInput.value) {
                showNotification('NIM Kosong! Harap arahkan scanner ke barcode.', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'MEMPROSES...';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }    
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan pada server.');
                }
                return data;
            })
            .then(data => {
                if (data.status === 'success') {
                    const tbody = document.getElementById('riwayat-tbody');
                    const emptyRow = document.getElementById('empty-row');
                    if(emptyRow) emptyRow.remove();

                    const statusColor = data.data.status === 'HADIR' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';

                    const newRow = `
                        <tr class="bg-white border-b hover:bg-gray-50 animation-fade-in">
                            <td class="px-4 py-3 font-mono text-xs whitespace-nowrap">${data.data.waktu}</td>
                            <td class="px-4 py-3 font-bold text-gray-900">${data.data.nim}</td>
                            <td class="px-4 py-3">${data.data.nama}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-[10px] font-black ${statusColor}">
                                    ${data.data.status}
                                </span>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('afterbegin', newRow);
                    
                    let countEl = document.getElementById('riwayat-count');
                    countEl.innerText = parseInt(countEl.innerText) + 1;

                    showNotification(data.message, 'success');
                }
            })
            .catch(error => {
                showNotification(error.message, 'error');
            })
            .finally(() => {
                scanInput.value = ''; 
                document.getElementById('decoded_result').textContent = '-';
                submitBtn.disabled = false;
                submitBtn.textContent = 'KIRIM';
            });
        });
    </script>
</x-layout>