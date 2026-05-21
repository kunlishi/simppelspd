<p>Kepada Saudara/i,</p>

<ul>
    <li>Nama: {{ $mahasiswa->nama }}</li>
    <li>NIM: {{ $mahasiswa->nim }}</li>
    {{-- Menggunakan relasi kelas jika ada, atau fallback teks biasa --}}
    <li>Kelas: {{ $mahasiswa->kelas->nama_kelas ?? '-' }}</li>
</ul>

@if($jenis == 'scan')
    <p>Kami telah mencatat presensi Anda pada kegiatan apel. Berikut adalah rincian data presensi Anda:</p>
@elseif($jenis == 'update')
    <p>Kami memberitahukan bahwa terdapat perubahan manual pada data presensi Anda. Berikut adalah rincian data presensi Anda saat ini:</p>
@elseif($jenis == 'alpa')
    <p>Kami mencatat bahwa Anda <strong>TIDAK HADIR</strong> hingga batas waktu yang ditentukan. Berikut adalah rincian kegiatan tersebut:</p>
@endif

<ul>
    <li>Kegiatan: {{ $apel->nama_apel }}</li>
    <li>Tanggal: {{ \Carbon\Carbon::parse($apel->tanggal_apel)->translatedFormat('d F Y') }}</li>
    <li>Status Kehadiran: <strong>{{ $status }}</strong></li>
</ul>

@if($jenis == 'alpa')
    <p style="color: red;"><em>*Jika Anda merasa sudah hadir, harap segera melapor ke petugas SPD dengan membawa bukti yang valid.</em></p>
@endif

<p>
    Jika Anda memiliki pertanyaan atau memerlukan informasi lebih lanjut, silakan menghubungi kami melalui
    email ini.
</p>

<p>Terima kasih atas perhatian dan kerja samanya.</p>

<div class="footer">
    <p>⚜ SATYA DHARMA SISWA ⚜</p>
    <p>➖Official Account 📢 ➖</p>
    <p style="margin: 0;"> Instagram : @spdstis</p>
    <p style="margin: 0;"> X : @spdstis</p>
    <p style="margin: 0;"> Surel : spd@stis.ac.id</p>
    <p> ➖➖➖➖➖➖➖➖➖➖ </p>
    <p>SIMPPEL (Sistem Pencatatan dan Pelaporan Pelanggaran)</p>
</div>