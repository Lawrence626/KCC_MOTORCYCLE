// Global application entrypoint for Vite.
// Unified Product Image Resolver & Synchronizer across all modules

window.getProductDefaultImage = function(product) {
    if (!product) return '';
    const brand = String(product.brand || '').trim().toUpperCase();
    const desc = String(product.product_description || product.description || product.category || '').trim().toUpperCase();
    const name = String(product.product_name || product.name || '').trim().toUpperCase();
    const compat = String(product.compatibility || '').trim().toUpperCase();
    const sku = String(product.sku || '').trim().toUpperCase();

    // Check for Apido Brand / Pipe
    const isApido = brand.includes('APIDO') || name.includes('APIDO') || sku.includes('APIDO');
    const isPipe = desc.includes('PIPE') || name.includes('PIPE') || compat.includes('PIPE') || sku.includes('PIPE') ||
                   desc.includes('EXHAUST') || name.includes('EXHAUST') || compat.includes('EXHAUST') || sku.includes('EXHAUST') ||
                   desc.includes('MUFFLER') || name.includes('MUFFLER') || compat.includes('MUFFLER') || sku.includes('MUFFLER');

    if (isApido || (isPipe && brand.includes('APIDO'))) {
        const apidoImages = [
            '/images/products/apido_pipe_1.png',
            '/images/products/apido_pipe_2.png',
            '/images/products/apido_pipe_3.png'
        ];
        const seedStr = String(product.id || product.product_id || '') + String(product.name || product.product_name || product.sku || '');
        let hash = 0;
        for (let i = 0; i < seedStr.length; i++) {
            hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
        }
        return apidoImages[Math.abs(hash) % apidoImages.length];
    }

    // Check for KVIN Brand / Pipe
    const isKvin = brand.includes('KVIN') || brand.includes('K-VIN') || brand.includes('K VIN') ||
                   name.includes('KVIN') || name.includes('K-VIN') || name.includes('K VIN') ||
                   sku.includes('KVIN') || sku.includes('K-VIN');

    if (isKvin) {
        const kvinImages = [
            '/images/products/kvin_pipe_1.png',
            '/images/products/kvin_pipe_2.png'
        ];
        const seedStr = String(product.id || product.product_id || '') + String(product.name || product.product_name || product.sku || '');
        let hash = 0;
        for (let i = 0; i < seedStr.length; i++) {
            hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
        }
        return kvinImages[Math.abs(hash) % kvinImages.length];
    }

    // Check for TRC Brand / Pipe
    const isTrc = brand === 'TRC' || brand.includes('TRC') || name.includes('TRC') || sku.includes('TRC');

    if (isTrc) {
        const trcImages = [
            '/images/products/trc_pipe_1.png',
            '/images/products/trc_pipe_2.png',
            '/images/products/trc_pipe_3.png'
        ];
        const seedStr = String(product.id || product.product_id || '') + String(product.name || product.product_name || product.sku || '');
        let hash = 0;
        for (let i = 0; i < seedStr.length; i++) {
            hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
        }
        return trcImages[Math.abs(hash) % trcImages.length];
    }

    // Check for MT8 Brand / Pipe
    const isMt8 = brand === 'MT8' || brand.includes('MT8') || brand.includes('MT-8') || brand.includes('MT 8') ||
                  name.includes('MT8') || name.includes('MT-8') || name.includes('MT 8') ||
                  sku.includes('MT8') || sku.includes('MT-8');

    if (isMt8) {
        const mt8Images = [
            '/images/products/mt8_pipe_1.png',
            '/images/products/mt8_pipe_2.png',
            '/images/products/mt8_pipe_3.png'
        ];
        const seedStr = String(product.id || product.product_id || '') + String(product.name || product.product_name || product.sku || '');
        let hash = 0;
        for (let i = 0; i < seedStr.length; i++) {
            hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
        }
        return mt8Images[Math.abs(hash) % mt8Images.length];
    }

    return '';
};

window.resolveProductImage = function(item) {
    if (!item) return '';
    if (typeof item === 'string') {
        item = { name: item, sku: item };
    }
    if (item.image || item.image_url) {
        return item.image || item.image_url;
    }

    try {
        const stored = localStorage.getItem('posProductImages');
        if (stored) {
            const images = JSON.parse(stored);
            const pid = item.id ?? item.product_id ?? null;
            if (pid !== null && pid !== undefined) {
                if (images[pid]) return images[pid];
                if (images[String(pid)]) return images[String(pid)];
            }

            if (item.sku) {
                const s = String(item.sku).trim();
                if (images[s]) return images[s];
                if (images[s.toUpperCase()]) return images[s.toUpperCase()];
                if (images[s.toLowerCase()]) return images[s.toLowerCase()];
            }

            if (item.name) {
                const n = String(item.name).trim();
                if (images[n]) return images[n];
                if (images[n.toLowerCase()]) return images[n.toLowerCase()];
            }

            if (item.product_name) {
                const pn = String(item.product_name).trim();
                if (images[pn]) return images[pn];
                if (images[pn.toLowerCase()]) return images[pn.toLowerCase()];
            }

            // Compound name matching (e.g. "PIPE - CLICK 2IN1" vs "CLICK 2IN1")
            const rawName = String(item.name || item.product_name || '');
            if (rawName.includes(' - ')) {
                const parts = rawName.split(' - ').map(s => s.trim());
                for (const p of parts) {
                    if (p && images[p]) return images[p];
                    if (p && images[p.toLowerCase()]) return images[p.toLowerCase()];
                }
            }

            // Fuzzy scan all keys
            const keys = Object.keys(images);
            if (item.sku) {
                const skuClean = String(item.sku).trim().toLowerCase();
                const matchSku = keys.find(k => k.toLowerCase() === skuClean);
                if (matchSku && images[matchSku]) return images[matchSku];
            }
            if (item.name) {
                const nameClean = String(item.name).trim().toLowerCase();
                const matchName = keys.find(k => k.toLowerCase() === nameClean);
                if (matchName && images[matchName]) return images[matchName];
            }
        }
    } catch (e) {
        console.warn('Error resolving stored product image:', e);
    }

    // Fallback to default product image
    const def = window.getProductDefaultImage(item);
    if (def) return def;

    return '';
};

window.saveProductImage = function(product, dataUrl) {
    if (!product || !dataUrl) return;
    try {
        let images = {};
        try {
            images = JSON.parse(localStorage.getItem('posProductImages') || '{}');
        } catch(e) {
            images = {};
        }

        const pid = product.id ?? product.product_id ?? null;
        if (pid !== null && pid !== undefined) {
            images[String(pid)] = dataUrl;
            images[Number(pid)] = dataUrl;
        }

        if (product.sku) {
            const sku = String(product.sku).trim();
            if (sku && sku !== 'N/A' && sku !== '-') {
                images[sku] = dataUrl;
                images[sku.toUpperCase()] = dataUrl;
                images[sku.toLowerCase()] = dataUrl;
            }
        }

        if (product.name) {
            const name = String(product.name).trim();
            if (name && name !== 'Unknown Product' && name !== '-') {
                images[name] = dataUrl;
                images[name.toLowerCase()] = dataUrl;
            }
        }

        if (product.product_name) {
            const pName = String(product.product_name).trim();
            if (pName) {
                images[pName] = dataUrl;
                images[pName.toLowerCase()] = dataUrl;
            }
        }

        if (product.product_name && product.name && product.product_name !== product.name) {
            const compound1 = `${product.product_name} - ${product.name}`;
            const compound2 = `${product.name} - ${product.product_name}`;
            images[compound1] = dataUrl;
            images[compound1.toLowerCase()] = dataUrl;
            images[compound2] = dataUrl;
            images[compound2.toLowerCase()] = dataUrl;
        }

        localStorage.setItem('posProductImages', JSON.stringify(images));

        // Dispatch custom event for real-time reactivity in the current window
        window.dispatchEvent(new CustomEvent('pos-image-updated', {
            detail: { product, dataUrl }
        }));

        console.log('✓ Product image saved and synced across all modules:', pid, product.sku, product.name);
    } catch (err) {
        console.error('Failed to save product image:', err);
    }
};

console.log('KCC Motorcycle app.js & Product Image Synchronizer loaded.');
