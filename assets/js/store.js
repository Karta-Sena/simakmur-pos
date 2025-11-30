// FILE: assets/js/store.js

const Store = {
    // Data Pusat (State)
    state: {
        cart: [],          // Array belanjaan: [{id, name, price, qty, img}, ...]
        customerTable: null, // Nomor meja (untuk customer app)
        user: null         // Data user login
    },

    // --- ACTIONS (Cara mengubah data) ---

    // Tambah Item ke Keranjang
    // Tambah Item ke Keranjang
    addToCart(product, addon = null) {
        // Match by product ID AND addon ID (if present)
        const existingItem = this.state.cart.find(item => {
            if (addon) {
                return item.id === product.id && item.addon?.id === addon.id;
            }
            return item.id === product.id && !item.addon;
        });

        if (existingItem) {
            existingItem.qty++;
        } else {
            const cartItem = { ...product, qty: 1 };

            // Add addon if provided
            if (addon) {
                cartItem.addon = {
                    id: addon.id,
                    name: addon.name,
                    type: addon.type,
                    price: addon.price
                };
                // If addon has price, add to item price
                if (addon.price > 0) {
                    cartItem.price += addon.price;
                }
            }

            this.state.cart.push(cartItem);
        }

        this.notify('cart-updated');
        this.saveLocal();
    },

    // Kurangi Item / Hapus
    updateQty(productId, change) {
        const index = this.state.cart.findIndex(item => item.id === productId);

        if (index !== -1) {
            this.state.cart[index].qty += change;

            // Jika qty jadi 0, hapus item dari array
            if (this.state.cart[index].qty <= 0) {
                this.state.cart.splice(index, 1);
            }

            this.notify('cart-updated');
            this.saveLocal();
        }
    },

    // Hapus Item Spesifik
    removeFromCart(productId) {
        this.state.cart = this.state.cart.filter(item => item.id !== productId);
        this.notify('cart-updated');
        this.saveLocal();
    },

    // Kosongkan Keranjang
    clearCart() {
        this.state.cart = [];
        this.notify('cart-updated');
        this.saveLocal();
    },

    // --- GETTERS (Cara ambil data hitungan) ---

    getCartTotal() {
        return this.state.cart.reduce((total, item) => total + (item.price * item.qty), 0);
    },

    getCartCount() {
        return this.state.cart.reduce((count, item) => count + item.qty, 0);
    },

    // --- SYSTEM (Event Listener) ---

    // Meneriakan Event Custom
    notify(eventName) {
        // Dispatch event ke window agar bisa didengar file JS lain
        window.dispatchEvent(new CustomEvent(eventName, { detail: this.state }));
    },

    // Load data dari LocalStorage saat aplikasi mulai
    init() {
        const savedCart = localStorage.getItem('simakmur_cart');
        const savedTable = sessionStorage.getItem('simakmur_table');

        if (savedCart) this.state.cart = JSON.parse(savedCart);
        if (savedTable) this.state.customerTable = savedTable;

        // Trigger update awal
        this.notify('cart-updated');
    },

    saveLocal() {
        localStorage.setItem('simakmur_cart', JSON.stringify(this.state.cart));
    }
};

// Jalankan init saat file dimuat
Store.init();