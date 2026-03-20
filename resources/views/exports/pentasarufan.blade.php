<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: bold; font-size: 14pt;">LAPORAN PENTASARUFAN LAZISNU PC NU KABUPATEN PEKALONGAN</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">Periode: {{ $filters['start_date'] ?? '-' }} s/d {{ $filters['end_date'] ?? '-' }}</th>
        </tr>
        <tr></tr>
        <tr style="background-color: #0d6efd; color: #ffffff;">
            <th style="font-weight: bold; border: 1px solid #000000;">No</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Tanggal</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Kode Transaksi</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Penerima Manfaat</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Keterangan / Program</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Jenis</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Nominal (Rp)</th>
            <th style="font-weight: bold; border: 1px solid #000000;">Wilayah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($distributions as $index => $item)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000;">{{ $item['date'] }}</td>
                <td style="border: 1px solid #000000;">{{ $item['transaction_code'] }}</td>
                <td style="border: 1px solid #000000;">{{ $item['penerima_manfaat'] }}</td>
                <td style="border: 1px solid #000000;">{{ $item['event_name'] }}</td>
                <td style="border: 1px solid #000000;">{{ $item['type'] }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $item['amount'] }}</td>
                <td style="border: 1px solid #000000;">{{ $item['wilayah'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6" style="text-align: right; font-weight: bold; border: 1px solid #000000;">Total</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: right;">{{ $distributions->sum('amount') }}</th>
            <th style="border: 1px solid #000000;"></th>
        </tr>
    </tfoot>
</table>
