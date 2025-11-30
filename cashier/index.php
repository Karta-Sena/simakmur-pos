<?php
// cashier/index.php
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
                <button class="nav-item active" title="Dashboard"><i data-feather="grid"></i></button>
                <button class="nav-item" title="Riwayat"><i data-feather="clock"></i></button>
                <button class="nav-item" title="Laporan"><i data-feather="pie-chart"></i></button>
                <button class="nav-item" title="Keluar" onclick="window.location.href='../index.php'"><i data-feather="log-out"></i></button>
            </div>

            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=Kasir+1&background=cba135&color=fff" alt="User">
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

            <div class="menu-scroll-area">
                <div class="menu-grid-wireframe" id="menuGrid">
                    <div style="padding:40px; text-align:center; grid-column:span 3; color:#888;">
                        Memuat Menu...
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

    <script src="../assets/js/utils.js"></script>
    <script src="../assets/js/api.js"></script>
    <script src="../assets/js/store.js"></script>
    <script src="js/pos.js"></script> </body>
</html>