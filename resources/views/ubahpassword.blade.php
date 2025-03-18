<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="/build/assets/app-DeHizUAL.css">
    <script type="module" src="/build/assets/app-DEdkgso6.js"></script>
    <title>Ubah Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome for Eye Icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body class="font-sans">
    <div class="flex min-h-screen flex-col justify-center items-center px-6 py-12 bg-gray-100 relative">
        <!-- Container Putih -->
        <div class="bg-white w-3/4 lg:w-1/3 rounded-lg shadow-lg z-10">
            <div class="sm:mx-auto sm:w-full sm:max-w-sm p-6 flex items-center">
                <!-- Kolom kiri untuk h2 dan h3 -->
                <div class="flex flex-col">
                    <h2 class="text-2xl font-bold tracking-tight text-red-800 mb-2">
                        SIMPPEL
                    </h2>
                    <h4 class="text-base text-blue-900 font-bold">
                        Sistem Pencatatan dan Pelaporan Pelanggaran SPD
                    </h4>
                </div>

                <!-- Gambar di kanan -->
                <img class="h-20 w-auto" src="{{ asset('img/logospd.png') }}" alt="Logo SPD">
            </div>
        </div>

        <!-- Container Kuning -->
        <div class="bg-yellow-300 w-3/4 lg:w-1/3 rounded-lg shadow-lg -mt-4 z-0">
            <div class="p-6 sm:mx-auto sm:w-full sm:max-w-sm">

                <!-- Peringatan Kesalahan Input Password -->
                @if ($errors->any())
                    <div class="p-4 mt-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                        role="alert">
                        <ul>
                            @foreach ($errors->all() as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-4" action="" method="POST">
                    @csrf

                    <!-- Password Lama-->
                    <div>
                        <label for="password" class="block text-xs font-bold text-white pt-8 pb-1">Password Lama</label>
                        <div class="mt-1 relative">
                            <input type="password" name="password" id="password" autocomplete="current-password"
                                required
                                class="block w-full rounded-md bg-slate-100 px-[8px] py-[4px] text-[14px] text-gray-900 outline outline-1 outline-gray-300 placeholder:text-gray-400 focus:ring focus:ring-indigo-300 focus:outline-none">
                            <button type="button" id="togglePassword1" class="absolute right-2 top-2">
                                <i class="fas fa-eye text-gray-500"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Baru-->
                    <div>
                        <label for="newpassword" class="block text-xs font-bold text-white">Password Baru</label>
                        <div class="mt-1 relative">
                            <input type="password" name="newpassword" id="newpassword" autocomplete="current-password"
                                required
                                class="block w-full rounded-md bg-slate-100 px-[8px] py-[4px] text-[14px] text-gray-900 outline outline-1 outline-gray-300 placeholder:text-gray-400 focus:ring focus:ring-indigo-300 focus:outline-none">
                            <button type="button" id="togglePassword2" class="absolute right-2 top-2">
                                <i class="fas fa-eye text-gray-500"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password Baru-->
                    <div>
                        <label for="cnewpassword" class="block text-xs font-bold text-white">Konfirmasi Password
                            Baru</label>
                        <div class="mt-1 relative pb-8">
                            <input type="password" name="cnewpassword" id="cnewpassword" autocomplete="current-password"
                                required
                                class="block w-full rounded-md bg-slate-100 px-[8px] py-[4px] text-[14px] text-gray-900 outline outline-1 outline-gray-300 placeholder:text-gray-400 focus:ring focus:ring-indigo-300 focus:outline-none">

                            <button type="button" id="togglePassword3" class="absolute right-2 top-2">
                                <i class="fas fa-eye text-gray-500"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button (Kecil dan di pojok kanan) -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="flex justify-center items-center rounded-xl bg-blue-700 px-6 py-1.5 text-xs font-semibold text-white shadow hover:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Ubah
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.getElementById('togglePassword1').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;

            const icon = this.querySelector('i');
            if (passwordField.type === 'password') {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });

        document.getElementById('togglePassword2').addEventListener('click', function() {
            const passwordField = document.getElementById('newpassword');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;

            const icon = this.querySelector('i');
            if (passwordField.type === 'password') {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });

        document.getElementById('togglePassword3').addEventListener('click', function() {
            const passwordField = document.getElementById('cnewpassword');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;

            const icon = this.querySelector('i');
            if (passwordField.type === 'password') {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    </script>
</body>

</html>
