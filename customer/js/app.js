// FILE: customer/js/app.js

const App = {
    state: {
        products: [],
        categories: [],
        filter: 'all',
        search: '',
        tableNum: null
    },

    // --- 1. INISIALISASI ---
    init() {
        console.log("App Started...");

        // Cek Parameter URL (?meja=1)
        const params = new URLSearchParams(window.location.search);
        this.state.tableNum = params.get('meja');

        this.setupUI();
        this.loadData();

        // Listener: Saat data keranjang berubah (dari Store.js), update tampilan
        window.addEventListener('cart-updated', () => {
            this.updateCartUI();
        });
    },

    setupUI() {
        const headerTable = document.getElementById('header-table-num');
        const fab = document.getElementById('fab-cart');

        if (this.state.tableNum) {
            // Mode Order (Scan di Meja)
            headerTable.innerText = `MEJA ${this.state.tableNum}`;
            headerTable.style.color = 'var(--c-primary)';

            // Simpan ke Store Global agar sinkron
            Store.state.customerTable = this.state.tableNum;
        } else {
            // Mode View (Scan di Kasir)
            headerTable.innerText = 'MENU DIGITAL';
            fab.style.display = 'none'; // Sembunyikan tombol keranjang
        }

        // Listener Search
        document.getElementById('search-input').addEventListener('input', (e) => {
            this.state.search = e.target.value.toLowerCase();
            this.renderMenu();
        });

        // Listener Klik overlay untuk tutup semua modal
        document.getElementById('app-overlay').addEventListener('click', () => {
            this.closeAll();
        });
    },

    // --- 2. AMBIL DATA DARI SERVER ---
    async loadData() {
        try {
            // Load products AND addons concurrently
            const [productsResponse] = await Promise.all([
                API.get('/products/list.php'),
                AddonManager.load()
            ]);

            if (productsResponse && productsResponse.status === 'success') {
                this.state.products = productsResponse.data;

                // Debug: Log sample image path untuk verifikasi
                if (this.state.products.length > 0) {
                    console.log('✅ Loaded', this.state.products.length, 'products');
                    console.log('📸 Sample image URL:', this.state.products[0].image);
                }

                // Ambil daftar kategori unik dari data produk
                const uniqueCats = [...new Set(this.state.products.map(p => p.category_slug || 'lainnya'))];

                // Format kategori untuk Tab
                this.state.categories = [
                    { id: 'all', label: 'SEMUA' },
                    ...uniqueCats.map(c => ({ id: c, label: c.toUpperCase() }))
                ];

                // Render Tampilan
                this.renderCategories();
                this.renderMenu();

                // Matikan Loading Skeleton
                document.getElementById('main-content').classList.remove('is-loading');

            } else {
                throw new Error("Gagal memuat data");
            }
        } catch (e) {
            console.error(e);
            document.getElementById('menu-grid').innerHTML =
                `<div style="grid-column:1/-1; text-align:center; padding:40px; color:#999">
                    Gagal memuat menu. Periksa koneksi.
                </div>`;
        }
    },

    // --- 3. RENDER KATEGORI ---
    renderCategories() {
        const container = document.getElementById('category-container');
        container.innerHTML = this.state.categories.map(c => `
            <button class="cat-pill-luxury ${c.id === this.state.filter ? 'active' : ''}" 
                onclick="App.setFilter('${c.id}')">
                ${c.label}
            </button>
        `).join('');
    },

    setFilter(id) {
        this.state.filter = id;
        this.renderCategories(); // Re-render agar tombol aktif pindah
        this.renderMenu();
    },

    // --- 4. RENDER GRID MENU (Sesuai Desain Revisi) ---
    renderMenu() {
        const grid = document.getElementById('menu-grid');
        if (!grid) return;

        // Filter Logic
        const filtered = this.state.products.filter(p => {
            const matchCat = this.state.filter === 'all' || (p.category_slug || 'lainnya') === this.state.filter;
            // Gunakan optional chaining (?.) jaga-jaga kalau p.name null
            const matchSearch = p.name?.toLowerCase().includes(this.state.search);
            return matchCat && matchSearch;
        });

        // Handle Kosong
        if (filtered.length === 0) {
            grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:#888;">Menu tidak ditemukan</div>`;
            return;
        }

        // Generate HTML
        grid.innerHTML = filtered.map(p => {

            // Gunakan helper function untuk resolve path gambar
            const imageUrl = this.resolveImageUrl(p.image);

            return `
            <div class="menu-card-luxury" onclick="App.openProductModal(${p.id})">
                <div class="card-header">
                    <h3 class="card-title-luxury">${p.name}</h3>
                    <p class="card-desc-luxury">${p.description || ''}</p>
                </div>
                
                <div class="card-img-center">
                    <div class="img-circle-frame">
                        <img src="${imageUrl}" loading="lazy" onerror="this.src='https://placehold.co/200x200?text=Error'">
                    </div>
                </div>

                <div class="card-footer-actions">
                    <span class="price-label-luxury">${Utils.formatRp(p.price)}</span>
                    ${this.state.tableNum ? `
                    <button class="btn-add-mini" onclick="event.stopPropagation(); App.quickAdd(${p.id})">
                        <i data-feather="plus"></i>
                    </button>` : ''}
                </div>
            </div>
            `;
        }).join('');

        // Refresh Icon Feather
        if (window.feather) feather.replace();
    },

    // --- 5. LOGIKA MODAL & KERANJANG ---

    openProductModal(id) {
        const p = this.state.products.find(x => x.id === id);
        if (!p) return;

        // Isi Data Modal (gunakan resolveImageUrl untuk konsistensi)
        document.getElementById('pm-img').src = this.resolveImageUrl(p.image);
        document.getElementById('pm-title').innerText = p.name;
        document.getElementById('pm-desc').innerText = p.description || '';
        document.getElementById('pm-price').innerText = Utils.formatRp(p.price);

        // Show addon selection if product has addons
        AddonManager.showSelection(p);

        const btnAdd = document.getElementById('pm-add-btn');

        // Cek Mode (View vs Order)
        if (this.state.tableNum) {
            btnAdd.style.display = 'block';
            btnAdd.onclick = () => {
                const selectedAddon = AddonManager.getSelected();
                Store.addToCart(p, selectedAddon);
                this.closeAll();
                this.showToast('Masuk Keranjang');
            };
        } else {
            btnAdd.style.display = 'none';
        }

        // Tampilkan Modal
        document.getElementById('product-modal').classList.add('is-active');
        document.getElementById('app-overlay').classList.add('is-active');
    },

    quickAdd(id) {
        const p = this.state.products.find(x => x.id === id);
        // Quick add doesn't support addon selection - just add without addon
        Store.addToCart(p, null);
        this.showToast('Ditambahkan +1');
    },

    // Update Tampilan Sidebar Keranjang
    updateCartUI() {
        const count = Store.getCartCount();
        const badge = document.getElementById('cart-count');
        const fab = document.getElementById('fab-cart');

        // Update Badge
        badge.innerText = count;
        badge.classList.toggle('is-visible', count > 0);

        // Logic FAB Visibility
        if (this.state.tableNum && count > 0) {
            fab.style.display = 'flex';
        } else {
            fab.style.display = 'none';
        }

        // Render List Item di Sidebar
        const list = document.getElementById('cart-items-container');
        const cart = Store.state.cart;

        if (cart.length === 0) {
            list.innerHTML = `<div style="text-align:center; padding:50px 20px; color:#999;">Keranjang masih kosong</div>`;
        } else {
            list.innerHTML = cart.map(item => `
                <div class="cart-item-luxury">
                    <div class="cart-thumb-frame">
                        <img src="${this.resolveImageUrl(item.image)}">
                    </div>
                    <div class="cart-item-details">
                        <h4 class="cart-item-title">${item.name}</h4>
                        ${item.addon ? `<p style="font-size: 11px; color: var(--c-text-muted); margin: 2px 0;">+ ${item.addon.name}</p>` : ''}
                        <div class="cart-item-price">${Utils.formatRp(item.price)}</div>
                        
                        <div class="qty-control-group">
                            <button class="btn-qty-ctrl" onclick="Store.updateQty(${item.id}, -1)">-</button>
                            <span class="qty-text">${item.qty}</span>
                            <button class="btn-qty-ctrl" onclick="Store.updateQty(${item.id}, 1)">+</button>
                        </div>
                    </div>
                    <div onclick="Store.removeFromCart(${item.id})" style="cursor:pointer; padding:5px; color:#d11;">
                        <i data-feather="trash-2" style="width:16px;"></i>
                    </div>
                </div>
            `).join('');
        }

        // Update Total
        document.getElementById('cart-total-price').innerText = Utils.formatRp(Store.getCartTotal());
        feather.replace();
    },

    // --- 6. CHECKOUT FLOW (GENERATE QR) ---
    checkout() {
        if (Store.state.cart.length === 0) return;

        const total = Store.getCartTotal();
        // Format String QR: ORDER|MEJA|TOTAL|TIMESTAMP
        const qrString = `ORDER|${this.state.tableNum}|${total}|${Date.now()}`;

        // Generate QR Image
        document.getElementById('qr-image').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrString)}`;
        document.getElementById('qr-total-amount').innerText = Utils.formatRp(total);

        // Isi Info Tambahan
        if (document.getElementById('qr-table-info')) {
            document.getElementById('qr-table-info').innerText = `MEJA ${this.state.tableNum}`;
        }
        if (document.getElementById('qr-date')) {
            document.getElementById('qr-date').innerText = Utils.formatDate();
        }
        if (document.getElementById('qr-time')) {
            document.getElementById('qr-time').innerText = Utils.formatTime();
        }

        // Tampilkan Modal QR
        this.toggleCart(false);
        document.getElementById('qr-modal').classList.add('active');
        document.getElementById('app-overlay').classList.add('is-active');
    },

    // --- 7. HELPER UTILITIES ---
    toggleCart(show) {
        const sb = document.getElementById('sidebar-cart');
        const ov = document.getElementById('app-overlay');

        if (show) {
            sb.classList.add('is-active');
            ov.classList.add('is-active');
        } else {
            sb.classList.remove('is-active');
            ov.classList.remove('is-active');
        }
    },

    closeAll() {
        document.getElementById('sidebar-cart').classList.remove('is-active');
        document.getElementById('product-modal').classList.remove('is-active');
        document.getElementById('qr-modal').classList.remove('active');
        document.getElementById('app-overlay').classList.remove('is-active');
    },

    showToast(msg) {
        const t = document.getElementById('toast');
        const msgEl = document.getElementById('toast-msg');
        if (msgEl) msgEl.innerText = msg;

        t.classList.add('is-show');
        setTimeout(() => t.classList.remove('is-show'), 2000);
    },

    // Helper Function: Resolve image URL untuk konsistensi path
    resolveImageUrl(image) {
        if (!image) return 'https://placehold.co/200x200?text=No+Img';
        return image.startsWith('http') ? image : '../uploads/products/' + image;
    }
};

// Start App when DOM Ready
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});