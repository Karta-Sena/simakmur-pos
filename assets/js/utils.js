// FILE: assets/js/utils.js

const Utils = {
    // Format Angka ke Rupiah (Rp 50.000)
    formatRp: (number) => {
        return new Intl.NumberFormat('id-ID', { 
            style: 'currency', 
            currency: 'IDR', 
            minimumFractionDigits: 0 
        }).format(number);
    },

    // Format Tanggal (Kamis, 27 Nov 2025)
    formatDate: (dateObj = new Date()) => {
        return new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        }).format(dateObj);
    },

    // Format Jam (14:30)
    formatTime: (dateObj = new Date()) => {
        return new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        }).format(dateObj);
    },

    // Simulasi Delay (Untuk efek loading)
    delay: (ms) => new Promise(resolve => setTimeout(resolve, ms))
};