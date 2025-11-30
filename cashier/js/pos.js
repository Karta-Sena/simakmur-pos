// cashier/js/pos.js

const POS = {
    // State Lokal (Hanya untuk UI Kasir)
    state: {
        currentCategory: 'all',
        searchQuery: '',
        payInput: '0',
        paymentMethod: 'cash',
        products: [] // Nanti diisi dari API
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
    // --- 2. DATA LOADING (UPDATED) ---
    async loadProducts() {
        // Tampilkan loading di grid (Opsional, visual feedback)
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
        const nums = [7,8,9,4,5,6,1,2,3,'C',0,'00'];
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
        if(method !== 'cash') {
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
            items: Store.state.cart,
            total: this.state.grandTotal,
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
                
                // TODO: Panggil Printer Bluetooth di sini nanti
                // Printer.printReceipt(payload);

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

    // --- 5. HELPERS ---
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