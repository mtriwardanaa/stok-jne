<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Summary Pengeluaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 10px; color: #333; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #333; }
        .header h1 { font-size: 14px; font-weight: bold; margin-bottom: 3px; }
        .header p { font-size: 10px; color: #666; }
        .info { margin-bottom: 10px; font-size: 9px; }
        .info p { margin: 2px 0; }
        .info strong { display: inline-block; width: 90px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 4px 5px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; font-size: 8px; text-transform: uppercase; white-space: nowrap; }
        td { font-size: 8px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .grandtotal { background: #e3f2fd; font-weight: bold; }
        .footer { margin-top: 20px; display: flex; justify-content: space-between; }
        .signature { width: 150px; text-align: center; font-size: 9px; }
        .signature .line { border-top: 1px solid #333; margin-top: 50px; padding-top: 3px; }
        .nowrap { white-space: nowrap; }
        @media print {
            body { padding: 5px; font-size: 8px; }
            .no-print { display: none; }
            th, td { padding: 2px 3px; font-size: 7px; }
            th { font-size: 6px; }
        }
        @page { size: landscape; margin: 10mm; }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">
            🖨️ Print Report
        </button>
        <button onclick="window.close(); if(!window.closed) history.back();" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px; font-size: 12px;">
            ✕ Tutup
        </button>
    </div>

    <div class="header">
        <h1>LAPORAN PENGELUARAN BARANG</h1>
        <p>PT. JNE EXPRESS</p>
    </div>

    <div class="info">
        <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
        <p><strong>Filter:</strong> {{ $filterLabel }}</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d M Y H:i') }}</p>
    </div>

    @if(!empty($data))
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 3%;">No</th>
                    <th class="nowrap" style="width: 8%;">Tgl Keluar</th>
                    <th style="width: 10%;">No BK</th>
                    <th style="width: 8%;">Kode Brg</th>
                    <th style="width: 18%;">Nama Barang</th>
                    <th class="text-center" style="width: 5%;">Sat</th>
                    <th style="width: 15%;">Penerima</th>
                    <th class="nowrap" style="width: 8%;">Tgl Request</th>
                    <th class="text-center" style="width: 4%;">Qty</th>
                    <th class="text-right nowrap" style="width: 9%;">Harga</th>
                    <th class="text-right nowrap" style="width: 10%;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="nowrap">{{ \Carbon\Carbon::parse($item['tanggal_keluar'])->format('d/m/Y') }}</td>
                        <td>{{ $item['no_barang_keluar'] }}</td>
                        <td>{{ $item['kode_barang'] }}</td>
                        <td>{{ $item['nama_barang'] }}</td>
                        <td class="text-center">{{ $item['satuan'] }}</td>
                        <td>{{ $item['penerima'] }}</td>
                        <td class="nowrap">{{ \Carbon\Carbon::parse($item['tanggal_request'])->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $item['qty'] }}</td>
                        <td class="text-right nowrap">{{ number_format($item['harga'], 0, ',', '.') }}</td>
                        <td class="text-right nowrap">{{ number_format($item['nilai'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="grandtotal">
                    <td colspan="8" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{{ $totalQty }}</strong></td>
                    <td></td>
                    <td class="text-right"><strong>{{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 40px; color: #666;">Tidak ada data pengeluaran untuk periode yang dipilih</p>
    @endif

    <div class="footer" style="margin-top: 30px;">
        <div class="signature">
            <p>Dibuat Oleh,</p>
            <div class="line">Admin GA</div>
        </div>
        <div class="signature">
            <p>Mengetahui,</p>
            <div class="line">Kepala Bagian GA</div>
        </div>
    </div>
</body>
</html>
