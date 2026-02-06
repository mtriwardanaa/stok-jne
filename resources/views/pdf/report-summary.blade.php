<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Summary Pengeluaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #333; }
        .header h1 { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .header p { font-size: 12px; color: #666; }
        .info { margin-bottom: 15px; }
        .info p { margin: 3px 0; }
        .info strong { display: inline-block; width: 120px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        td { font-size: 11px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .subtotal { background: #f5f5f5; font-weight: bold; }
        .grandtotal { background: #e3f2fd; font-weight: bold; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; }
        .signature { width: 200px; text-align: center; }
        .signature .line { border-top: 1px solid #333; margin-top: 60px; padding-top: 5px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Print Report
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
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

    @if(!empty($grouped))
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Divisi/Partner</th>
                    <th style="width: 35%;">Nama Barang</th>
                    <th style="width: 15%;" class="text-center">Qty</th>
                    <th style="width: 25%;" class="text-right">Nilai (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped as $org)
                    @foreach($org['items'] as $item)
                        <tr>
                            @if($loop->first)
                                <td rowspan="{{ count($org['items']) }}">{{ $org['name'] }}</td>
                            @endif
                            <td>{{ $item['nama'] }}</td>
                            <td class="text-center">{{ $item['qty'] }}</td>
                            <td class="text-right">{{ number_format($item['nilai'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal">
                        <td colspan="2">Subtotal {{ $org['name'] }}</td>
                        <td class="text-center">{{ $org['total_qty'] }}</td>
                        <td class="text-right">{{ number_format($org['total_nilai'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="grandtotal">
                    <td colspan="2"><strong>GRAND TOTAL</strong></td>
                    <td class="text-center"><strong>{{ $totalQty }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 40px; color: #666;">Tidak ada data pengeluaran untuk periode yang dipilih</p>
    @endif

    <div class="footer" style="margin-top: 50px;">
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
