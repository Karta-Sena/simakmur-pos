// FILE: assets/js/api.js

const API = {
    // Sesuaikan dengan URL project kamu
    // Jika config.php kamu pakai localhost/simakmur-pos, maka ini sudah benar.
    baseURL: '/simakmur-pos/api',

    // Fungsi Request Internal
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        };

        try {
            const response = await fetch(url, { ...options, headers });
            
            // Handle jika server error (500/404)
            if (!response.ok) {
                throw new Error(`API Error: ${response.statusText}`);
            }

            const result = await response.json();
            return result; // Ekspektasi format: { status: 'success', data: ... }

        } catch (error) {
            console.error('API Fetch Error:', error);
            // Return null atau object error agar aplikasi tidak crash
            return { status: 'error', message: error.message };
        }
    },

    // Shortcut GET
    get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    },

    // Shortcut POST (Kirim Data)
    post(endpoint, body) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(body)
        });
    }
};