# COSMETICA
## AI Implementation-Ready Master Specification
Generated: 2026-02-27 18:09

---
# PURPOSE

This document is structured for direct consumption by a coding AI.
It contains design tokens, layout rules, components, UX logic,
accessibility requirements, SEO structure, and performance rules.

The AI should be able to generate production-ready frontend code from this specification.

title: public/assets/title.png (サイズは現物を見て判断してください)
favicon: public/assets/cropped-icon-32x32.png (32px x 32px)

実装時に文言は日本語で記述してね。

---
# GLOBAL DESIGN SYSTEM

## Color Tokens (CSS Variable Ready)

:root {
  --bg-primary: #F8F7F5;
  --bg-section: #F1EEEC;
  --bg-card: #FFFFFF;
  --bg-hover: #F6EFF1;

  --text-primary: #1F1F1F;
  --text-secondary: #6B6B6B;
  --text-muted: #9A9A9A;

  --accent-rose: #E6A4AF;
  --accent-rose-hover: #D78290;
  --accent-soft: #F3D6DB;
  --rating-star: #E9A1B0;

  --cta-primary: #111111;
  --cta-hover: #333333;
  --cta-active: #000000;

  --price-low: #FFB8C6;
  --price-mid: #8DA9C4;
  --price-high: #C6A75E;
  --price-organic: #8AA77B;
}

Accent usage must not exceed 8 percent of total UI surface.

---
## Typography

Primary Font: Modern elegant sans-serif (Inter / SF Pro / Noto Sans JP)
Fallback: system-ui, sans-serif

Scale:
H1: 32px / 600
H2: 24px / 600
H3: 18px / 600
Body: 14px / 400
Small: 12px / 400

Line-height: 1.6

---
# LAYOUT SYSTEM

Desktop 1440px: 5 columns
Desktop 1200px: 4 columns
Tablet 768px: 2 columns
Mobile: 2 columns

Card gap: 24px
Section padding: 64px desktop / 32px mobile

---
# HOMEPAGE STRUCTURE

1. Sticky header with search
2. Category grid
3. Trending carousel
4. Top ranking block
5. Mood filter section
6. Infinite product grid
7. Footer

---
# PRODUCT CARD COMPONENT

Structure:
- 1:1 image
- Brand label (small)
- Product name (max 2 lines)
- Price
- Rating stars
- Favorite button
- Price tier badge

Shadow:
box-shadow: 0 2px 8px rgba(0,0,0,0.04);

Hover:
Image scale 1.05
Background change to var(--bg-hover)
Subtle elevation increase

---
# CORE DIFFERENTIATION UX

Mood Filter Categories:
Transparent
Glow
Matte
Natural
Mature
Korean Style

Filtering must update grid dynamically without full reload.

---
# USER FLOW

SEO Landing
→ Category
→ Mood Filter
→ Product Discovery
→ Review Validation
→ Primary Black CTA

---
# SEO STRUCTURE

H1: Category
H2: Sections
H3: Product Name
JSON-LD: Product, Review, AggregateRating
Infinite scroll must maintain paginated URLs.

---
# ACCESSIBILITY

Minimum contrast ratio 4.5:1
Keyboard navigation required
Visible focus states
ARIA labels on interactive components

---
# PERFORMANCE

Lazy load images
Use WebP
Skeleton loaders
Target LCP under 2.5 seconds

---
# BRAND RULES

No full-screen face hero images
Product-first design
Feminine but not overly pastel
Accent used only for emotional triggers

---
END OF SPECIFICATION
