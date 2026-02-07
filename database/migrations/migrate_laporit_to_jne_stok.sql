-- =========================================
-- MIGRATION: jne_old → jne_stok
-- Created: 2026-02-07
-- =========================================
-- BACKUP DULU SEBELUM EKSEKUSI!
-- mysqldump -u root -p jne_old > backup_jne_old.sql
-- mysqldump -u root -p jne_stok > backup_jne_stok.sql
-- =========================================

SET SESSION sql_mode = '';
SET FOREIGN_KEY_CHECKS = 0;

-- =========================================
-- CLEAR TARGET TABLES (optional - uncomment if fresh migration)
-- =========================================
TRUNCATE TABLE jne_stok.stok_barang_keluar_detail;
TRUNCATE TABLE jne_stok.stok_barang_keluar;
TRUNCATE TABLE jne_stok.stok_order_detail;
TRUNCATE TABLE jne_stok.stok_order;
TRUNCATE TABLE jne_stok.stok_barang_harga;
TRUNCATE TABLE jne_stok.stok_barang_masuk_detail;
TRUNCATE TABLE jne_stok.stok_barang_masuk;
TRUNCATE TABLE jne_stok.stok_barang;
TRUNCATE TABLE jne_stok.stok_barang_satuan;
TRUNCATE TABLE jne_stok.stok_supplier;

-- =========================================
-- STEP 1: stok_supplier
-- =========================================
INSERT INTO jne_stok.stok_supplier (id, nama_supplier, created_at, updated_at)
SELECT id, nama_supplier, created_at, updated_at
FROM jne_old.stok_supplier
ON DUPLICATE KEY UPDATE nama_supplier = VALUES(nama_supplier);

-- =========================================
-- STEP 2: stok_barang_satuan
-- =========================================
INSERT INTO jne_stok.stok_barang_satuan (id, nama_satuan, created_at, updated_at)
SELECT id, nama_satuan, created_at, updated_at
FROM jne_old.stok_barang_satuan
ON DUPLICATE KEY UPDATE nama_satuan = VALUES(nama_satuan);

-- =========================================
-- STEP 3: stok_barang
-- =========================================
INSERT INTO jne_stok.stok_barang (
  id, kode_barang, nama_barang, qty_barang, harga_barang, 
  warning_stok, stok_awal, id_barang_satuan, 
  internal, agen, subagen, corporate,
  created_at, updated_at
)
SELECT 
  id, kode_barang, nama_barang, 
  COALESCE(qty_barang, 0) as qty_barang, 
  COALESCE(harga_barang, 0) as harga_barang,
  COALESCE(warning_stok, 0) as warning_stok, 
  COALESCE(stok_awal, 0) as stok_awal, 
  id_barang_satuan,
  COALESCE(internal, 0) as internal, 
  COALESCE(agen, 0) as agen, 
  COALESCE(subagen, 0) as subagen, 
  COALESCE(corporate, 0) as corporate,
  created_at, updated_at
FROM jne_old.stok_barang
ON DUPLICATE KEY UPDATE 
  kode_barang = VALUES(kode_barang),
  nama_barang = VALUES(nama_barang),
  qty_barang = VALUES(qty_barang);

-- =========================================
-- STEP 4: stok_barang_masuk (tanpa id_supplier)
-- =========================================
INSERT INTO jne_stok.stok_barang_masuk (
  id, no_barang_masuk, tanggal, created_by, updated_by,
  created_at, updated_at
)
SELECT 
  id, no_barang_masuk, tanggal, 
  created_by, updated_by,
  created_at, updated_at
FROM jne_old.stok_barang_masuk
ON DUPLICATE KEY UPDATE tanggal = VALUES(tanggal);

-- =========================================
-- STEP 5: stok_barang_masuk_detail (dengan supplier)
-- =========================================
INSERT INTO jne_stok.stok_barang_masuk_detail (
  id, id_barang_masuk, id_barang, id_supplier, 
  qty_barang, harga_barang, created_at, updated_at
)
SELECT 
  id, id_barang_masuk, id_barang,
  COALESCE(id_supplier, 1) as id_supplier,
  COALESCE(qty_barang, 0) as qty_barang,
  CAST(COALESCE(harga_barang, 0) AS DECIMAL(15,2)) as harga_barang,
  created_at, updated_at
FROM jne_old.stok_barang_masuk_detail
ON DUPLICATE KEY UPDATE qty_barang = VALUES(qty_barang);

-- =========================================
-- STEP 6: stok_barang_harga
-- =========================================
INSERT INTO jne_stok.stok_barang_harga (
  id, id_barang_masuk, id_barang, harga_barang, 
  created_at, updated_at
)
SELECT 
  id, id_barang_masuk, id_barang, harga_barang,
  created_at, updated_at
FROM jne_old.stok_barang_harga
ON DUPLICATE KEY UPDATE harga_barang = VALUES(harga_barang);

-- =========================================
-- STEP 7: stok_order
-- =========================================
INSERT INTO jne_stok.stok_order (
  id, no_order, tanggal, tanggal_update, tanggal_approve, tanggal_reject,
  id_divisi, id_kategori,
  created_by, updated_by, approved_by, rejected_by, rejected_text,
  nama_user_request, hp_user_request,
  status,
  created_at, updated_at
)
SELECT 
  id, no_order, tanggal, tanggal_update, tanggal_approve, tanggal_reject,
  id_divisi, id_kategori,
  created_by, updated_by, approved_by, rejected_by, rejected_text,
  nama_user_request, hp_user_request,
  CASE 
    WHEN tanggal_reject IS NOT NULL THEN 'ditolak'
    WHEN tanggal_approve IS NOT NULL THEN 'selesai'
    WHEN tanggal_update IS NOT NULL THEN 'diproses'
    ELSE 'menunggu'
  END as status,
  created_at, updated_at
FROM jne_old.stok_order
ON DUPLICATE KEY UPDATE tanggal = VALUES(tanggal);

-- =========================================
-- STEP 8: stok_order_detail
-- =========================================
INSERT INTO jne_stok.stok_order_detail (
  id, id_order, id_barang, qty_barang, qty_approved, created_at, updated_at
)
SELECT 
  id, id_order, id_barang, 
  COALESCE(qty_barang, 0) as qty_barang, 
  COALESCE(jumlah_approve, 0) as qty_approved,
  created_at, updated_at
FROM jne_old.stok_order_detail
ON DUPLICATE KEY UPDATE qty_barang = VALUES(qty_barang);

-- =========================================
-- STEP 9: stok_barang_keluar
-- =========================================
INSERT INTO jne_stok.stok_barang_keluar (
  id, no_barang_keluar, tanggal, 
  id_divisi, id_kategori, id_agen, id_order,
  nama_user_request, distribusi_sales,
  created_by, updated_by,
  created_at, updated_at
)
SELECT 
  id, no_barang_keluar, tanggal, 
  id_divisi, id_kategori, id_agen, id_order,
  nama_user_request, 
  CASE WHEN distribusi_sales = 1 THEN 'yes' ELSE NULL END,
  created_by, updated_by,
  created_at, updated_at
FROM jne_old.stok_barang_keluar
ON DUPLICATE KEY UPDATE tanggal = VALUES(tanggal);

-- =========================================
-- STEP 10: stok_barang_keluar_detail
-- =========================================
INSERT INTO jne_stok.stok_barang_keluar_detail (
  id, id_barang_keluar, id_barang, qty_barang, created_at, updated_at
)
SELECT 
  id, id_barang_keluar, id_barang, 
  COALESCE(qty_barang, 0) as qty_barang,
  created_at, updated_at
FROM jne_old.stok_barang_keluar_detail
ON DUPLICATE KEY UPDATE qty_barang = VALUES(qty_barang);

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================
-- VERIFICATION
-- =========================================
SELECT 
  'stok_supplier' as tabel, 
  (SELECT COUNT(*) FROM jne_old.stok_supplier) as lama,
  (SELECT COUNT(*) FROM jne_stok.stok_supplier) as baru
UNION ALL SELECT 'stok_barang', 
  (SELECT COUNT(*) FROM jne_old.stok_barang),
  (SELECT COUNT(*) FROM jne_stok.stok_barang)
UNION ALL SELECT 'stok_barang_masuk', 
  (SELECT COUNT(*) FROM jne_old.stok_barang_masuk),
  (SELECT COUNT(*) FROM jne_stok.stok_barang_masuk)
UNION ALL SELECT 'stok_order', 
  (SELECT COUNT(*) FROM jne_old.stok_order),
  (SELECT COUNT(*) FROM jne_stok.stok_order)
UNION ALL SELECT 'stok_barang_keluar', 
  (SELECT COUNT(*) FROM jne_old.stok_barang_keluar),
  (SELECT COUNT(*) FROM jne_stok.stok_barang_keluar);
