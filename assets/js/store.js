// FILE: assets/js/store.js

const Store = {
    // Data Pusat (State)
    state: {
        cart: [],          // Array belanjaan: [{id, name, price, qty, img}, ...}
        customerTable: null, // Nomor meja (untuk customer app)
        user: null         // Data user login
    },

    // --- ACTIONS (Cara mengubah data) ---

    // Tambah Item ke Keranjang
    addToCart(product, addon = null) {
        // Generate unique ID untuk cart item
        const uniqueId = addon
            ? `${product.id}_addon_${addon.id}`
            : `${product.id}_no_addon`;

        // Cari apakah sudah ada item dengan uniqueId yang sama
        const existingItem = this.state.cart.find(item => item.uniqueId === uniqueId);

        if (existingItem) {
            // Item sudah ada, tambah quantity
            existingItem.qty++;
        } else {
            // Item baru, buat object baru
            const cartItem = {
                ...product,
                uniqueId: uniqueId,
                qty: 1
            };

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
                    cartItem.price = parseFloat(product.price) + parseFloat(addon.price);
                }
            }

            this.state.cart.push(cartItem);
        }

        this.notify('cart-updated');
        this.saveLocal();
    },

    // Kurangi Item / Hapus
    updateQty(uniqueId, change) {
        const index = this.state.cart.findIndex(item => item.uniqueId === uniqueId);

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
    removeFromCart(uniqueId) {
        this.state.cart = this.state.cart.filter(item => item.uniqueId !== uniqueId);
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