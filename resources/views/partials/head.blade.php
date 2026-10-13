<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/png" href="{{ function_exists('app_logo_url') ? app_logo_url() : \App\Services\AppSettingService::getLogoUrl() }}">
<link rel="shortcut icon" type="image/png" href="{{ function_exists('app_logo_url') ? app_logo_url() : \App\Services\AppSettingService::getLogoUrl() }}">
<link rel="apple-touch-icon" href="{{ function_exists('app_logo_url') ? app_logo_url() : \App\Services\AppSettingService::getLogoUrl() }}">
<script>
    (function() {
        try {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (savedTheme === 'system' && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        } catch (e) {}
    })();

    /* ═══ GLOBAL COLOR THEME ENGINE (zero-flicker, runs before paint) ═══ */
    (function() {
        function hexToRgb(hex) {
            hex = hex.replace(/^#/, '');
            if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
            var n = parseInt(hex, 16);
            return { r: (n >> 16) & 255, g: (n >> 8) & 255, b: n & 255 };
        }

        function rgbToHex(r, g, b) {
            return '#' + [r, g, b].map(function(c) {
                var h = Math.max(0, Math.min(255, Math.round(c))).toString(16);
                return h.length < 2 ? '0' + h : h;
            }).join('');
        }

        function clamp(v) { return Math.max(0, Math.min(255, Math.round(v))); }

        function getLuminance(r, g, b) {
            var a = [r, g, b].map(function(v) {
                v = v / 255;
                return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
            });
            return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2];
        }

        function rgbToHsl(r, g, b) {
            r /= 255; g /= 255; b /= 255;
            var max = Math.max(r, g, b), min = Math.min(r, g, b);
            var h = 0, s = 0, l = (max + min) / 2;
            if (max !== min) {
                var d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }
            return { h: h, s: s, l: l };
        }

        function hslToRgb(h, s, l) {
            var r, g, b;
            if (s === 0) {
                r = g = b = l;
            } else {
                function hue2rgb(p, q, t) {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1/6) return p + (q - p) * 6 * t;
                    if (t < 1/2) return q;
                    if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                    return p;
                }
                var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                var p = 2 * l - q;
                r = hue2rgb(p, q, h + 1/3);
                g = hue2rgb(p, q, h);
                b = hue2rgb(p, q, h - 1/3);
            }
            return {
                r: Math.round(r * 255),
                g: Math.round(g * 255),
                b: Math.round(b * 255)
            };
        }

        window.generateThemeTokens = function(hex) {
            hex = (hex || '#6EC1D1').toUpperCase();
            var c = hexToRgb(hex);
            var lum = getLuminance(c.r, c.g, c.b);
            var hsl = rgbToHsl(c.r, c.g, c.b);

            var hoverL, activeL, borderL, lightBg, textColor;

            // WCAG luminance contrast: threshold 0.45 ensures excellent readability
            if (lum > 0.45) {
                textColor = '#0F172A';
            } else {
                textColor = '#FFFFFF';
            }

            // Adaptive hover color calculation:
            // 1) Very light / White (L >= 0.85): subtle light gray on hover (#D4D4D8)
            // 2) Pure Black / Dark (L <= 0.15): slightly lighter clean variation on hover (#2E2E2E)
            // 3) Deep Slate (0.15 < L <= 0.22): slightly lighter slate (#334155)
            // 4) Deep Teal (0.22 < L <= 0.28): slightly darker deep teal (#0A3B40)
            // 5) Normal & vibrant colors (0.28 < L < 0.85, e.g. Blue, Purple, Green, Red, Cyan, Amber):
            //    drops by 0.13 for a clean, slightly darker shade matching user specification
            if (hsl.l >= 0.85) {
                hoverL = 0.82; // Subtle light gray
                activeL = 0.74;
                borderL = '#CBD5E1';
                lightBg = '#F8FAFC';
            } else if (hsl.l <= 0.15) {
                hoverL = Math.min(1.0, hsl.l + 0.18); // Slightly lighter clean dark variation
                activeL = Math.min(1.0, hsl.l + 0.26);
                borderL = '#64748B';
                lightBg = '#F1F5F9';
            } else if (hsl.l <= 0.22) {
                hoverL = 0.28; // Slightly lighter slate
                activeL = 0.35;
                borderL = '#475569';
                lightBg = '#F8FAFC';
            } else if (hsl.l <= 0.28) {
                hoverL = Math.max(0.06, hsl.l - 0.08); // Slightly darker
                activeL = Math.max(0.04, hsl.l - 0.12);
                borderL = Math.max(0.08, hsl.l - 0.06);
                lightBg = '#F8FAFC';
            } else {
                hoverL = Math.max(0.0, hsl.l - 0.13); // Slightly darker (e.g. blue -> slightly darker blue)
                activeL = Math.max(0.0, hsl.l - 0.20);
                borderL = Math.max(0.0, hsl.l - 0.10);
                lightBg = rgbToHex(
                    clamp(c.r + (255 - c.r) * 0.88),
                    clamp(c.g + (255 - c.g) * 0.88),
                    clamp(c.b + (255 - c.b) * 0.88)
                );
            }

            var hoverRgb = hslToRgb(hsl.h, hsl.s, hoverL);
            var hoverColor = rgbToHex(hoverRgb.r, hoverRgb.g, hoverRgb.b);

            var activeRgb = hslToRgb(hsl.h, hsl.s, activeL);
            var activeColor = rgbToHex(activeRgb.r, activeRgb.g, activeRgb.b);

            var borderColor = typeof borderL === 'string' ? borderL : (function() {
                var bRgb = hslToRgb(hsl.h, hsl.s, borderL);
                return rgbToHex(bRgb.r, bRgb.g, bRgb.b);
            })();

            return {
                primary: hex,
                hover: hoverColor,
                active: activeColor,
                light: lightBg,
                border: borderColor,
                text: textColor,
                rgb: c.r + ', ' + c.g + ', ' + c.b
            };
        };

        window.applyThemeColor = function(hex, save) {
            hex = (hex || '#6EC1D1').toUpperCase();
            var tokens = window.generateThemeTokens(hex);
            var root = document.documentElement.style;

            root.setProperty('--primary-color', tokens.primary);
            root.setProperty('--primary-hover', tokens.hover);
            root.setProperty('--primary-active', tokens.active);
            root.setProperty('--primary-light', tokens.light);
            root.setProperty('--primary-border', tokens.border);
            root.setProperty('--primary-text', tokens.text);
            root.setProperty('--primary-color-rgb', tokens.rgb);

            try {
                window.dispatchEvent(new CustomEvent('themeColorChanged', { detail: { color: hex, tokens: tokens } }));
            } catch(e) {}

            if (save) {
                try { localStorage.setItem('theme_color', hex); } catch(e) {}
                // Persist to backend (fire-and-forget)
                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = csrfMeta ? csrfMeta.content : '';
                if (csrfToken) {
                    fetch('/settings/theme-color', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ color: hex })
                    }).catch(function() {});
                }
            }
        };

        // Apply immediately before first paint
        var initialColor = null;
        try { initialColor = localStorage.getItem('theme_color'); } catch(e) {}
        if (!initialColor) {
            initialColor = @json(\App\Services\AppSettingService::getThemeColor());
        }
        window.applyThemeColor(initialColor || '#6EC1D1', false);
    })();
</script>
