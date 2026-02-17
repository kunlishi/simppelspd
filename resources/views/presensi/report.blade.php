<x-layout>
<style>
    /* Garis pembatas untuk tabel sesuai permintaan */
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

    /* Sticky positioning untuk Nama Mahasiswa */
    table thead th.sticky,
    table tbody td.sticky {
        position: sticky;
        z-index: 10;
    }

    table thead th.sticky.left-0,
    table tbody td.sticky.left-0 {
        left: 0;
        background-color: #f9fafb;
        border-right: 1px solid #d1d5db;
    }

    table tbody td.sticky.left-0 {
        background-color: #ffffff;
        border-right: 1px solid #d1d5db;
    }

    @media screen and (max-width: 768px) {
        table tbody td,
        table thead th {
            white-space: nowrap;
        }
    }

    .dropdown-item-text {
        color: #374151; /* gray-700 */
        font-size: 0.75rem; /* text-xs */
    }
    .dropdown-item-text:hover {
        color: #000000; /* black */
    }

    .hover-effect:hover {
        color: black;
    }
</style>

<div class="p-4 sm:ml-64 mt-8">
    <h3 class="text-2xl font-bold dark:text-white">Laporan Kehadiran Apel</h3>
    
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <div id="exampleWrapper" class="dark:bg-gray-900 p-4">
            <div class="datatable-wrapper no-footer sortable searchable fixed-columns">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-start gap-4 w-full sm:w-auto">
                        <form method="GET" action="{{ route('presensi.report') }}" id="filterForm" class="flex flex-wrap gap-2">
                            <select id="tingkat-filter" name="tingkat"
                                class="text-gray-500 bg-white border border-gray-300 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm p-2 dark:bg-gray-800 dark:text-white"
                                onchange="this.form.submit()">
                                <option value="" disabled selected>Pilih Tingkat</option>
                                @foreach([1,2,3,4] as $t)
                                    <option value="{{ $t }}" {{ request('tingkat') == $t ? 'selected' : '' }}>Tingkat {{ $t }}</option>
                                @endforeach
                            </select>

                            <input id="datepicker-actions" name="tanggal" type="date"
                                value="{{ request('tanggal') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 p-2 dark:bg-gray-700 dark:text-white"
                                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                onchange="this.form.submit()">

                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 20 20" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>
                                </div>
                                <input type="text" id="search-input" name="nama"
                                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-48 bg-gray-50 focus:ring-blue-500"
                                    value="{{ request('nama') }}" placeholder="Cari Nama/NIM" oninput="handleSearch(event)">
                            </div>

                            <button type="button" onclick="window.location.href='{{ route('presensi.report') }}'"
                                class="p-2 text-sm text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg px-4">
                                Hapus Filter
                            </button>
                        </form>
                    </div>

                    <div class="w-full sm:w-auto">
                        <button id="exportDropdownButton" data-dropdown-toggle="exportDropdown" type="button"
                            class="flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-900 hover:bg-gray-100 dark:bg-gray-800 dark:text-white">
                            Export as <svg class="ms-1.5 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
                        </button>
                        <div id="exportDropdown" class="z-20 hidden w-40 bg-white divide-y divide-gray-100 rounded-lg shadow-lg border border-gray-100 dark:bg-gray-700">
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

                <div class="overflow-x-auto">
                    <table id="export-table">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th>Hari/Tanggal</th>
                                <th>NIM</th>
                                <th class="sticky left-0 bg-gray-50 dark:bg-gray-700">Nama Mahasiswa</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Petugas Scanner</th>
                                @if(Auth::user()->role == 'admin') <th>Aksi</th> @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                            <tr class="bg-white border-b dark:bg-gray-800 hover:bg-gray-50">
                                <td class="text-xs font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l, d-m-Y') : 'N/A' }}
                                </td>
                                <td>{{ $row->nim }}</td>
                                <td class="sticky left-0 bg-white dark:bg-gray-800">{{ $row->nama }}</td>
                                <td>{{ $row->kelas ?? '-' }}</td>
                                <td>
                                    <span class="px-2 py-1 rounded text-[10px] font-black {{ $row->status == 'hadir' ? 'bg-green-100 text-green-700' : 
                                    ($row->status == 'terlambat' ? 'bg-yellow-100 text-yellow-700' : ($row->status == 'tidak_hadir' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                                        {{ strtoupper(str_replace('_', ' ', $row->status)) }}
                                    </span>
                                </td>
                                <td class="italic text-[10px] text-gray-400">{{ $row->nama_petugas ?? 'Sistem' }}</td>
                                @if(Auth::user()->role == 'admin')
                                <td class="flex justify-center py-4">
                                    <button class="text-blue-600 hover:underline text-xs">Edit</button>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-4 italic">Tidak ada data tersedia</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $data->appends(request()->query())->links() }}
    </div>
</div>

<script>
    function downloadFile(format) {
        const baseUrl = "{{ route('presensi.download', ['format' => ':format']) }}";
        const tanggal = document.getElementById('datepicker-actions').value;
        const tingkat = document.getElementById('tingkat-filter').value;
        
        if (!tanggal || !tingkat) {
            alert('Silakan pilih tanggal dan tingkat terlebih dahulu!');
            return;
        }
        
        const url = baseUrl.replace(':format', format) + `?tanggal=${tanggal}&tingkat=${tingkat}`;
        window.location.href = url;
    }

    function handleSearch(event) {
        const query = event.target.value.trim().toLowerCase();
        const rows = document.querySelectorAll('#export-table tbody tr');

        rows.forEach((row) => {
            const nim = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
            const name = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
            row.style.display = (nim.includes(query) || name.includes(query)) ? '' : 'none';
        });
    }
</script>
</x-layout>