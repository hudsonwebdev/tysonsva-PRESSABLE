# PageSpeed Insights & Core Web Vitals – Improvement Guide

Use [PageSpeed Insights](https://pagespeed.web.dev/) to measure your site. This guide focuses on **LCP**, **INP** (interaction to next paint), **CLS**, and **Speed Index** so the site can pass Core Web Vitals.

---

## 1. Reduce render-blocking resources (LCP / FCP)

### Theme (implemented in `inc/performance.php`)
- **Preload main CSS** so the browser discovers it earlier.
- **Preconnect** to third-party origins (Google Tag Manager, Mapbox when used) to save connection time.
- **Defer non-critical scripts** so parsing isn’t blocked; scripts already in the footer get `defer` for better behavior.

### Hosting / server
- Enable **HTTP/2** (or HTTP/3) and **compression (Brotli or gzip)** for HTML/CSS/JS.
- Use a **CDN** for static assets (e.g. Pressable CDN) and ensure cache headers are set.

### Optional (advanced)
- **Critical CSS**: Inline the minimal CSS needed for above-the-fold content and load the rest of `main.css` asynchronously (e.g. `media="print"` + `onload`).
- **Split CSS**: Load block-specific or below-the-fold CSS in a separate file and load it later.

---

## 2. Improve Largest Contentful Paint (LCP)

- **LCP element** is usually the hero/banner image or a large heading.
- **Hero video** (image-banner block with “Video”): The theme now uses a **poster image** for LCP, defers the video source until after `window.load`, and on **mobile does not load the video** (poster only). Set the block’s “Banner Image” when using Video so it’s used as the poster and LCP stays fast.
- **Hero image** (e.g. image-banner block):
  - Use **`fetchpriority="high"`** and **`loading="eager"`** for the first visible hero image (see `awesome_acf_responsive_image` optional 5th/6th params).
  - Serve **appropriately sized** images (e.g. 1920px or 1440px wide for desktop) and use **WebP/AVIF** where possible.
- **Preconnect** to the domain that serves images (e.g. your CDN or same-origin) if different from the main domain.
- Ensure **server response time (TTFB)** is low; use caching and a fast host/CDN.

---

## 3. Avoid large layout shifts (CLS)

- Give **explicit `width` and `height`** (or aspect-ratio) to images, iframes, and ads so the layout doesn’t jump when they load.
- **Reserve space** for embeds (e.g. Mapbox, YouTube) with a fixed aspect-ratio container.
- Avoid inserting **above-the-fold content** (e.g. banners or text) after load without reserved space; use CSS min-height or aspect-ratio if needed.
- **Fonts**: Use `font-display: swap` (or optional) in `@font-face` so text remains visible and layout is stable while fonts load.

---

## 4. Reduce JavaScript execution time (INP / TBT)

- **Defer** non-critical scripts (theme does this for main JS and Mapbox when used).
- **Reduce or delay** third-party scripts (GTM, analytics, chat). Load them after interaction or after a short delay if possible.
- Prefer **native** behavior (e.g. CSS or minimal JS) over heavy libraries where possible.
- **Code splitting**: Keep main bundle small; load neighborhood/map code only on the neighborhood page (already done).

---

## 5. Image best practices

- **Responsive images**: Use `srcset` and `sizes` (e.g. via `awesome_acf_responsive_image`).
- **Lazy loading**: Use `loading="lazy"` for below-the-fold images (theme does this); use `loading="eager"` + `fetchpriority="high"` only for the LCP image.
- **Format**: Prefer **WebP** (or AVIF) with fallbacks; use a plugin or server rules to serve them.
- **Dimensions**: Add **width** and **height** (or CSS aspect-ratio) to avoid CLS.
- **Compression**: Optimize uploads (e.g. Imagify, ShortPixel, or Smush) and serve at appropriate sizes.

### Block image sizes (theme)

Each block `render.php` uses an appropriate WordPress image size so the browser isn’t sent larger files than needed:

| Block / context        | Size used   | Max width (sizes) | Notes |
|------------------------|-------------|--------------------|-------|
| image-banner (hero)    | `tca-hero`  | 1920px             | LCP; poster for video uses same. |
| block-image           | `tca-hero`  | 1920px             | Full-bleed content image. |
| text-image-combo      | `large`     | 1024px             | Side image; gallery slider uses `large`. |
| quote-image           | `large`     | 1024px             | Side image. |
| image-gallery         | `large`     | 800px              | Grid thumbnails. |
| cta-blocks             | `large`     | 768px              | Card background. |
| flex-deco-colums      | `large`     | 600px              | Column header image. |
| logo-gallery          | `medium`    | 320px              | Logos; responsive srcset, lazy. |

Custom size `tca-hero` (1920px wide) is registered in `functions.php`; regenerate thumbnails after adding it if you need existing uploads to get that size.

---

## 6. Fonts

- **Preload** the one or two critical font files (e.g. Silka Regular and Bold) used for above-the-fold text.
- Use **`font-display: swap`** in all `@font-face` rules (in `src/scss/silka/.../stylesheet.scss` or wherever fonts are defined) so text is visible immediately.
- **Subset** fonts if possible to reduce size.

---

## 7. Third-party scripts

- **Google Tag Manager**: Already loaded async; consider delaying GTM until user interaction or after a few seconds to improve INP.
- **Mapbox**: Loaded only on neighborhood pages; keep it deferred and ensure the map container has fixed dimensions to avoid CLS.

---

## 8. Checklist summary

| Area              | Action |
|-------------------|--------|
| CSS               | Preload main.css; consider critical CSS inline. |
| Scripts           | Defer non-critical JS (theme adds defer). |
| Preconnect        | GTM, Mapbox (on neighborhood pages). |
| LCP image         | fetchpriority="high", loading="eager", correct size/format. |
| All images        | width/height or aspect-ratio, lazy below fold, WebP/AVIF. |
| Fonts             | font-display: swap, preload critical fonts. |
| Server/CDN        | Caching, compression, HTTP/2, low TTFB. |
| Third-party       | Minimize and/or delay GTM and other scripts. |

After changes, re-test with [PageSpeed Insights](https://pagespeed.web.dev/) (mobile and desktop) and the Chrome “Core Web Vitals” / “Largest Contentful Paint” diagnostics.
