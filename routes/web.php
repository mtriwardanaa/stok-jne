<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Order\Index as OrderIndex;
use App\Livewire\Order\Detail as OrderDetail;
use App\Livewire\Barang\Index as BarangIndex;
use App\Livewire\BarangMasuk\Index as BarangMasukIndex;
use App\Livewire\BarangMasuk\Create as BarangMasukCreate;
use App\Livewire\BarangMasuk\Detail as BarangMasukDetail;
use App\Livewire\BarangKeluar\Index as BarangKeluarIndex;
use App\Livewire\BarangKeluar\Create as BarangKeluarCreate;
use App\Livewire\BarangKeluar\Detail as BarangKeluarDetail;
use App\Livewire\Supplier\Index as SupplierIndex;
use App\Livewire\Report\Index as ReportIndex;
use App\Livewire\StokOpname\Index as StokOpnameIndex;
use App\Livewire\StokOpname\Report as StokOpnameReport;


// Public routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    if (auth()->attempt($credentials)) {
        request()->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
        'username' => 'Username atau password salah.',
    ])->onlyInput('username');
})->name('login.store');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', Dashboard::class)->name('dashboard');

    // Order
    Route::get('/order', OrderIndex::class)->name('order.index');
    Route::get('/order/{id}', OrderDetail::class)->name('order.detail');

    // Barang
    Route::get('/barang', BarangIndex::class)->name('barang.index');

    // Barang Masuk
    Route::get('/barang-masuk', BarangMasukIndex::class)->name('barang-masuk.index');
    Route::get('/barang-masuk/create', BarangMasukCreate::class)->name('barang-masuk.create');
    Route::get('/barang-masuk/{id}', BarangMasukDetail::class)->name('barang-masuk.detail');

    // Barang Keluar
    Route::get('/barang-keluar', BarangKeluarIndex::class)->name('barang-keluar.index');
    Route::get('/barang-keluar/create', BarangKeluarCreate::class)->name('barang-keluar.create');
    Route::get('/barang-keluar/{id}', BarangKeluarDetail::class)->name('barang-keluar.detail');
    Route::get('/barang-keluar/{id}/invoice', function ($id) {
        $barangKeluar = \App\Models\BarangKeluar::with(['details.barang.satuan', 'order', 'createdUser', 'requestUser'])->findOrFail($id);
        return view('pdf.invoice', compact('barangKeluar'));
    })->name('barang-keluar.invoice');
    Route::get('/barang-keluar/{id}/surat-jalan', function ($id) {
        $barangKeluar = \App\Models\BarangKeluar::with(['details.barang.satuan', 'order', 'createdUser', 'requestUser'])->findOrFail($id);
        return view('pdf.surat-jalan', compact('barangKeluar'));
    })->name('barang-keluar.surat-jalan');

    // Supplier
    Route::get('/supplier', SupplierIndex::class)->name('supplier.index');

    // Report
    Route::get('/report', ReportIndex::class)->name('report.index');
    Route::get('/report/print-summary', [App\Http\Controllers\ReportController::class, 'printSummary'])->name('report.print-summary');
    Route::get('/report/print-opname', [App\Http\Controllers\ReportController::class, 'printOpname'])->name('report.print-opname');

    // Stock Opname
    Route::get('/stok-opname', StokOpnameIndex::class)->name('stok-opname');
    Route::get('/stok-opname/report', StokOpnameReport::class)->name('stok-opname.report');
});
