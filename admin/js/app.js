// FILE: admin/js/app.js

const Admin = {
    init() {
        console.log("Admin Dashboard Start...");
        this.nav('dashboard'); // Load dashboard by default
    },

    // --- 1. ROUTER SEDERHANA ---
    async nav(page) {
        const contentDiv = document.getElementById('mainContent');
        
        // Load HTML Fragment
        try {
            const html = await fetch(`views/${page}.html`).then(res => res.text());
            contentDiv.innerHTML = html;
            
            feather.replace();
            
            // Inisialisasi Logic per Halaman
            if (page === 'dashboard') this.initDashboard();
            if (page === 'products') this.initProducts(); // Nanti kita buat

        } catch (e) {
            contentDiv.innerHTML = "Gagal memuat halaman.";
        }
    },

    // --- 2. LOGIC DASHBOARD ---
    async initDashboard() {
        // Set Tanggal
        document.getElementById('currentDate').innerText = Utils.formatDate();

        // Ambil Data dari API
        const response = await API.get('/reports/stats.php');
        
        if (response && response.status === 'success') {
            const data = response.data;

            // Update Kartu
            document.getElementById('statOmzet').innerText = Utils.formatRp(data.omzet);
            document.getElementById('statTrx').innerText = data.trx_count;

            // Render Chart
            this.renderChart(data.chart.labels, data.chart.data);

            // Render Top Menu
            this.renderTopProducts(data.top_products);
        }
    },

    renderChart(labels, data) {
        const ctx = document.getElementById('peakHourChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Transaksi',
                    data: data,
                    borderColor: '#6B1C23',
                    backgroundColor: 'rgba(107, 28, 35, 0.05)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    },

    renderTopProducts(products) {
        const container = document.getElementById('topProductsList');
        if(products.length === 0) {
            container.innerHTML = '<div style="color:#999">Belum ada penjualan</div>';
            return;
        }

        container.innerHTML = products.map((p, index) => `
            <div class="top-item-row" style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #eee;">
                <div style="display:flex; gap:10px;">
                    <span style="font-weight:bold; color:var(--c-primary)">${index + 1}</span>
                    <span>${p.product_name}</span>
                </div>
                <span style="font-weight:bold; color:#888">${p.qty} Terjual</span>
            </div>
        `).join('');
    },

    async initProducts() {
        const response = await API.get('/products/list.php');
        const tbody = document.getElementById('productTableBody');
        
        if (response && response.status === 'success') {
            tbody.innerHTML = response.data.map(p => `
                <tr>
                    <td><img src="${p.image}" style="width:50px; height:50px; border-radius:8px; object-fit:cover; border:1px solid #ddd;"></td>
                    <td style="font-weight:bold;">${p.name}</td>
                    <td><span class="badge badge-neutral">${p.category_slug}</span></td>
                    <td class="mono-num">${Utils.formatRp(p.price)}</td>
                    <td><span class="badge badge-success">Aktif</span></td>
                    <td>
                        <button class="btn-icon" style="color:red; border-color:red;" onclick="Admin.deleteProduct(${p.id})">
                            <i data-feather="trash-2"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            feather.replace();
        }
    },

    openModal() {
        document.getElementById('productModal').classList.add('active');
    },

    closeModal() {
        document.getElementById('productModal').classList.remove('active');
    },

    async saveProduct(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSave');
        const form = document.getElementById('productForm');
        
        btn.innerText = "Mengupload...";
        btn.disabled = true;

        try {
            // Khusus Upload File, pakai FormData (Bukan JSON biasa)
            const formData = new FormData(form);
            
            // Panggil API (Manual Fetch karena api.js kita defaultnya JSON)
            const response = await fetch('../api/products/create.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();

            if (result.status === 'success') {
                alert("Menu Berhasil Ditambah!");
                this.closeModal();
                this.initProducts(); // Refresh Tabel
                form.reset();
            } else {
                alert("Gagal: " + result.message);
            }
        } catch (error) {
            console.error(error);
            alert("Terjadi kesalahan sistem");
        } finally {
            btn.innerText = "SIMPAN";
            btn.disabled = false;
        }
    },

    async deleteProduct(id) {
        if(!confirm("Yakin hapus menu ini?")) return;

        const response = await API.post('/products/delete.php', { id: id });
        if (response.status === 'success') {
            this.initProducts(); // Refresh tabel
        } else {
            alert("Gagal menghapus");
        }
    }
};

// Start
document.addEventListener('DOMContentLoaded', () => Admin.init());