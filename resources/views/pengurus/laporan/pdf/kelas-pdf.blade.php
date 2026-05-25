<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header h2 { margin: 0; font-size: 14px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-size: 9px; text-align: center; }
        .text-center { text-align: center; }
        .summary-table th { text-align: left; }
        .recap-table .date-col { font-size: 8px; }
        .recap-table .status { font-weight: bold; font-size: 11px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Kehadiran Bulanan</h1>
        <h2>Kelas: {{ $kelas->nama_kelas }}</h2>
        {{-- PERBAIKAN: Menggunakan format() yang lebih aman --}}
        <p>Bulan: {{ \Carbon\Carbon::create(null, $bulan, 1)->format('F') }} {{ $tahun }}</p>
    </div>

    <h3>Ringkasan Kehadiran Siswa</h3>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Nama Siswa</th>
                <th class="text-center">Hadir</th>
                <th class="text-center">Sakit</th>
                <th class="text-center">Izin</th>
                <th class="text-center">Alfa</th>
                <th class="text-center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekapSiswa as $rekap)
                <tr>
                    <td>{{ $rekap['siswa']->nama }}</td>
                    <td class="text-center">{{ $rekap['hadir'] }}</td>
                    <td class="text-center">{{ $rekap['sakit'] }}</td>
                    <td class="text-center">{{ $rekap['izin'] }}</td>
                    <td class="text-center">{{ $rekap['alfa'] }}</td>
                    <td class="text-center">{{ $rekap['persentase'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="margin-top: 30px;">Rekapitulasi Kehadiran Harian</h3>
    <table class="recap-table">
        <thead>
            <tr>
                <th>Nama Siswa</th>
                @foreach ($daftarHari as $hari)
                    <th class="date-col">
                        <div>{{ $hari->format('d') }}</div>
                        {{-- PERBAIKAN: Menggunakan format() yang lebih aman --}}
                        <div>{{ $hari->format('D') }}</div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rekapSiswa as $rekap)
                <tr>
                    <td>{{ $rekap['siswa']->nama }}</td>
                    @foreach ($daftarHari as $hari)
                        <td class="text-center status">
                            @php
                                $status = $rekap['riwayat_harian'][$hari->toDateString()] ?? 'kosong';
                                $char = '-';
                                if ($status == 'hadir') { $char = 'H'; }
                                if ($status == 'sakit') { $char = 'S'; }
                                if ($status == 'izin') { $char = 'I'; }
                                if ($status == 'alfa') { $char = 'A'; }
                            @endphp
                            {{ $char }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>

