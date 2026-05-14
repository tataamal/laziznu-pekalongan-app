<table>
    <thead>
        <tr>
            <th colspan="9" style="text-align: center; font-weight: bold; font-size: 14pt;">LAPORAN PENTASARUFAN LAZISNU PC NU KABUPATEN PEKALONGAN</th>
        </tr>
        <tr>
            <th colspan="9" style="text-align: center;">Periode: {{ $filters['start_date'] ?? '-' }} s/d {{ $filters['end_date'] ?? '-' }}</th>
        </tr>
        <tr></tr>
        <tr style="background-color: #0d6efd; color: #ffffff;">
            <th style="font-weight: bold; border: 1px solid #000000;">No</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Kode Distribusi</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Tanggal</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Jenis Pilar</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Deskripsi</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Jumlah Penerima Manfaat</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Keterangan</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Jumlah Total Distribusi (Rp)</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Nama Wilayah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($distributions as $index => $item)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000;">{{ $item['distribution_code'] }}</td>
                <td style="border: 1px solid #000000;">{{ $item['date'] }}</td>
                <td style="border: 1px solid #000000;">{{ $item['jenis_pilar'] }}</td>
                <td style="border: 1px solid #000000;">{{ $item['deskripsi'] }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $item['jumlah_penerima_manfaat'] }}</td>
                <td style="border: 1px solid #000000;">{{ $item['keterangan'] }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $item['jumlah_total_distribusi'] }}</td>
                <td style="border: 1px solid #000000;">{{ $item['nama_wilayah'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="7" style="text-align: right; font-weight: bold; border: 1px solid #000000;">Total</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: right;">{{ $distributions->sum('jumlah_total_distribusi') }}</th>
            <th style="border: 1px solid #000000;"></th>
        </tr>
    </tfoot>
</table>
