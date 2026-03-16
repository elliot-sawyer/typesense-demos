function drawSwatch(width, height, hsl) {
    const swatch = document.createElement('span');
    swatch.setAttribute('title', HSLtoHex(hsl));
    swatch.style.display = 'inline-block';
    swatch.style.width = `${width}px`;
    swatch.style.height = `${height}px`;
    swatch.style.backgroundColor = `hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`;

    return swatch.outerHTML;
}

function HSLtoHex(hsl) {
    const h = hsl.h / 360;
    const s = hsl.s / 100;
    const l = hsl.l / 100;

    const hue2rgb = (p, q, t) => {
        if (t < 0) t += 1;
        if (t > 1) t -= 1;
        if (t < 1/6) return p + (q - p) * 6 * t;
        if (t < 1/2) return q;
        if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
        return p;
    };

    let r, g, b;
    if (s === 0) {
        r = g = b = l;
    } else {
        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    const toHex = x => Math.round(x * 255).toString(16).padStart(2, '0');
    return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
}

function adjustHue(percent, red, green, blue) {
    const hsl = HSL(red, green, blue);
    hsl.h = (hsl.h + (360 * (percent / 100))) % 360;
    if (hsl.h < 0) hsl.h += 360;
    return hsl;
}

function HSL(red, green, blue) {
    const r = red / 255;
    const g = green / 255;
    const b = blue / 255;

    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    const delta = max - min;

    const l = (max + min) / 2;

    let h, s;

    if (delta === 0) {
        h = 0;
        s = 0;
    } else {
        s = delta / (1 - Math.abs(2 * l - 1));

        if (max === r) {
            h = 60 * (((g - b) / delta) % 6);
            if (h < 0) h += 360;
        } else if (max === g) {
            h = 60 * ((b - r) / delta + 2);
        } else {
            h = 60 * ((r - g) / delta + 4);
        }
    }

    return {
        h: Math.round(h * 100) / 100,
        s: Math.round(s * 100 * 100) / 100,
        l: Math.round(l * 100 * 100) / 100,
    };
}
