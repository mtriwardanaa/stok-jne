<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Opname - {{ $monthName }} {{ $year }}</title>
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
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .bg-highlight { background: #e3f2fd; font-weight: bold; }
        .footer { margin-top: 40px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 30px; }
        .signature { width: 200px; text-align: center; }
        .signature .line { border-top: 1px solid #333; margin-top: 60px; padding-top: 5px; }
        .signature .name { font-weight: bold; margin-top: 5px; }
        @media print {
            body { padding: 0; font-size: 10px; }
            .no-print { display: none; }
            th, td { padding: 4px 6px; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Print Report
        </button>
        <button onclick="history.back()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            ✕ Tutup
        </button>
    </div>

    <div class="header">
        <h1>LAPORAN STOK OPNAME</h1>
        <p>PT. JNE EXPRESS</p>
    </div>

    <div class="info">
        <p><strong>Periode:</strong> {{ $monthName }} {{ $year }}</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Kode</th>
                <th style="width: 30%;">Nama Barang</th>
                <th style="width: 8%;" class="text-center">Satuan</th>
                <th style="width: 10%;" class="text-center">Stok Awal</th>
                <th style="width: 10%;" class="text-center">Masuk</th>
                <th style="width: 10%;" class="text-center">Keluar</th>
                <th style="width: 15%;" class="text-center bg-highlight">Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['kode'] }}</td>
                    <td>{{ $item['nama'] }}</td>
                    <td class="text-center">{{ $item['satuan'] }}</td>
                    <td class="text-center">{{ $item['stok_awal'] }}</td>
                    <td class="text-center text-green">{{ $item['masuk'] > 0 ? '+' . $item['masuk'] : '-' }}</td>
                    <td class="text-center text-red">{{ $item['keluar'] > 0 ? '-' . $item['keluar'] : '-' }}</td>
                    <td class="text-center bg-highlight">{{ $item['stok_akhir'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p style="margin-bottom: 10px;">Demikian laporan stok opname ini dibuat dengan sebenar-benarnya.</p>
        
        <div class="signatures">
            <div class="signature">
                <p>Koordinator GA</p>
                <div class="line"></div>
                <p class="name">{{ $koordinator ?: '........................' }}</p>
            </div>
            <div class="signature">
                <p>Audit Internal</p>
                <div class="line"></div>
                <p class="name">{{ $auditor ?: '........................' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
