<x-layout></x-layout>

    <div class="p-4 sm:ml-64 mt-9">
    <div class="flex justify-center">
        <div class="block max-w-sm p-6 bg-yellow-300 border rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 w-full">
            <h3 class="text-2xl text-center font-bold dark:text-white">Update Jadwal Apel</h3>
            <p class="text-center text-gray-800 dark:text-gray-400 italic opacity-50">(Silakan perbarui data di bawah ini)</p>

            {{-- Form --}}
                <form class="max-w-sm mx-auto pt-6" action="{{ route('admin.apel.update', $apel->id) }}" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    {{-- Sangat Penting: Ambil nilai awal dari variabel $apel --}}
                    <div class="mb-5">
                        <label for="nama_apel" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama / Jenis Apel</label>
                        <input type="text" id="nama_apel" name="nama_apel" value="{{ $apel->nama_apel }}" required
                            class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>

                    <div class="mb-5">
                        <label for="tingkat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tingkat</label>
                        <input type="number" id="tingkat" name="tingkat" value="{{ $apel->tingkat }}" min="0" max="4" required
                            class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>

                    <div class="mb-5">
                        <label for="tanggal_apel" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pelaksanaan</label>
                        <input type="date" id="tanggal_apel" name="tanggal_apel" value="{{ $apel->tanggal_apel }}" required
                            class="shadow bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.apel.index') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">Batal</a>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-900 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script SweetAlert Konfirmasi Update --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Data jadwal akan diperbarui di sistem.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Batal',
                didOpen: () => {
                    const confirmBtn = document.querySelector('.swal2-confirm');
                    const cancelBtn = document.querySelector('.swal2-cancel');
                    
                    confirmBtn.onmouseover = () => {
                        confirmBtn.style.backgroundColor = '#0f52ba';
                    };
                    confirmBtn.onmouseout = () => {
                        confirmBtn.style.backgroundColor = '#3085d6';
                    };
                    
                    cancelBtn.onmouseover = () => {
                        cancelBtn.style.backgroundColor = '#ff5555';
                    };
                    cancelBtn.onmouseout = () => {
                        cancelBtn.style.backgroundColor = '#d33';
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan Data...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    e.target.submit();
                }
            });
        });
    </script>
<x-footer></x-footer>