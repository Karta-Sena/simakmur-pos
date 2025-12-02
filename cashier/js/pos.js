// cashier/js/pos.js

const POS = {
    // State Lokal (Hanya untuk UI Kasir)
    state: {
        currentCategory: 'all',
        searchQuery: '',
        payInput: '0',
        paymentMethod: 'cash',
        products: [], // Nanti diisi dari API
        grandTotal: 0
    },

    // --- 1. INISIALISASI ---
    init() {
        console.log("POS System Starting...");
        this.loadProducts();
        this.setupEventListeners();
        this.setupClock();
        this.renderNumpad();

        // Subscribe ke Perubahan Global Store (Keranjang)
        window.addEventListener('cart-updated', () => {
            this.renderCart();
        });

        feather.replace();
    },

    // --- 2. DATA LOADING ---
    async loadProducts() {
        // Tampilkan loading di grid
        document.getElementById('menuGrid').innerHTML = '<div style="padding:20px;">Memuat data dari server...</div>';

        // PANGGIL API SUNGGUHAN
        const response = await API.get('/products/list.php');

        if (response && response.status === 'success') {
            this.state.products = response.data;
            this.renderMenu(); // Render ulang grid dengan data asli
        } else {
            // Jika gagal ambil data
            document.getElementById('menuGrid').innerHTML = '<div style="color:red; padding:20px;">Gagal mengambil menu. Periksa server.</div>';
            console.error("Gagal load menu:", response);
        }
    },

    // --- 3. RENDERING UI ---
    renderMenu() {
        const grid = document.getElementById('menuGrid');

        // Filter Logic
        const filtered = this.state.products.filter(p => {
            const matchCat = this.state.currentCategory === 'all' || p.cat === this.state.currentCategory;
            const matchSearch = p.name.toLowerCase().includes(this.state.searchQuery.toLowerCase());
            return matchCat && matchSearch;
        });

        if (filtered.length === 0) {
            grid.innerHTML = `<div style="padding:40px; text-align:center; grid-column:span 2;">Tidak ada menu.</div>`;
            return;
        }

        grid.innerHTML = filtered.map(p => `
            <div class="menu-card" onclick="Store.addToCart(${JSON.stringify(p).replace(/"/g, "&quot;")})">
                <div class="img-circle-frame">
                    <img src="${p.img}" loading="lazy" alt="${p.name}">
                </div>
                <div style="margin-top:15px; text-align:center;">
                    <div style="font-weight:bold; color:var(--c-primary); margin-bottom:5px;">${p.name}</div>
                    <div style="color:var(--c-accent-gold); font-size:14px; font-weight:700;">${Utils.formatRp(p.price)}</div>
                </div>
            </div>
        `).join('');
    },

    renderCart() {
        const list = document.getElementById('cartList');
        const cart = Store.state.cart; // Ambil dari Global Store

        // Update Badge (Mobile)
        const totalQty = cart.reduce((acc, item) => acc + item.qty, 0);
        const badge = document.getElementById('cartBadge');
        if (badge) badge.innerText = totalQty;

        if (cart.length === 0) {
            list.innerHTML = `<div class="empty-state" style="text-align:center; margin-top:50px; color:#999;">Keranjang Kosong</div>`;
            document.getElementById('btnPay').disabled = true;
            this.updateTotals(0);
            return;
        }

        document.getElementById('btnPay').disabled = false;

        let subtotal = 0;
        list.innerHTML = cart.map(item => {
            subtotal += item.price * item.qty;
            return `
            <div class="cart-item">
                <img src="${item.img}">
                <div style="flex:1;">
                    <div style="font-weight:bold; font-size:14px;">${item.name}</div>
                    <div style="font-size:12px; color:#888;">${Utils.formatRp(item.price)}</div>
                    <div style="display:flex; align-items:center; gap:10px; margin-top:5px;">
                        <button onclick="Store.updateQty(${item.id}, -1)" style="background:#eee; width:24px; border-radius:50%;">-</button>
                        <span style="font-weight:bold; font-size:13px;">${item.qty}</span>
                        <button onclick="Store.updateQty(${item.id}, 1)" style="background:#eee; width:24px; border-radius:50%;">+</button>
                    </div>
                </div>
                <div style="font-weight:bold;">${Utils.formatRp(item.price * item.qty)}</div>
            </div>
            `;
        }).join('');

        this.updateTotals(subtotal);
    },

    updateTotals(subtotal) {
        const tax = subtotal * 0.1; // PPN 10%
        const total = subtotal + tax;

        this.state.grandTotal = total; // Simpan untuk pembayaran

        document.getElementById('txtSubtotal').innerText = Utils.formatRp(subtotal);
        document.getElementById('txtTax').innerText = Utils.formatRp(tax);
        document.getElementById('txtTotal').innerText = Utils.formatRp(total);
    },

    toggleCart() {
        const panel = document.querySelector('.cart-panel');
        panel.classList.toggle('open');

        const overlay = document.getElementById('cartOverlay');
        if (panel.classList.contains('open')) {
            overlay.classList.add('active');
        } else {
            overlay.classList.remove('active');
        }
    },

    // --- 4. PAYMENT LOGIC ---
    openPayment() {
        this.state.payInput = '0';
        this.updateNumpadDisplay();
        document.getElementById('modalPayment').classList.add('active');
    },

    closeModal(id) {
        document.getElementById(id).classList.remove('active');
    },

    renderNumpad() {
        const nums = [7, 8, 9, 4, 5, 6, 1, 2, 3, 'C', 0, '00'];
        document.getElementById('numpadGrid').innerHTML = nums.map(n => `
            <button class="num-btn" onclick="POS.handleNumpad('${n}')">${n}</button>
        `).join('');
    },

    handleNumpad(val) {
        if (val === 'C') {
            this.state.payInput = '0';
        } else {
            if (this.state.payInput === '0') this.state.payInput = String(val);
            else this.state.payInput += String(val);
        }
        this.updateNumpadDisplay();
    },

    updateNumpadDisplay() {
        const val = parseInt(this.state.payInput);
        document.getElementById('payInputDisplay').innerText = Utils.formatRp(val);

        const change = val - this.state.grandTotal;
        const elChange = document.getElementById('lblChange');

        if (change >= 0) {
            elChange.innerText = Utils.formatRp(change);
            elChange.style.color = 'var(--c-primary)';
        } else {
            elChange.innerText = "Kurang " + Utils.formatRp(Math.abs(change));
            elChange.style.color = 'red';
        }
    },

    setMethod(method) {
        this.state.paymentMethod = method;
        document.querySelectorAll('.method-card').forEach(el => el.classList.remove('selected'));
        // Visual selection logic here (simplified)
        event.target.classList.add('selected');

        // Auto-fill jika Non-Tunai (QRIS/Debit pas bayar)
        if (method !== 'cash') {
            this.state.payInput = String(Math.ceil(this.state.grandTotal));
            this.updateNumpadDisplay();
        }
    },

    async processTransaction() {
        const paid = parseInt(this.state.payInput);

        // Validasi Pembayaran (Khusus Tunai)
        if (this.state.paymentMethod === 'cash' && paid < this.state.grandTotal) {
            alert("Uang pembayaran kurang!");
            return;
        }

        const change = paid - this.state.grandTotal;

        // Siapkan Data Payload
        const payload = {
            cashier_id: 1, // TODO: Get from session after auth
            customer_type: 'walk-in',
            items: Store.state.cart,
            total: this.state.grandTotal, // This is just for validation on server
            payment_method: this.state.paymentMethod,
            cash_received: paid,
            change_amount: change
        };

        // UI Feedback: Loading
        const btn = document.querySelector('.btn-block-luxury');
        const originalText = btn.innerText;
        btn.innerText = "Memproses...";
        btn.disabled = true;

        try {
            // KIRIM KE API
            const response = await API.post('/transactions/create.php', payload);

            if (response && response.status === 'success') {
                this.closeModal('modalPayment');
                this.showToast(`Transaksi ${response.data.transaction_number} Berhasil!`);

                // Print Receipt
                this.printReceipt(response.data);

                Store.clearCart();
                this.state.payInput = '0'; // Reset Numpad
            } else {
                alert("Gagal: " + (response.message || "Unknown Error"));
            }

        } catch (e) {
            console.error(e);
            alert("Terjadi kesalahan koneksi");
        } finally {
            // Reset Tombol
            btn.innerText = originalText;
            btn.disabled = false;
        }
    },

    // --- 5. PRINTING ---
    printReceipt(data) {
        console.log("Printing receipt:", data);

        // Save data to localStorage so receipt page can read it
        localStorage.setItem('lastReceipt', JSON.stringify(data));

        // Open receipt page in new window
        const printWindow = window.open('views/receipt.html', '_blank', 'width=350,height=600');

        if (!printWindow) {
            alert("Pop-up diblokir! Izinkan pop-up untuk mencetak struk.");
        }
    },

    // --- 6. VIEW SWITCHING ---
    switchView(viewName) {
        // Update Nav State
        document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
        if (viewName === 'dashboard') document.getElementById('navDashboard').classList.add('active');
        if (viewName === 'history') document.getElementById('navHistory').classList.add('active');
        if (viewName === 'report') document.getElementById('navReport').classList.add('active');

        // Toggle Views
        document.getElementById('posView').style.display = 'none';
        document.getElementById('historyView').style.display = 'none';
        document.getElementById('reportView').style.display = 'none';
        document.getElementById('categoryContainer').style.display = 'none';

        if (viewName === 'dashboard') {
            document.getElementById('posView').style.display = 'block';
            document.getElementById('categoryContainer').style.display = 'flex';
        } else if (viewName === 'history') {
            document.getElementById('historyView').style.display = 'block';
            this.loadHistory();
        } else if (viewName === 'report') {
            document.getElementById('reportView').style.display = 'block';
            this.loadReport();
        }
    },

    // --- 7. HISTORY LOGIC ---
    async loadHistory() {
        const tbody = document.getElementById('historyList');
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px;">Memuat data...</td></tr>';

        try {
            const response = await API.get('/transactions/history.php');

            if (response && response.status === 'success') {
                if (response.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px;">Belum ada transaksi hari ini.</td></tr>';
                    return;
                }

                tbody.innerHTML = response.data.map(trx => `
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px; font-weight:bold;">${trx.transaction_number}</td>
                        <td style="padding:10px;">${trx.created_at.split(' ')[1]}</td>
                        <td style="padding:10px;">${trx.payment_method.toUpperCase()}</td>
                        <td style="padding:10px; text-align:right; font-weight:bold;">${Utils.formatRp(trx.total_amount)}</td>
                        <td style="padding:10px; text-align:center;">
                            <span style="background:#e6fffa; color:#047857; padding:2px 8px; border-radius:10px; font-size:12px;">${trx.status}</span>
                        </td>
                        <td style="padding:10px;">
                            <button onclick="POS.printReceipt(${JSON.stringify(trx).replace(/"/g, "&quot;")})" style="background:none; border:none; cursor:pointer; color:var(--c-primary);">
                                <i data-feather="printer" style="width:16px;"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');

                feather.replace();
            } else {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:red;">Gagal: ${response.message}</td></tr>`;
            }
        } catch (e) {
            console.error(e);
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:red;">Terjadi kesalahan koneksi.</td></tr>';
        }
    },

    // --- 8. REPORT LOGIC ---
    async loadReport() {
        document.getElementById('rptTotalSales').innerText = 'Memuat...';
        document.getElementById('rptTotalTrx').innerText = '...';
        document.getElementById('rptMethods').innerHTML = 'Memuat...';
        document.getElementById('rptTopProducts').innerHTML = 'Memuat...';

        try {
            const response = await API.get('/reports/daily.php');

            if (response && response.status === 'success') {
                const data = response.data;

                // 1. Summary
                document.getElementById('rptTotalSales').innerText = Utils.formatRp(data.total_sales);
                document.getElementById('rptTotalTrx').innerText = data.total_transactions;

                // 2. Methods
                if (data.payment_methods.length > 0) {
                    document.getElementById('rptMethods').innerHTML = data.payment_methods.map(m => `
                        <div style="display:flex; justify-content:space-between; margin-bottom:10px; border-bottom:1px dashed #eee; padding-bottom:5px;">
                            <span>${m.payment_method.toUpperCase()} (${m.count})</span>
                            <span style="font-weight:bold;">${Utils.formatRp(m.total)}</span>
                        </div>
                    `).join('');
                } else {
                    document.getElementById('rptMethods').innerHTML = '<div style="color:#888;">Belum ada data.</div>';
                }

                // 3. Top Products
                if (data.top_products.length > 0) {
                    document.getElementById('rptTopProducts').innerHTML = data.top_products.map((p, index) => `
                        <div style="display:flex; justify-content:space-between; margin-bottom:10px; border-bottom:1px dashed #eee; padding-bottom:5px;">
                            <span>${index + 1}. ${p.product_name}</span>
                            <span style="font-weight:bold;">${p.qty}x</span>
                        </div>
                    `).join('');
                } else {
                    document.getElementById('rptTopProducts').innerHTML = '<div style="color:#888;">Belum ada data.</div>';
                }

            } else {
                alert("Gagal memuat laporan: " + response.message);
            }
        } catch (e) {
            console.error(e);
            alert("Terjadi kesalahan koneksi saat memuat laporan.");
        }
    },

    // --- 9. HELPERS ---
    showToast(msg) {
        const t = document.getElementById('toast');
        document.getElementById('toastMsg').innerText = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    },

    setupEventListeners() {
        // Search
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.state.searchQuery = e.target.value;
            this.renderMenu();
        });
    },

    setupClock() {
        setInterval(() => {
            document.getElementById('timeDisplay').innerText = Utils.formatTime();
        }, 1000);
        document.getElementById('currentDateDisplay').innerText = Utils.formatDate();
    }
};

// Start App
document.addEventListener('DOMContentLoaded', () => POS.init());