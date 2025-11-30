<?php require_once '../config.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="A premium vintage food ordering experience.">
    <meta name="theme-color" content="#FFF8E7">
    <title>Kedai Sekar Makmur | Order Menu</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="css/customer.css">
</head>

<body>
    <div class="sticky-header-group">
        <header class="app-header">
            <div class="top-bar-nav">
                <div class="brand-logo-text">
                    KEDAI SEKAR MAKMUR
                    <span class="table-label-small" id="header-table-num">LOADING...</span>
                </div>

                <div class="search-wrapper">
                    <div class="search-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <input type="text" id="search-input" class="search-input-luxury" placeholder="Cari menu favorit...">
                </div>
            </div>

            <div class="divider-wavy-charcoal" aria-hidden="true"></div>

            <div class="hero-headline-split">
                <h1 class="hero-text serif">Hallo,</h1>
                <h1 class="hero-text serif font-black accent-underline">Selamat Datang di Kedai Sekar Makmur</h1>
            </div>
        </header>

        <div class="category-wrapper">
            <nav class="category-scroll-container" id="category-container" role="tablist">
                <div class="skeleton-pulse sk-pill show-on-load" style="width: 90px; flex-shrink: 0; height: 40px;"></div>
                <div class="skeleton-pulse sk-pill show-on-load" style="width: 110px; flex-shrink: 0; height: 40px;"></div>
            </nav>
        </div>
    </div>

    <main class="main-content-scroll is-loading" id="main-content">
        <div class="menu-grid-wrapper">
            <div class="menu-grid-wireframe" id="menu-grid" role="list">
                <div class="menu-card-luxury show-on-load">
                    <div class="skeleton-pulse" style="width: 70%; height: 24px; border-radius: 4px; margin-bottom: 10px;"></div>
                    <div class="skeleton-pulse" style="width: 100%; height: 120px; border-radius: 50%;"></div>
                </div>
                <div class="menu-card-luxury show-on-load">
                    <div class="skeleton-pulse" style="width: 70%; height: 24px; border-radius: 4px; margin-bottom: 10px;"></div>
                    <div class="skeleton-pulse" style="width: 100%; height: 120px; border-radius: 50%;"></div>
                </div>
            </div>
        </div>
    </main>

    <div class="fab-cart-luxury" id="fab-cart" role="button" aria-label="Open Cart" onclick="App.toggleCart(true)" style="display:none;">
        <div class="cart-badge-count" id="cart-count">0</div>
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
    </div>

    <div class="app-overlay" id="app-overlay" aria-hidden="true"></div>

    <aside class="sidebar-cart" id="sidebar-cart" aria-modal="true" role="dialog">
        <div class="sidebar-header">
            <h2 class="serif font-bold text-luxury" style="font-size: 22px;">Pesanan Anda</h2>
            <div class="btn-close-sidebar" onclick="App.toggleCart(false)" role="button">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>
        </div>
        <div class="sidebar-body-scroll" id="cart-items-container"></div>
        <div class="sidebar-footer">
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px; align-items: center;">
                <span class="serif font-bold text-luxury" style="font-size: 18px;">Total Pembayaran</span>
                <span id="cart-total-price" class="serif font-black text-accent" style="font-size: 22px;">Rp 0</span>
            </div>
            <button class="btn-block-luxury" id="btn-checkout-start" onclick="App.checkout()">
                Checkout
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </div>
    </aside>

    <div class="modal-sheet-product" id="product-modal" aria-modal="true" role="dialog">
        <div class="modal-indicator"></div>
        <img id="pm-img" class="modal-hero-img" src="" alt="Product Image">
        <h2 id="pm-title" class="serif font-black text-luxury" style="font-size: 26px; margin-bottom: 12px; line-height: 1.2;"></h2>
        <p id="pm-desc" style="font-size: 15px; color: var(--c-text-muted); line-height: 1.7; margin-bottom:30px;"></p>
        <div id="addon-section" style="display:none; margin-bottom: 20px;">
            <h4 class="serif" style="font-size: 16px; margin-bottom: 10px; color: var(--c-primary);">Pilih Tambahan:</h4>
            <div id="addon-options" style="display: flex; flex-direction: column; gap: 8px;"></div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px;">
            <span id="pm-price" class="serif font-black text-accent" style="font-size: 28px;"></span>
            <button id="pm-add-btn" class="btn-block-luxury" style="width: auto; padding-left: 35px; padding-right: 35px; font-size: 15px;">Tambahkan</button>
        </div>
    </div>

    <div class="modal-receipt" id="qr-modal">
        <div class="btn-close-receipt" onclick="App.closeAll()">✕</div>
        <div class="receipt-top" style="text-align: center;">
            <div class="brand-logo-text" style="font-size: 20px; margin-bottom: 5px;">Kedai Sekar Makmur</div>
            <p style="font-size: 10px; color: #888; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 20px;">OFFICIAL RECEIPT</p>
            <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--c-text-body-dark); font-weight: bold; margin-bottom: 5px;">
                <span id="qr-table-info">MEJA ?</span>
                <span id="qr-date">--/--/--</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: #999;">
                <span>Order ID: #SCAN</span>
                <span id="qr-time">--:--</span>
            </div>
            <div class="dashed-divider"></div>
            <h3 class="serif font-black" style="font-size: 16px; color: var(--c-primary); margin-bottom: 15px;">SCAN TO ORDER</h3>
            <div style="padding: 10px; border: 2px solid #333; display: inline-block; border-radius: 10px;">
                <img id="qr-image" src="" style="width: 160px; height: 160px; object-fit: contain; display: block;">
            </div>
        </div>
        <div class="receipt-bottom" style="text-align: center;">
            <div class="status-pulse">
                <div class="status-dot"></div>
                Menunggu Konfirmasi Kasir...
            </div>
            <div class="dashed-divider"></div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 14px; color: #555; font-weight: bold;">TOTAL</span>
                <h2 id="qr-total-amount" class="serif font-black" style="font-size: 24px; color: var(--c-primary); margin: 0;">Rp 0</h2>
            </div>
            <p style="font-size: 10px; color: #aaa; margin-top: 15px;">Terima kasih atas kunjungan Anda</p>
        </div>
    </div>

    <div class="toast-notification" id="toast">
        <svg class="toast-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span id="toast-msg" style="font-weight: 600; letter-spacing: 0.5px;">Berhasil!</span>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    
    <script src="../assets/js/utils.js"></script>
    <script src="../assets/js/api.js"></script>
    <script src="../assets/js/store.js"></script>
    <script src="js/app-addon.js"></script>
    <script src="js/app.js"></script>

</body>
</html>