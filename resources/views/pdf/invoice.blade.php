<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $barangKeluar->invoice?->no_invoice ?? $barangKeluar->no_barang_keluar }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; border-bottom: 2px solid #e11d48; padding-bottom: 15px; }
        .company { }
        .company h1 { font-size: 24px; color: #e11d48; margin-bottom: 5px; }
        .company p { color: #666; font-size: 11px; }
        .invoice-info { text-align: right; }
        .invoice-info h2 { font-size: 20px; color: #333; margin-bottom: 10px; }
        .invoice-info table { margin-left: auto; }
        .invoice-info td { padding: 2px 8px; font-size: 11px; }
        .invoice-info td:first-child { color: #666; }
        .parties { display: flex; gap: 40px; margin-bottom: 25px; }
        .party { flex: 1; }
        .party h3 { font-size: 11px; color: #666; text-transform: uppercase; margin-bottom: 8px; }
        .party p { font-size: 12px; line-height: 1.5; }
        .party strong { font-size: 14px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th { background: #f8f9fa; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; color: #666; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; }
        table.items td { padding: 10px 12px; border-bottom: 1px solid #eee; }
        table.items tr:hover { background: #fafafa; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 20px; margin-left: auto; width: 250px; }
        .summary table { width: 100%; }
        .summary td { padding: 6px 10px; }
        .summary .total { font-size: 14px; font-weight: bold; background: #f8f9fa; }
        .footer { margin-top: 40px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #eee; padding-top: 15px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
        .signature { text-align: center; width: 150px; }
        .signature-line { border-top: 1px solid #333; margin-top: 60px; padding-top: 5px; }
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
        <div class="invoice-info">
            <h2>INVOICE</h2>
            <table>
                <tr><td>No Invoice</td><td>:</td><td><strong>{{ $barangKeluar->invoice?->no_invoice ?? $barangKeluar->no_barang_keluar }}</strong></td></tr>
                <tr><td>Tanggal</td><td>:</td><td>{{ ($barangKeluar->invoice?->tanggal_invoice ?? $barangKeluar->tanggal)->format('d M Y') }}</td></tr>
                @if($barangKeluar->order)
                <tr><td>No Order</td><td>:</td><td>{{ $barangKeluar->order->no_order }}</td></tr>
                @endif
                @if($barangKeluar->invoice)
                <tr><td>Status</td><td>:</td><td><strong>{{ strtoupper($barangKeluar->invoice->status) }}</strong></td></tr>
                @endif
            </table>
        </div>
    </div>

    <div class="parties">
        <div class="party">
            <h3>Dari</h3>
            <p><strong>Bagian General Affair</strong></p>
            <p>JNE Main Branch Pontianak</p>
            <p>Kalimantan Barat</p>
        </div>
        <div class="party">
            <h3>Kepada</h3>
            <p><strong>{{ $barangKeluar->requestUser->name ?? $barangKeluar->nama_user_request ?? '-' }}</strong></p>
            @if($barangKeluar?->requestUser?->phone)
            <p>HP: {{ $barangKeluar?->requestUser?->phone }}</p>
            @endif
            <p>{{ $barangKeluar->organization_name }}</p>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Nama Barang</th>
                <th class="text-center" style="width: 80px;">Qty</th>
                <th class="text-center" style="width: 80px;">Satuan</th>
                <th class="text-right" style="width: 120px;">Harga</th>
                <th class="text-right" style="width: 120px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @if($barangKeluar->invoice && $barangKeluar->invoice->details->count() > 0)
                {{-- Use invoice detail prices --}}
                @foreach($barangKeluar->invoice->details as $i => $detail)
                @php 
                    $harga = $detail->harga ?? 0;
                    $subtotal = $detail->qty * $harga;
                    $total += $subtotal;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $detail->barang?->nama_barang }}</strong><br>
                        <span style="color: #999; font-size: 10px;">{{ $detail->barang?->kode_barang }}</span>
                    </td>
                    <td class="text-center">{{ $detail->qty }}</td>
                    <td class="text-center">{{ $detail->barang?->satuan?->nama_satuan }}</td>
                    <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                {{-- Fallback: use barang master prices --}}
                @foreach($barangKeluar->details as $i => $detail)
                @php 
                    $harga = $detail->barang?->harga_barang ?? 0;
                    $subtotal = $detail->qty_barang * $harga;
                    $total += $subtotal;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $detail->barang?->nama_barang }}</strong><br>
                        <span style="color: #999; font-size: 10px;">{{ $detail->barang?->kode_barang }}</span>
                    </td>
                    <td class="text-center">{{ $detail->qty_barang }}</td>
                    <td class="text-center">{{ $detail->barang?->satuan?->nama_satuan }}</td>
                    <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr class="total">
                <td>TOTAL</td>
                <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="signatures">
        <div class="signature">
            <p>Diserahkan oleh,</p>
            <div class="signature-line">{{ $barangKeluar->createdUser?->name ?? '-' }}</div>
        </div>
        <div class="signature">
            <p>Diterima oleh,</p>
            <div class="signature-line">{{ $barangKeluar->requestUser->name ?? $barangKeluar->nama_user_request ?? '-' }}</div>
        </div>
    </div>

    <div class="footer">
        <p>Dicetak pada {{ now()->format('d M Y H:i') }} | Dokumen ini dibuat secara elektronik</p>
    </div>
</body>
</html>
