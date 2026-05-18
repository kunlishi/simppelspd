<x-layout>
<style>
    /* Garis pembatas untuk tabel */
    table {
        border-collapse: collapse;
        width: 100%;
    }

    table thead th,
    table tbody td {
        border: 1px solid #d1d5db;
        padding: 12px;
        text-align: center;
    }

    /* Sticky positioning untuk desktop */
    table thead th.sticky,
    table tbody td.sticky {
        position: sticky;
        z-index: 10;
    }

    table thead th.sticky.left-0,
    table tbody td.sticky.left-0 {
        left: 0;
        background-color: #f9fafb;
        border-right: 1px solid #9ca3af; /* Dipertebal sedikit agar batasnya jelas saat discroll */
    }

    table tbody td.sticky.left-0 {
        background-color: #ffffff;
        border-right: 1px solid #9ca3af;
    }

    /* PENYESUAIAN KHUSUS LAYAR HP (MOBILE RESPONSIVE) */
    @media screen and (max-width: 768px) {
        table tbody td,
        table thead th {
            white-space: nowrap; /* Mencegah teks turun ke baris baru agar tabel rapi */
        }
        
        /* Mematikan efek sticky di HP agar tidak menutupi kolom No & NIM */
        table thead th.sticky,
        table tbody td.sticky,
        table thead th.sticky.left-0,
        table tbody td.sticky.left-0 {
            position: static !important; /* Kembalikan ke posisi normal */
            z-index: auto !important;
            border-right: 1px solid #d1d5db !important; /* Kembalikan border seperti semula */
        }
    }

    .dropdown-item-text {
        color: #374151;
        font-size: 0.75rem;
    }
    .dropdown-item-text:hover {
        color: #000000;
    }
</style>

<div class="p-4 sm:ml-64 mt-8">
    <h3 class="text-2xl font-bold dark:text-white">Laporan Kehadiran Apel</h3>
    
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <div id="exampleWrapper" class="dark:bg-gray-900 p-4">
            <div class="datatable-wrapper no-footer sortable searchable fixed-columns">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-start gap-4 w-full sm:w-auto">
                        {{-- Logika Route Dinamis untuk Admin dan SPD --}}
                        @php
                            $routeAction = Auth::user()->role == 'admin' ? route('presensi.reportAdmin') : route('presensi.reportSpd');
                        @endphp
                        
                        <form method="GET" action="{{ $routeAction }}" id="filterForm" class="flex flex-wrap items-center gap-2">
                            {{-- Dropdown Pilih Jadwal Apel --}}
                            <select name="apel_id" id="apel_id_filter"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 p-2 dark:bg-gray-700 dark:text-white"
                                onchange="this.form.submit()">
                                <option value="" disabled {{ !$apel_id ? 'selected' : '' }}>Pilih Jadwal Apel</option>
                                @foreach($apels as $a)
                                    <option value="{{ $a->id }}" {{ $apel_id == $a->id ? 'selected' : '' }}>
                                        {{ $a->nama_apel }} ({{ \Carbon\Carbon::parse($a->tanggal_apel)->translatedFormat('d M Y') }})
                                    </option>
                                @endforeach
                            </select>

                            {{-- Dropdown Pilih Tingkat --}} {{--
                            <select id="tingkat-filter" name="tingkat"
                                class="text-gray-500 bg-white border border-gray-300 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm p-2 dark:bg-gray-800 dark:text-white"
                                onchange="this.form.submit()">
                                <option value="" selected>Semua Tingkat</option>
                                @foreach([1,2,3,4] as $t)
                                    <option value="{{ $t }}" {{ request('tingkat') == $t ? 'selected' : '' }}>Tingkat {{ $t }}</option>
                                @endforeach
                            </select>
                            --}}

                            {{-- Pencarian Server-Side --}}
                            <div class="relative flex items-center gap-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 20 20" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                                    </div>
                                    <input type="text" id="search-input" name="nama"
                                        class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-48 bg-gray-50 focus:ring-blue-500"
                                        value="{{ request('nama') }}" placeholder="Cari Nama/NIM">
                                </div>
                                <button type="submit" class="p-2 text-sm text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg px-4 transition-colors">
                                    Cari
                                </button>
                            </div>

                            {{-- Tombol Reset dinamis menyesuaikan role --}}
                            <button type="button" onclick="window.location.href='{{ $routeAction }}'"
                                class="p-2 text-sm text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg px-4 transition-colors">
                                Reset Filter
                            </button>
                        </form>
                    </div>

                    <div class="flex flex-row items-center justify-end gap-2 w-full sm:w-auto">
        
                        {{-- Tombol Email Peringatan Alpa (Ikon Saja) --}}
                        @if(Auth::user()->role == 'admin' && ($apel_id ?? null))
                            <button type="button" onclick="kirimEmailAlpa('{{ $apel_id }}')" 
                                title="Kirim Email Peringatan Massal"
                                class="flex items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition-colors focus:outline-none focus:ring-2 focus:ring-red-300 h-9 w-9">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        @endif

                        {{-- Wrapper Export Dropdown --}}
                        <div class="relative">
                            <button id="exportDropdownButton" data-dropdown-toggle="exportDropdown" type="button"
                                class="flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-medium text-gray-900 hover:bg-gray-100 dark:bg-gray-800 dark:text-white h-9">
                                Export <svg class="ms-1.5 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                            </button>
                            
                            {{-- Tambahkan 'absolute right-0' agar dropdown menu sejajar ke kanan dan tidak merusak layout --}}
                            <div id="exportDropdown" class="z-20 hidden w-40 bg-white divide-y divide-gray-100 rounded-lg shadow-lg border border-gray-100 dark:bg-gray-700 absolute right-0 mt-2">
                                <ul class="p-2 text-left text-xs font-medium" aria-labelledby="exportDropdownButton">
                                    <li>
                                        <button onclick="downloadFile('csv')" class="flex w-full items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dropdown-item-text dark:text-gray-200">
                                            Export CSV
                                        </button>
                                    </li>
                                    <li>
                                        <button onclick="downloadFile('excel')" class="flex w-full items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dropdown-item-text dark:text-gray-200">
                                            Export EXCEL
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                    </div>

                </div>

                <div class="overflow-x-auto">
                    <table id="export-table">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th>No</th>
                                <th>Kelas</th>
                                <th>NIM</th>
                                <th class="sticky left-0 bg-gray-50 dark:bg-gray-700">Nama Mahasiswa</th>                                
                                <th>Waktu Scan</th>
                                <th>Satgas Pemeriksa</th>
                                <th>Status</th>
                                {{-- @if(Auth::user()->role == 'admin') <th>Aksi</th> @endif --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $row)
                            <tr class="bg-white border-b dark:bg-gray-800 hover:bg-gray-50">
                                <td class="text-xs text-center">{{ $data->firstItem() + $index }}</td>
                                <td>{{ $row->kelas ?? '-' }}</td>
                                <td>{{ $row->nim }}</td>
                                <td class="sticky left-0 bg-white dark:bg-gray-800 text-left">{{ $row->nama_mhs }}</td>
                                <td class="text-xs font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $row->waktu_scan ? \Carbon\Carbon::parse($row->waktu_scan)->format('H:i:s') : '-' }}
                                </td>
                                <td class="text-xs italic text-gray-600">
                                    @if($row->nama_petugas)
                                        {{ $row->nama_petugas }}
                                    @elseif($row->waktu_scan)
                                        <span class="text-blue-600 font-bold">ADMIN / SISTEM</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user()->role == 'admin')
                                        <div class="relative inline-block w-32">
                                            <select onchange="changeStatusInline(this, '{{ $row->presensi_id ?? 'new' }}', '{{ $row->nim }}')"
                                                    data-original="{{ $row->status_kehadiran }}"
                                                    class="w-full px-2 py-1.5 rounded text-[10px] font-black cursor-pointer appearance-none outline-none border-none shadow-sm focus:ring-2 focus:ring-blue-400 transition-colors text-center
                                                    {{ $row->status_kehadiran == 'hadir' ? 'bg-green-100 text-green-700' : 
                                                       ($row->status_kehadiran == 'terlambat' ? 'bg-yellow-100 text-yellow-700' : 
                                                       ($row->status_kehadiran == 'tidak_hadir' ? 'bg-red-100 text-red-700' : 
                                                       ($row->status_kehadiran == 'izin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'))) }}"
                                                    style="text-align-last: center;">
                                                <option class="bg-white text-gray-900 text-left font-semibold" value="hadir" {{ $row->status_kehadiran == 'hadir' ? 'selected' : '' }}>HADIR</option>
                                                <option class="bg-white text-gray-900 text-left font-semibold" value="tidak_hadir" {{ $row->status_kehadiran == 'tidak_hadir' ? 'selected' : '' }}>TIDAK HADIR</option>
                                                <option class="bg-white text-gray-900 text-left font-semibold" value="terlambat" {{ $row->status_kehadiran == 'terlambat' ? 'selected' : '' }}>TERLAMBAT</option>
                                                <option class="bg-white text-gray-900 text-left font-semibold" value="izin" {{ $row->status_kehadiran == 'izin' ? 'selected' : '' }}>IZIN</option>
                                                <option class="bg-white text-gray-900 text-left font-semibold" value="sakit" {{ $row->status_kehadiran == 'sakit' ? 'selected' : '' }}>SAKIT</option>
                                            </select>
                                        </div>
                                    @else
                                        {{-- Tampilan Badge Biasa untuk Petugas SPD --}}
                                        <span class="px-2 py-1 rounded text-[10px] font-black 
                                            {{ $row->status_kehadiran == 'hadir' ? 'bg-green-100 text-green-700' : 
                                            ($row->status_kehadiran == 'terlambat' ? 'bg-yellow-100 text-yellow-700' : 
                                            ($row->status_kehadiran == 'tidak_hadir' ? 'bg-red-100 text-red-700' : 
                                            ($row->status_kehadiran == 'izin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'))) }}">
                                            {{ strtoupper(str_replace('_', ' ', $row->status_kehadiran)) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-4 italic">Pilih jadwal apel atau tidak ada data tersedia</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        {{-- Menampilkan paginasi lengkap dengan withQueryString --}}
        @if($data->count() > 0)
            {{ $data->withQueryString()->links() }}
        @endif
    </div>
</div>

{{-- SweetAlert2 untuk Popup --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function downloadFile(format) {
        const baseUrl = "{{ url('presensi/download') }}/" + format; 
        const apel_id = document.getElementById('apel_id_filter').value;
        const tingkat = document.getElementById('tingkat-filter').value;
        
        if (!apel_id) {
            Swal.fire('Perhatian', 'Silakan pilih Jadwal Apel terlebih dahulu sebelum mengunduh laporan.', 'warning');
            return;
        }
        
        const url = baseUrl + `?apel_id=${apel_id}&tingkat=${tingkat}`;
        window.location.href = url;
    }

    async function kirimEmailAlpa(apel_id) {
        // 1. Konfirmasi Awal
        const result = await Swal.fire({
            title: 'Kirim Email Peringatan?',
            text: "Sistem akan mengirim email satu per satu ke semua mahasiswa yang berstatus TIDAK HADIR.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Mulai Kirim!',
            cancelButtonText: 'Batal'
        });

        if (!result.isConfirmed) return;

        // 2. Tampilkan Loading Penyiapan Data
        Swal.fire({
            title: 'Menyiapkan Data...',
            text: 'Mencari daftar mahasiswa yang alpa.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            // 3. Ambil daftar mahasiswa
            const listResponse = await fetch('/presensi/get-alpa-list', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ apel_id: apel_id })
            });
            const listData = await listResponse.json();
            
            if (!listData.success) throw new Error("Gagal mengambil data dari server.");
            
            const mhsList = listData.data;
            const total = mhsList.length;

            if (total === 0) {
                Swal.fire('Info', 'Semua mahasiswa hadir. Tidak ada email peringatan yang perlu dikirim.', 'info');
                return;
            }

            // 4. Siapkan Variabel Progres
            let sukses = 0;
            let gagal = 0;

            // Tampilkan Popup Progres yang bisa diupdate
            Swal.fire({
                title: 'Mengirim Email...',
                html: `Progres: <b>0</b> dari <b>${total}</b> diproses.<br><br><span style="color: green; font-size: 14px;">Sukses: 0</span> | <span style="color: red; font-size: 14px;">Gagal: 0</span>`,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // 5. Loop: Kirim email satu per satu
            for (let i = 0; i < total; i++) {
                const mhs = mhsList[i];
                try {
                    const sendResp = await fetch('/presensi/send-alpa-single', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ apel_id: apel_id, nim: mhs.nim })
                    });
                    
                    const sendData = await sendResp.json();
                    
                    if (sendData.success) {
                        sukses++;
                    } else {
                        gagal++;
                        console.error('Gagal kirim ke ' + mhs.nim + ':', sendData.message);
                    }
                } catch (e) {
                    gagal++;
                    console.error('Network Error ke ' + mhs.nim);
                }
                
                // 6. UPDATE TEKS PROGRES SECARA REAL-TIME DI LAYAR
                Swal.getHtmlContainer().innerHTML = `Progres: <b>${sukses + gagal}</b> dari <b>${total}</b> diproses.<br><br><span style="color: green; font-size: 14px; font-weight: bold;">Sukses: ${sukses}</span> | <span style="color: red; font-size: 14px; font-weight: bold;">Gagal: ${gagal}</span>`;
            }

            // 7. Jika Loop Selesai, Tampilkan Hasil Akhir
            Swal.fire({
                icon: gagal === 0 ? 'success' : 'warning',
                title: 'Pengiriman Selesai!',
                html: `<p>Berhasil terkirim: <b>${sukses} email</b></p>${gagal > 0 ? `<p style="color:red;">Gagal terkirim: <b>${gagal} email</b> (Cek log console untuk detail)</p>` : ''}`
            });

        } catch (error) {
            Swal.fire('Error', error.message || 'Terjadi kesalahan sistem.', 'error');
        }
    }

    // Fungsi untuk memproses perubahan status langsung dari Dropdown
    function changeStatusInline(selectElement, id, nim) {
        const apel_id = document.getElementById('apel_id_filter').value;
        const newStatus = selectElement.value;
        const originalStatus = selectElement.getAttribute('data-original');

        if (!apel_id) {
            Swal.fire('Error', 'Jadwal Apel belum dipilih.', 'error');
            selectElement.value = originalStatus;
            return;
        }

        // Tampilkan Popup Loading Bawaan BPS / SweetAlert Standar
        Swal.fire({
            title: 'Memproses...',
            text: 'Menyimpan perubahan presensi.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Langsung ubah warna dropdown agar terasa instan
        updateSelectColor(selectElement, newStatus);

        fetch(`/presensi/update-inline/${id}`, {
            method: 'POST', 
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: newStatus,
                nim: nim,
                apel_id: apel_id,
                _method: 'PUT' 
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Koneksi bermasalah');
            return response.json();
        })
        .then(data => {
            if(data.success) {
                // Update atribut orisinal jika sukses
                selectElement.setAttribute('data-original', newStatus);
                if (id === 'new' && data.new_id) {
                    selectElement.setAttribute('onchange', `changeStatusInline(this, '${data.new_id}', '${nim}')`);
                }

                // Tampilkan Feedback Sukses Standar (Sama seperti Scanner)
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'Error server');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Tampilkan Feedback Gagal Standar
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat memperbarui status.',
            });
            
            // Kembalikan status dropdown ke semula jika gagal
            selectElement.value = originalStatus;
            updateSelectColor(selectElement, originalStatus);
        });
    }

    // Fungsi warna tetap dipertahankan agar tabel terlihat rapi
    function updateSelectColor(element, status) {
        element.className = element.className.replace(/bg-\w+-100/g, '').replace(/text-\w+-700/g, '');
        let colorClass = '';
        switch(status) {
            case 'hadir': colorClass = 'bg-green-100 text-green-700'; break;
            case 'tidak_hadir': colorClass = 'bg-red-100 text-red-700'; break;
            case 'terlambat': colorClass = 'bg-yellow-100 text-yellow-700'; break;
            case 'izin': colorClass = 'bg-purple-100 text-purple-700'; break;
            case 'sakit': colorClass = 'bg-blue-100 text-blue-700'; break;
        }
        element.className = element.className + ' ' + colorClass;
    }
</script>
</x-layout>