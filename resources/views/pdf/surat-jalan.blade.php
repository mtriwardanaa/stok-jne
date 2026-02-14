<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Jalan - {{ $barangKeluar->no_barang_keluar }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; border-bottom: 2px solid #2563eb; padding-bottom: 15px; }
        .company h1 { font-size: 22px; color: #2563eb; margin-bottom: 5px; }
        .company p { color: #666; font-size: 11px; }
        .doc-info { text-align: right; }
        .doc-info h2 { font-size: 18px; color: #333; margin-bottom: 10px; text-transform: uppercase; }
        .doc-info table td { padding: 2px 8px; font-size: 11px; }
        .doc-info td:first-child { color: #666; }
        .parties { display: flex; gap: 40px; margin-bottom: 20px; }
        .party { flex: 1; background: #f8fafc; padding: 12px; border-radius: 6px; }
        .party h3 { font-size: 10px; color: #666; text-transform: uppercase; margin-bottom: 6px; }
        .party p { font-size: 12px; line-height: 1.4; }
        .party strong { font-size: 13px; color: #1e40af; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.items th { background: #1e40af; color: white; padding: 8px 12px; text-align: left; font-size: 11px; text-transform: uppercase; }
        table.items td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; }
        table.items tr:nth-child(even) { background: #f9fafb; }
        .text-center { text-align: center; }
        .notes { background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 12px; margin-bottom: 20px; }
        .notes h4 { color: #b45309; font-size: 11px; margin-bottom: 5px; }
        .notes p { color: #92400e; font-size: 11px; }
        .signatures { display: flex; justify-content: space-around; margin-top: 40px; }
        .signature { text-align: center; width: 140px; }
        .signature p { font-size: 11px; color: #666; }
        .signature-line { border-top: 1px solid #333; margin-top: 50px; padding-top: 5px; font-size: 11px; }
        .footer { margin-top: 30px; text-align: center; color: #9ca3af; font-size: 10px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .checklist { display: flex; gap: 20px; margin-top: 15px; }
        .check-item { display: flex; align-items: center; gap: 5px; font-size: 11px; }
        .check-box { width: 14px; height: 14px; border: 1px solid #333; display: inline-block; }
        @media print { body { padding: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">
            <h1>JNE Main Branch Pontianak</h1>
            <p>Jl. Gusti Hamzah No.35, Kota Pontianak 78115</p>
            <p>Telp: (0561) 560 3111</p>
        </div>
        <div class="doc-info">
            <h2>Surat Jalan</h2>
            <table>
                <tr><td>No</td><td>:</td><td><strong>{{ $barangKeluar->no_barang_keluar }}</strong></td></tr>
                <tr><td>Tanggal</td><td>:</td><td>{{ $barangKeluar->tanggal->format('d M Y') }}</td></tr>
            </table>
        </div>
    </div>

    <div class="parties">
        <div class="party">
            <h3>Pengirim</h3>
            <p><strong>Bagian General Affair</strong></p>
            <p>JNE Main Branch Pontianak</p>
        </div>
        <div class="party">
            <h3>Penerima</h3>
            <p><strong>{{ $barangKeluar->requestUser->name ?? $barangKeluar->nama_user_request ?? '-' }}</strong></p>
            @if($barangKeluar->no_hp)
            <p>HP: {{ $barangKeluar->no_hp }}</p>
            @endif
            <p>{{ $barangKeluar->organization_name }}</p>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th class="text-center" style="width: 80px;">Qty</th>
                <th class="text-center" style="width: 80px;">Satuan</th>
                <th class="text-center" style="width: 80px;">✓</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangKeluar->details as $i => $detail)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $detail->barang?->kode_barang }}</td>
                <td><strong>{{ $detail->barang?->nama_barang }}</strong></td>
                <td class="text-center">{{ $detail->qty_barang }}</td>
                <td class="text-center">{{ $detail->barang?->satuan?->nama_satuan }}</td>
                <td class="text-center"><span class="check-box"></span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="notes">
        <h4>Catatan Penting:</h4>
        <p>Harap periksa kelengkapan barang sebelum menandatangani. Keluhan setelah tanda tangan tidak dapat diproses.</p>
    </div>

    <div class="checklist">
        <div class="check-item"><span class="check-box"></span> Barang sesuai</div>
        <div class="check-item"><span class="check-box"></span> Qty sesuai</div>
        <div class="check-item"><span class="check-box"></span> Kondisi baik</div>
    </div>

    <div class="signatures">
        <div class="signature">
            <p>Dikirim</p>
            <div class="signature-line">{{ $barangKeluar->createdUser?->name ?? '-' }}</div>
        </div>
        <div class="signature">
            <p>Diterima</p>
            <div class="signature-line">(................................)</div>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini sah tanpa materai | Lembar 1: Arsip | Lembar 2: Penerima</p>
        <p>Dicetak: {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
