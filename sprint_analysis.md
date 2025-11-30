# Analisa Mendalam: Transisi Sprint 1 ke Sprint 2
**Project:** SiMakmur POS  
**Status:** Sprint 1 Completed (Customer Interface)  
**Next:** Sprint 2 (Cashier & Order Processing)

## 1. Retrospektif Sprint 1 (Customer Module)

### A. Arsitektur & Code Quality
*   **Pola Desain**: Kita menggunakan pendekatan *Vanilla JS SPA-like* dengan struktur `Model-View-Controller` (MVC) ringan di sisi klien (`app.js` sebagai Controller/View logic, `store.js` sebagai State Management).
*   **Kelebihan**:
    *   **Performa**: Sangat ringan tanpa overhead framework besar (React/Vue). Load time sangat cepat.
    *   **Modularitas**: Pemisahan `customer.css` (Theme), `app.js` (Logic), dan `store.js` (Data) memudahkan maintenance.
    *   **Add-on System**: Implementasi `AddonManager` yang *concurrent* (memuat data paralel dengan produk) adalah keputusan tepat untuk UX yang responsif.
*   **Kelemahan & Hutang Teknis**:
    *   **State Persistence**: Saat ini keranjang belanja tersimpan di memory (`Store.state.cart`). Jika user refresh browser, keranjang hilang. Ini perlu diperbaiki di Sprint 2 atau perbaikan bugfix (menggunakan `localStorage`).
    *   **Hardcoded Values**: Masih ada beberapa nilai hardcoded di CSS/JS yang mungkin perlu dibuat dinamis via config kedepannya.

### B. User Experience (UX) & UI
*   **Visual Identity**: Tema "Deep Red" (`#520000`) & "Ivory" (`#FFFFF0`) memberikan kesan premium/luxury yang kuat, sesuai request.
*   **Flow Pemesanan**:
    1.  Scan QR (Masuk dengan parameter `?meja=X`)
    2.  Pilih Menu -> Modal Pop-up -> Pilih Add-on (Wajib/Opsional)
    3.  Masuk Keranjang -> Checkout -> Generate QR Order.
*   **Critical Point**: Flow "Generate QR Order" di akhir adalah jembatan ke Sprint 2. Customer tidak mengirim data langsung ke database server (POST), melainkan membuat QR code yang berisi data pesanan string (`ORDER|MEJA|TOTAL|TIMESTAMP`).
    *   *Analisa*: Ini metode "Offline-First" yang unik. Kasir harus scan QR dari HP customer untuk input pesanan.
    *   *Resiko*: Jika QR terlalu panjang (banyak item), QR code menjadi sangat padat dan sudah discan. Perlu pertimbangan untuk switch ke metode "Direct API Order" di Sprint 2 jika flow scan ini menyulitkan operasional.

---

## 2. Rencana Strategis Sprint 2 (Cashier & Admin)

Fokus utama Sprint 2 adalah **"Menerima & Memproses Uang"**.

### A. Modul Kasir (Point of Sales)
Ini adalah jantung operasional.
1.  **Order Input Methods**:
    *   **Scan QR**: Kasir scan QR dari HP customer (hasil output Sprint 1). Kita butuh fitur *QR Scanner* via webcam di modul kasir.
    *   **Manual Input**: Kasir input pesanan manual untuk pelanggan *walk-in* tanpa HP.
2.  **Payment Processing**:
    *   Kalkulasi total + pajak/service charge.
    *   Input nominal bayar -> Kalkulasi kembalian.
    *   **Cetak Struk**: Integrasi dengan printer thermal (via browser print dialog atau raw ESC/POS command jika memungkinkan).

### B. Modul Admin (Back-Office)
1.  **Product Management (CRUD)**:
    *   Interface untuk tambah/edit/hapus menu.
    *   **PENTING**: Manajemen Add-on (Sambal/Saos) harus ada UI-nya agar tidak hardcoded di SQL.
2.  **Reporting**:
    *   Rekap penjualan harian/bulanan.

### C. Tantangan Teknis Sprint 2
1.  **Database Schema**: Perlu memastikan tabel `transactions` dan `transaction_details` siap menampung struktur data yang kompleks (termasuk data add-on yang dipilih).
2.  **Security**:
    *   Login system untuk Kasir dan Admin (Session management).
    *   Mencegah akses unauthorized ke halaman admin (`/admin`).

## 3. Rekomendasi Aksi (Immediate Actions)

1.  **Database Migration**: Finalisasi struktur tabel untuk transaksi.
2.  **Auth System**: Buat sistem login sederhana tapi aman (PHP Session + Password Hash).
3.  **Prototype Integration**: Ambil `chasierposprototype.html` dan konversi menjadi modul dinamis PHP.

---
**Kesimpulan**: Sprint 1 sukses membangun "Wajah" toko. Sprint 2 akan membangun "Otak" dan "Dompet" toko. Transisi harus mulus terutama di titik serah terima pesanan (QR Scan vs Direct Order).
