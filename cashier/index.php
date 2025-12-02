<?php
// cashier/index.php
session_start();

// Cek Login
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'cashier' && $_SESSION['role'] !== 'admin')) {
    header('Location: login.php');
    exit;
}

require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - SiMakmur POS</title>
    
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    
    <link rel="stylesheet" href="css/cashier.css">
    
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

    <div class="app-layout">
        
        <aside class="sidebar">
            <div class="brand-logo" onclick="window.location.reload()">SM</div>
            
            <div class="nav-menu">
                <button class="nav-item active" id="navDashboard" title="Dashboard" onclick="POS.switchView('dashboard')"><i data-feather="grid"></i></button>
                <button class="nav-item" id="navHistory" title="Riwayat" onclick="POS.switchView('history')"><i data-feather="clock"></i></button>
                <button class="nav-item" id="navReport" title="Laporan" onclick="POS.switchView('report')"><i data-feather="pie-chart"></i></button>
                <button class="nav-item" title="Keluar" onclick="if(confirm('Yakin ingin keluar?')) window.location.href='../api/auth/logout.php'"><i data-feather="log-out"></i></button>
            </div>

            <div class="user-profile" title="<?php echo htmlspecialchars($_SESSION['full_name']); ?>">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name']); ?>&background=cba135&color=fff" alt="User">
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="header-info">
                    <h2 class="font-serif font-black text-maroon">Kasir Sales Point</h2>
                    <p id="currentDateDisplay">Memuat tanggal...</p>
                </div>

                <div class="search-wrapper">
                    <i data-feather="search" class="search-icon"></i>
                    <input type="text" class="search-input" id="searchInput" placeholder="Cari menu (Ctrl+F)...">
                </div>
            </header>

            <div class="category-bar" id="categoryContainer">
                <button class="cat-pill active">Semua</button>
            </div>

            <!-- POS VIEW -->
            <div class="menu-scroll-area" id="posView">
                <div class="menu-grid-wireframe" id="menuGrid">
                    <div style="padding:40px; text-align:center; grid-column:span 3; color:#888;">
                        Memuat Menu...
                    </div>
                </div>
            </div>

            <!-- HISTORY VIEW (Hidden by default) -->
            <div class="history-view" id="historyView" style="display:none; padding: 20px; overflow-y: auto; height: calc(100vh - 140px);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 class="font-serif text-maroon">Riwayat Transaksi Hari Ini</h3>
                    <button class="btn-scan" onclick="POS.loadHistory()" style="width:auto; padding:8px 15px;">
                        <i data-feather="refresh-cw"></i> Refresh
                    </button>
                </div>
                
                <table class="table-luxury" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:2px solid #eee; text-align:left;">
                            <th style="padding:10px;">No. TRX</th>
                            <th style="padding:10px;">Waktu</th>
                            <th style="padding:10px;">Metode</th>
                            <th style="padding:10px; text-align:right;">Total</th>
                            <th style="padding:10px; text-align:center;">Status</th>
                            <th style="padding:10px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="historyList">
                        <tr><td colspan="6" style="text-align:center; padding:20px;">Memuat...</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- REPORT VIEW (Hidden by default) -->
            <div class="report-view" id="reportView" style="display:none; padding: 20px; overflow-y: auto; height: calc(100vh - 140px);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 class="font-serif text-maroon">Laporan Harian</h3>
                    <button class="btn-scan" onclick="POS.loadReport()" style="width:auto; padding:8px 15px;">
                        <i data-feather="refresh-cw"></i> Refresh
                    </button>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:30px;">
                    <div class="stat-card" style="background:#fff; padding:20px; border-radius:12px; box-shadow:var(--shadow-sm); border:1px solid #eee;">
                        <div style="color:#888; font-size:14px; margin-bottom:5px;">Total Penjualan</div>
                        <div style="font-size:24px; font-weight:bold; color:var(--c-primary);" id="rptTotalSales">Rp 0</div>
                    </div>
                    <div class="stat-card" style="background:#fff; padding:20px; border-radius:12px; box-shadow:var(--shadow-sm); border:1px solid #eee;">
                        <div style="color:#888; font-size:14px; margin-bottom:5px;">Total Transaksi</div>
                        <div style="font-size:24px; font-weight:bold; color:var(--c-text-primary);" id="rptTotalTrx">0</div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div style="background:#fff; padding:20px; border-radius:12px; border:1px solid #eee;">
                        <h4 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Metode Pembayaran</h4>
                        <div id="rptMethods">Memuat...</div>
                    </div>
                    <div style="background:#fff; padding:20px; border-radius:12px; border:1px solid #eee;">
                        <h4 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Produk Terlaris</h4>
                        <div id="rptTopProducts">Memuat...</div>
                    </div>
                </div>
            </div>
        </main>

        <aside class="cart-panel">
            <div class="cart-header">
                <div class="order-info">
                    <h3 class="font-serif text-maroon">Pesanan Baru</h3>
                    <p><span id="timeDisplay">00:00</span> • Walk-in</p>
                </div>
                <button class="btn-scan" onclick="Scanner.open()">
                    <i data-feather="maximize"></i> SCAN QR
                </button>
            </div>

            <div class="cart-list" id="cartList">
                <div class="empty-state">
                    <p>Keranjang Kosong</p>
                </div>
            </div>

            <div class="cart-footer">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="txtSubtotal">Rp 0</span>
                </div>
                <div class="summary-row">
                    <span>Pajak (10%)</span>
                    <span id="txtTax">Rp 0</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="txtTotal">Rp 0</span>
                </div>

                <button class="btn-checkout" id="btnPay" disabled onclick="POS.openPayment()">
                    <span>Bayar Sekarang</span>
                    <i data-feather="arrow-right"></i>
                </button>
            </div>
        </aside>

    </div>

    <div class="modal-overlay" id="modalPayment">
        <div class="modal-card">
            <button class="modal-close" onclick="POS.closeModal('modalPayment')"><i data-feather="x"></i></button>
            
            <h2 class="font-serif font-black text-maroon" style="margin-bottom:20px;">Pembayaran</h2>

            <div class="pay-container">
                <div class="numpad-wrapper">
                    <div class="pay-display" id="payInputDisplay">0</div>
                    <div class="numpad-grid" id="numpadGrid">
                        </div>
                </div>

                <div>
                    <h4 style="margin-bottom:15px;">Metode</h4>
                    <div class="pay-methods">
                        <div class="method-card selected" onclick="POS.setMethod('cash')">Tunai</div>
                        <div class="method-card" onclick="POS.setMethod('qris')">QRIS</div>
                    </div>

                    <div style="margin-top:20px; padding:15px; background:#f9f9f9; border-radius:10px;">
                        <div style="display:flex; justify-content:space-between;">
                            <span>Kembalian:</span>
                            <span id="lblChange" class="font-black text-maroon">Rp 0</span>
                        </div>
                    </div>

                    <button class="btn-block-luxury" style="margin-top:20px;" onclick="POS.processTransaction()">
                        SELESAIKAN
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast" id="toast">
        <i data-feather="check-circle"></i>
        <span id="toastMsg">Berhasil</span>
    </div>

    <!-- Mobile Cart Toggle -->
    <button class="btn-cart-toggle" onclick="POS.toggleCart()">
        <i data-feather="shopping-cart"></i>
        <span class="badge" id="cartBadge">0</span>
    </button>
    <div class="cart-overlay" id="cartOverlay" onclick="POS.toggleCart()"></div>

    <script src="../assets/js/utils.js"></script>
    <script src="../assets/js/api.js"></script>
    <script src="../assets/js/store.js"></script>
    <script src="js/pos.js"></script> </body>
</html>