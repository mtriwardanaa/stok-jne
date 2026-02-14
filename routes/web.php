<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KetersediaanController;


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
        if (auth()->user()->department_id == 10) {
            request()->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        Auth::logout();
        return redirect()->route('login')->withErrors('Anda tidak mempunyak akses ke halaman dashboard')->withInput();
    }

    return back()->withErrors([
        'username' => 'Username atau password salah.',
    ])->onlyInput('username');
})->name('login.store');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return \Inertia\Inertia::location(route('login'));
})->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Barang
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

    // Ketersediaan Barang
    Route::get('/ketersediaan', [KetersediaanController::class, 'index'])->name('ketersediaan.index');
    Route::post('/ketersediaan', [KetersediaanController::class, 'update'])->name('ketersediaan.update');

    // Barang Masuk
    Route::get('/barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
    Route::get('/barang-masuk/create', [BarangMasukController::class, 'create'])->name('barang-masuk.create');
    Route::post('/barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store');
    Route::get('/barang-masuk/{id}', [BarangMasukController::class, 'show'])->name('barang-masuk.show');

    // Barang Keluar
    Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');
    Route::get('/barang-keluar/create', [BarangKeluarController::class, 'create'])->name('barang-keluar.create');
    Route::post('/barang-keluar', [BarangKeluarController::class, 'store'])->name('barang-keluar.store');
    Route::get('/barang-keluar/{id}', [BarangKeluarController::class, 'show'])->name('barang-keluar.show');
    Route::post('/barang-keluar/{id}/generate-invoice', [InvoiceController::class, 'generate'])->name('barang-keluar.generate-invoice');
    Route::put('/invoice/{id}', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/barang-keluar/{id}/invoice', function ($id) {
        $barangKeluar = \App\Models\BarangKeluar::with(['invoice.details.barang.satuan', 'details.barang.satuan', 'order', 'createdUser', 'requestUser'])->findOrFail($id);
        return view('pdf.invoice', compact('barangKeluar'));
    })->name('barang-keluar.invoice');
    Route::get('/barang-keluar/{id}/surat-jalan', function ($id) {
        $barangKeluar = \App\Models\BarangKeluar::with(['details.barang.satuan', 'order', 'createdUser', 'requestUser', 'department', 'group'])->findOrFail($id);
        return view('pdf.surat-jalan', compact('barangKeluar'));
    })->name('barang-keluar.surat-jalan');

    // Order
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');
    Route::post('/order/{order}/approve', [OrderController::class, 'approve'])->name('order.approve');
    Route::post('/order/{order}/reject', [OrderController::class, 'reject'])->name('order.reject');

    // Supplier
    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

    // Stock Opname
    Route::get('/stok-opname', [StokOpnameController::class, 'index'])->name('stok-opname.index');
    Route::post('/stok-opname', [StokOpnameController::class, 'store'])->name('stok-opname.store');
    Route::post('/stok-opname/bulk', [StokOpnameController::class, 'storeBulk'])->name('stok-opname.store-bulk');
    Route::get('/stok-opname/report', [StokOpnameController::class, 'report'])->name('stok-opname.report');

    // Report (Inertia)
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/print-summary', [ReportController::class, 'printSummary'])->name('report.print-summary');
    Route::get('/report/print-opname', [ReportController::class, 'printOpname'])->name('report.print-opname');
});
