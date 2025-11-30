// FILE: customer/js/app-addon.js
// Addon Manager - Handles all addon-related logic

const AddonManager = {
    // State
    addons: [],
    selectedAddon: null,

    // Load addons from API
    async load() {
        try {
            const response = await API.get('/addons/list.php');
            if (response && response.status === 'success') {
                this.addons = response.data;
                console.log('✅ Loaded', this.addons.length, 'addons');
                return true;
            } else {
                throw new Error(response.message || 'Failed to load addons');
            }
        } catch (e) {
            console.error('❌ Failed to load addons:', e);

            // Show user notification
            if (window.App && typeof App.showToast === 'function') {
                App.showToast('⚠️ Gagal memuat pilihan addon');
            }

            // Set fallback empty array
            this.addons = [];
            return false;
        }
    },

    // Show addon selection in modal based on product type
    showSelection(product) {
        let addonSection = document.getElementById('addon-section');
        let addonOptions = document.getElementById('addon-options');

        // FALLBACK: Create elements if missing (Robust Fix)
        if (!addonSection) {
            console.warn('⚠️ Addon section missing, creating dynamically...');
            const descEl = document.getElementById('pm-desc');
            if (descEl && descEl.parentNode) {
                addonSection = document.createElement('div');
                addonSection.id = 'addon-section';
                addonSection.style.display = 'none';
                addonSection.style.marginBottom = '20px';

                const title = document.createElement('h4');
                title.className = 'serif';
                title.style.fontSize = '16px';
                title.style.marginBottom = '10px';
                title.style.color = 'var(--c-primary)';
                title.textContent = 'Pilih Tambahan:';

                addonOptions = document.createElement('div');
                addonOptions.id = 'addon-options';
                addonOptions.style.display = 'flex';
                addonOptions.style.flexDirection = 'column';
                addonOptions.style.gap = '8px';

                addonSection.appendChild(title);
                addonSection.appendChild(addonOptions);

                // Insert after description
                descEl.parentNode.insertBefore(addonSection, descEl.nextSibling);
            }
        }

        // Add loading state
        if (addonSection) {
            addonSection.classList.add('loading');
        }

        // Reset selection
        this.selectedAddon = null;

        // Determine which addons to show
        let addonsToShow = [];
        let title = '';

        if (product.has_sambal_addon == 1) {
            addonsToShow = this.addons.filter(a => a.type === 'sambal');
            title = 'Pilihan Sambal:';
        } else if (product.has_saos_addon == 1) {
            addonsToShow = this.addons.filter(a => a.type === 'saos');
            title = 'Pilihan Saos:';
        }

        // Show/hide section based on addon availability
        if (addonsToShow.length > 0) {
            addonSection.style.display = 'block';

            // Update title
            const titleEl = addonSection.querySelector('h4');
            if (titleEl) titleEl.textContent = title;

            // Render radio buttons with improved touch targets
            addonOptions.innerHTML = addonsToShow.map((addon, idx) => `
                <label style="display: flex; align-items: center; gap: 12px; padding: 16px; 
                    border: 2px solid var(--c-line-subtle); border-radius: 12px; 
                    cursor: pointer; transition: all 0.2s; background: white;"
                    onmouseover="this.style.borderColor='var(--c-primary)'; this.style.background='rgba(82,0,0,0.02)';" 
                    onmouseout="if(!this.querySelector('input').checked) { this.style.borderColor='var(--c-line-subtle)'; this.style.background='white'; }"
                    onclick="this.querySelector('input').checked = true; this.querySelector('input').dispatchEvent(new Event('change'));">
                    <input type="radio" name="addon" value="${addon.id}" 
                        ${idx === 0 ? 'checked' : ''}
                        onchange="AddonManager.select(${addon.id}); 
                            document.querySelectorAll('#addon-options label').forEach(l => { 
                                l.style.borderColor='var(--c-line-subtle)'; 
                                l.style.background='white'; 
                            }); 
                            this.parentElement.style.borderColor='var(--c-primary)';
                            this.parentElement.style.background='rgba(82,0,0,0.05)';"
                        style="width: 28px; height: 28px; cursor: pointer; accent-color: var(--c-primary);">
                    <span style="flex: 1; font-weight: 500; font-size: 15px;">${addon.name}</span>
                    ${addon.price > 0 ?
                    `<span style="color: var(--c-primary); font-weight: 600;">+${Utils.formatRp(addon.price)}</span>` :
                    '<span style="color: var(--c-text-muted); font-size: 12px; font-weight: 600;">GRATIS</span>'}
                </label>
            `).join('');

            // Auto-select first option
            if (addonsToShow.length > 0) {
                this.select(addonsToShow[0].id);
                // Highlight first option
                setTimeout(() => {
                    const firstLabel = addonOptions.querySelector('label');
                    if (firstLabel) {
                        firstLabel.style.borderColor = 'var(--c-primary)';
                        firstLabel.style.background = 'rgba(82,0,0,0.05)';
                    }
                }, 50);
            }

            // Remove loading state after render
            setTimeout(() => {
                if (addonSection) {
                    addonSection.classList.remove('loading');
                }
            }, 100);
        } else {
            addonSection.style.display = 'none';
            addonSection.classList.remove('loading');
        }
    },

    // Select an addon
    select(addonId) {
        this.selectedAddon = this.addons.find(a => a.id === addonId);
        console.log('🎯 Selected addon:', this.selectedAddon?.name);
    },

    // Get current selection
    getSelected() {
        return this.selectedAddon;
    },

    // Clear selection
    clear() {
        this.selectedAddon = null;
    }
};
