# Anti-AI Generic UI/UX Design Guide
### An Internal Handbook for Building Handcrafted, Intentional, Enterprise-Grade Interfaces

*Written from the perspective of a Senior Product Designer (15+ years) — for design teams who want their work to look designed, not generated.*

---

## Table of Contents

1. [Core Design Philosophy](#1-core-design-philosophy)
2. [Why AI-Generated Interfaces Are Easy to Recognize](#2-why-ai-generated-interfaces-are-easy-to-recognize)
3. [Things That Instantly Make a UI Look AI-Generated](#3-things-that-instantly-make-a-ui-look-ai-generated)
4. [Visual Hierarchy Principles](#4-visual-hierarchy-principles)
5. [Grid Systems & the 8px Spacing Rule](#5-grid-systems--the-8px-spacing-rule)
6. [Typography Hierarchy](#6-typography-hierarchy)
7. [Color Systems & Semantic Color](#7-color-systems--semantic-color)
8. [Card Design Principles](#8-card-design-principles)
9. [Layout Composition & Information Architecture](#9-layout-composition--information-architecture)
10. [White Space Management](#10-white-space-management)
11. [Alignment & Consistency Rules](#11-alignment--consistency-rules)
12. [Component Consistency Across an Application](#12-component-consistency-across-an-application)
13. [Dashboard Design Best Practices](#13-dashboard-design-best-practices)
14. [Table Design Best Practices](#14-table-design-best-practices)
15. [Form Design Best Practices](#15-form-design-best-practices)
16. [Filter & Search UX](#16-filter--search-ux)
17. [Data Visualization Best Practices](#17-data-visualization-best-practices)
18. [Empty, Loading, Error & Success States](#18-empty-loading-error--success-states)
19. [Notification & Alert Design](#19-notification--alert-design)
20. [Navigation Patterns](#20-navigation-patterns)
21. [Sidebar Organization](#21-sidebar-organization)
22. [Icon Usage Guidelines](#22-icon-usage-guidelines)
23. [Button Hierarchy & CTA Prioritization](#23-button-hierarchy--cta-prioritization)
24. [Interaction Design & Micro-interactions](#24-interaction-design--micro-interactions)
25. [Accessibility Principles](#25-accessibility-principles)
26. [Responsive Design Considerations](#26-responsive-design-considerations)
27. [UX Psychology Principles](#27-ux-psychology-principles)
28. [Visual Rhythm & Balance](#28-visual-rhythm--balance)
29. [Content Density & Scanning Behavior](#29-content-density--scanning-behavior)
30. [Enterprise Dashboard Design Standards](#30-enterprise-dashboard-design-standards)
31. [SaaS Application Design Patterns](#31-saas-application-design-patterns)
32. [Domain-Specific Guidance](#32-domain-specific-guidance)
    - [Inventory Management Systems](#321-inventory-management-systems-ims)
    - [Warehouse Management Systems](#322-warehouse-management-systems-wms)
    - [Decision Support Systems](#323-decision-support-systems-dss)
    - [Analytics Dashboards](#324-analytics-dashboards)
33. [Real Product Design Principles: Stripe, Linear, Notion, Vercel, GitHub, Figma, Apple](#33-real-product-design-principles)
34. [Human Design Patterns AI Usually Misses](#34-human-design-patterns-ai-usually-misses)
35. [Before vs After Examples](#35-before-vs-after-examples)
36. [Design Smells That Should Be Fixed](#36-design-smells-that-should-be-fixed)
37. [Professional Dashboard Layout Patterns](#37-professional-dashboard-layout-patterns)
38. [Senior Designer Checklist Before Shipping](#38-senior-designer-checklist-before-shipping)

---

## 1. Core Design Philosophy

A senior designer isn't distinguished by tool proficiency — it's distinguished by **restraint, intent, and taste**. Every decision on the canvas should be traceable to a reason. If you can't explain *why* a card has a shadow, *why* a button is blue, or *why* there's 24px here and 32px there, it isn't a decision — it's a default.

### The Five Pillars

1. **Intent over decoration.** Every visual element earns its place by serving comprehension, hierarchy, or action. Decoration for its own sake is noise.
2. **Constraint breeds consistency.** A small, disciplined set of tokens (colors, spacing, type sizes) used relentlessly looks more professional than a large palette used loosely.
3. **Hierarchy is the product.** Users don't read interfaces — they scan them. If everything is emphasized, nothing is.
4. **Density is a feature, not a flaw.** Enterprise software is used by professionals for hours a day. Comfortable ≠ spacious. Match density to the user's expertise and frequency of use.
5. **Invisible craftsmanship.** The best design decisions are the ones nobody consciously notices — consistent baseline grids, optically corrected alignment, purposeful motion. It "just feels right."

> **Senior mindset:** A junior designer asks "does this look good?" A senior designer asks "does this look *inevitable* — like there was no other reasonable way to design it?"

---

## 2. Why AI-Generated Interfaces Are Easy to Recognize

AI-generated (or template-copied) UIs tend to optimize for *looking complete* rather than *being coherent*. The tells are structural, not stylistic — which is why they show up across every AI tool and every generic template, regardless of color scheme.

| Symptom | Why it happens |
|---|---|
| Everything is a rounded card with a soft shadow | Cards are the "safe default" — no layout decisions required |
| Purple-to-blue gradients everywhere | Statistically the most common "modern SaaS" gradient in training data |
| Emoji used as icons (🚀 📊 ✅) | Fast, no icon library needed, reads as "friendly" |
| Every metric has an icon in a colored circle to its left | A single memorized pattern applied uniformly, regardless of fit |
| Inconsistent spacing that's "close enough" | No underlying spacing system — values are eyeballed per component |
| Center-aligned everything, including body text | Centering *looks* balanced without requiring hierarchy decisions |
| All buttons are the same shade of blue | No hierarchy between primary/secondary/tertiary actions |
| Excessive use of bold and color for emphasis | Emphasis by decoration instead of by hierarchy and spacing |
| Icons that don't match the product's actual domain | Generic icon set applied without curation |
| Perfect, evenly-distributed dummy data | No attention to real-world content: long names, empty states, overflow |
| Identical corner radius on everything (cards, buttons, inputs, avatars) | One border-radius token copy-pasted everywhere without proportional scaling |
| Overuse of drop shadows for depth instead of using borders/contrast | Shadows are an easy way to "add depth" without a real elevation system |

**The core signature of generic/AI design: it optimizes for looking like "a nice app" in a screenshot, not for being usable in the specific domain it was built for.**

---

## 3. Things That Instantly Make a UI Look AI-Generated

This is your red-flag list. If your screen has 3+ of these, stop and redesign.

- [ ] Gradient text or gradient buttons used decoratively (not functionally)
- [ ] Glassmorphism / frosted blur applied without a reason (no layering context)
- [ ] Every card has a shadow, even ones sitting flush in a grid
- [ ] Icons in colored circular/rounded-square badges next to every stat
- [ ] Emoji as functional icons in a professional product
- [ ] All corner radii identical regardless of element size (8px on a 400px card AND a 32px button)
- [ ] Overly generic microcopy: "Manage your data effortlessly," "Unlock powerful insights"
- [ ] Hero sections with 3 floating cards and abstract blobs behind them
- [ ] Buttons that are all full saturation, all the same size, all the same color
- [ ] No visible grid — elements "floating" at arbitrary positions
- [ ] Excessive white space used to mask lack of content strategy (looks empty, not calm)
- [ ] Perfectly centered single-column layouts for data-dense enterprise apps
- [ ] Illustration style: generic flat-color humans with oversized heads and no facial detail
- [ ] Font pairing: a rounded, friendly sans-serif for headings paired with system default elsewhere
- [ ] Stat cards with huge numbers and tiny arbitrary sparklines that don't map to axes
- [ ] Navigation sidebar icons with no labels, or labels that don't match icon meaning
- [ ] Randomly-alternating card background colors (a rainbow of pastel cards)
- [ ] Placeholder-quality dummy data ("John Doe," "Item 1," "Lorem Ipsum" visible in "final" mockups)

---

## 4. Visual Hierarchy Principles

Hierarchy is built from **five levers**, in order of strength:

1. **Size** — the strongest signal. Use sparingly; too many size steps flattens hierarchy.
2. **Weight** — bold vs regular. More reliable than color for hierarchy because it's colorblind-safe.
3. **Color/contrast** — reserve saturated color for the single most important element on screen.
4. **Spacing** — proximity groups related items; distance separates unrelated ones (see Gestalt, §27).
5. **Position** — top-left (in LTR contexts) carries the most visual weight; users scan in an F- or Z-pattern.

### Rule of One
On any given screen, there should be **one** primary focal point. If a dashboard has five equally-loud stat cards and a chart all screaming for attention, the user's eye has nowhere to rest — this is the single most common failure in generic dashboard design.

### Hierarchy checklist
- [ ] Can you tell what matters most on this screen in under 2 seconds?
- [ ] Is there exactly one primary CTA visible per view?
- [ ] Do headings decrease in visual weight as they decrease in importance?
- [ ] Is emphasis achieved with weight/size before color?

---

## 5. Grid Systems & the 8px Spacing Rule

Every professional design system is built on a **base unit**. 8px is the industry standard because it divides cleanly across common screen densities (1x, 2x, 3x) and keeps spacing decisions fast and consistent.

### The Scale
```
4px   →  micro spacing (icon-to-label gap, inline chip padding)
8px   →  base unit (tight component padding)
12px  →  small gaps (form field internal padding)
16px  →  default gap (between related elements)
24px  →  section-level gap (between grouped components)
32px  →  large gap (between distinct content blocks)
48px  →  major section break
64px  →  page-level rhythm (top of page, major dividers)
```

**Rule:** Every margin, padding, and gap value must be a multiple of 4, ideally of 8. If you find yourself typing `13px` or `22px`, stop — round to the nearest system value.

### Grid Anatomy (12-column, desktop)
```
┌──────────────────────────────────────────────────────────────────┐
│  margin(24) │ col │ col │ col │ col │ col │ col │... │ margin(24) │
│             │←16→ gutter between columns                          │
└──────────────────────────────────────────────────────────────────┘
Container max-width: 1440px (enterprise) / 1280px (SaaS marketing-adjacent)
Sidebar: fixed 240–280px | Content: fluid remaining columns
```

### Spacing checklist
- [ ] All spacing values are multiples of 4 (preferably 8)
- [ ] Padding inside a card is consistent across all cards of the same type
- [ ] Vertical rhythm between sections uses one of the defined scale steps, never arbitrary values
- [ ] Related elements sit closer together than unrelated ones (proximity = grouping)

---

## 6. Typography Hierarchy

Generic UIs use 3–4 font sizes with no clear logic. Professional systems use a **modular type scale** with defined roles.

### Type Scale (example, 1.25 ratio — Major Third adjacent)

| Token | Size | Line Height | Weight | Usage |
|---|---|---|---|---|
| Display | 32–40px | 1.2 | 600–700 | Page titles, rare |
| H1 | 28px | 1.25 | 600 | Page/section title |
| H2 | 22px | 1.3 | 600 | Card/module title |
| H3 | 18px | 1.35 | 600 | Subsection title |
| Body Large | 16px | 1.5 | 400 | Primary reading text |
| Body | 14px | 1.5 | 400 | Default UI text, table cells |
| Small / Caption | 12px | 1.4 | 400–500 | Metadata, timestamps, helper text |
| Micro | 11px | 1.3 | 500 | Badges, tags, dense data tables |

### Rules
- **Line height decreases as font size increases.** Large display text needs tighter leading (1.1–1.25); small body text needs looser leading (1.5+) for readability.
- **Never use more than 2 typefaces.** One for UI (a neutral grotesque: Inter, SF Pro, IBM Plex Sans, Söhne) and optionally one monospace for data/code (JetBrains Mono, SF Mono).
- **Weight does the emphasis work, not size, in dense UI.** In a data table, don't bump font size for a total row — bump weight to 600 and keep the same size.
- **Numerals should be tabular** (`font-variant-numeric: tabular-nums`) in any table, financial figure, or metric that will be scanned in a column — proportional numerals cause tables to "wobble."

### Typography checklist
- [ ] No more than 2 typefaces in the entire product
- [ ] No more than 6–8 font-size tokens exist in the whole system
- [ ] Tabular numerals used in all tables and metrics
- [ ] Line height is proportionally larger for smaller text
- [ ] Heading weight is 600, not 700+ (700+ often reads as "shouty" in UI, appropriate mainly for marketing)

---

## 7. Color Systems & Semantic Color

### Structure of a professional color system

```
Neutrals (the workhorse — 80–90% of the UI)
  gray-50 … gray-900  (9–11 steps, used for backgrounds, borders, text)

Brand (1 color, used sparingly)
  brand-500 as the base, brand-600 for hover/pressed, brand-100 for tinted backgrounds

Semantic (meaning-driven, NOT decorative)
  success   → green   (confirmations, positive deltas, completed states)
  warning   → amber   (needs attention, pending, low stock)
  danger    → red     (errors, destructive actions, critical alerts)
  info      → blue    (neutral information, tips)
```

### The 60-30-10 Rule
- **60%** neutral background/surface tones
- **30%** neutral text/border/structure tones
- **10%** color (brand + semantic combined)

Generic AI UIs often invert this — using saturated color for 40–50% of the screen. A professional enterprise product should look almost monochrome at a glance, with color appearing only where it carries meaning.

### Do vs Don't

| Don't | Do |
|---|---|
| Use brand color for every icon and every card border | Reserve brand color for primary actions and active/selected states only |
| Use red/green purely for decoration | Reserve red/green strictly for semantic meaning (error/success) |
| Use 5+ accent colors across a dashboard | Use 1 accent + semantic set only |
| Gradient backgrounds on cards/buttons | Flat, solid, single-tone surfaces; gradients reserved for rare hero/marketing moments |
| Pure black (#000) text on pure white (#FFF) | Off-black (#0F1115–#1A1D21) on off-white/gray-50 for reduced eye strain |

### Color checklist
- [ ] Neutral palette has 9+ steps for flexible light/dark contrast
- [ ] Semantic colors (success/warning/danger/info) are never reused for decoration
- [ ] Brand color usage is under 10% of visible surface area
- [ ] Text contrast meets WCAG AA (4.5:1 body, 3:1 large text) at minimum

---

## 8. Card Design Principles

**The generic AI failure mode: everything is a floating white rounded rectangle with a shadow.** Cards should be used with purpose, not as a default container for every piece of content.

### When to use a card
- Grouping genuinely distinct, self-contained pieces of content (e.g., a single KPI, a single record preview)
- Content that needs to visually separate from a busy background

### When NOT to use a card
- A full-width table already has row/column structure — wrapping the whole table in another bordered box is redundant framing
- A settings page with clearly separated sections doesn't need each section boxed — a heading + divider is often enough
- Dense dashboards: stacking cards-within-cards-within-cards creates "frame fatigue"

### Card anatomy
```
┌───────────────────────────────────┐
│ 24px padding                      │
│  Label (12px, gray-500, medium)   │
│  Value (28px, gray-900, semibold) │
│  Δ +4.2% vs last period (green)   │
│                                    │
└───────────────────────────────────┘
Border: 1px solid gray-200 (preferred over shadow for enterprise density)
Shadow: none, or 1px 2px rgba(0,0,0,0.04) MAX — never a soft 20px blur
Radius: 8–10px (not 16–24px — large radii read as "consumer/playful," not enterprise)
```

### Grouping with purpose (instead of scattering cards)
```
BEFORE (generic — 6 disconnected floating cards):
┌────────┐ ┌────────┐ ┌────────┐
│ Card 1 │ │ Card 2 │ │ Card 3 │
└────────┘ └────────┘ └────────┘
┌────────┐ ┌────────┐ ┌────────┐
│ Card 4 │ │ Card 5 │ │ Card 6 │
└────────┘ └────────┘ └────────┘

AFTER (intentional — one bordered module, internal dividers):
┌──────────────────────────────────────────┐
│  Inventory Overview                       │
│ ┌─────────┬─────────┬─────────┬─────────┐│
│ │ Stat 1  │ Stat 2  │ Stat 3  │ Stat 4  ││
│ └─────────┴─────────┴─────────┴─────────┘│
└──────────────────────────────────────────┘
```
The "after" version groups related KPIs into one visually connected module with internal dividers rather than gutters — this communicates "these 4 numbers belong to the same story" instead of "here are 4 unrelated boxes."

### Card checklist
- [ ] Every card exists because grouping/separation is genuinely needed, not by default
- [ ] Shadows are subtle (1px) or replaced entirely by borders
- [ ] Border radius is proportional to card size (smaller elements get smaller radii)
- [ ] Related cards are merged into one bordered module with internal dividers where appropriate

---

## 9. Layout Composition & Information Architecture

### The inverted pyramid of enterprise layout
1. **Global navigation** (top or side) — always accessible, minimal
2. **Contextual sub-navigation** (tabs, breadcrumbs) — where am I
3. **Primary content zone** — the task at hand
4. **Secondary/contextual panel** — details, filters, metadata (often right-docked)

### Standard enterprise shell
```
┌─────────────────────────────────────────────────────────────┐
│ Top bar: logo | search | notifications | user menu          │
├───────────┬───────────────────────────────────┬─────────────┤
│           │  Breadcrumb / Page title / Actions │             │
│  Sidebar  ├───────────────────────────────────┤ Context      │
│  Nav      │                                    │ Panel       │
│  (240px)  │      Primary Content               │ (320px,     │
│           │                                    │  optional)  │
│           │                                    │             │
└───────────┴───────────────────────────────────┴─────────────┘
```

### Information architecture rules
- Group by **user mental model**, not by database schema. Users think "Orders → Shipments → Returns," not "Table A → Table B → Table C."
- Flatten navigation depth to 2 levels wherever possible (section → subsection). A 4-level-deep nav is a sign the IA needs rework, not more UI polish.
- Every page should answer, within the first viewport: **Where am I? What can I do here? What needs my attention?**

---

## 10. White Space Management

White space is not "unused space" — it is an active layout tool with two roles:

1. **Grouping space** (small, consistent — e.g. 8–16px): binds related elements together.
2. **Separating space** (larger — 24–48px): signals unrelated content, new sections, new topics.

**Generic AI failure:** uniform, large white space applied everywhere, which flattens hierarchy (everything feels equally distant from everything else) — or the opposite, cramped UI with no breathing room where every element touches its neighbor.

**Senior approach:** vary white space *intentionally* to encode structure. If you can delete a divider line and the grouping is still obvious from spacing alone, your white space is doing its job.

### White space checklist
- [ ] Space between unrelated sections is visibly larger than space between related elements
- [ ] No arbitrary "just because it looked empty" padding
- [ ] Dense data views (tables, WMS scan screens) use tighter spacing deliberately; marketing-adjacent views use looser spacing deliberately

---

## 11. Alignment & Consistency Rules

- **Left-align data-dense and body content.** Center-aligned body text and center-aligned tables are a hallmark of generic templates — they're harder to scan because the eye's left starting edge moves on every line.
- **Right-align numbers in tables** so magnitudes are comparable at a glance (place decimal points/units in a fixed column).
- **Establish one consistent grid and snap everything to it.** Optical alignment sometimes overrides mathematical alignment (e.g., a triangular play icon needs to shift 1–2px to *look* centered) — but this is the exception, applied deliberately, not the rule.
- **Consistent icon-to-text baseline alignment.** Icons should optically align to the cap-height or x-height of adjacent text, not just be vertically centered by bounding box.

### Alignment checklist
- [ ] All left edges within a column align to the same grid line
- [ ] Numeric columns are right-aligned with tabular numerals
- [ ] Icons are optically — not just mathematically — centered against text
- [ ] No mixed alignment within a single list/table (some rows centered, others left-aligned)

---

## 12. Component Consistency Across an Application

A senior red flag audit question: *"If I place any two buttons from any two different screens side-by-side, are they identical in height, radius, padding, and type style?"*

### Build once, reference everywhere
- One button component with defined variants (primary/secondary/tertiary/destructive/ghost) and states (default/hover/active/disabled/loading) — never a one-off button styled inline for a single screen.
- One input component used identically in every form, every filter bar, every modal.
- One card component with defined padding/radius/border tokens, not five different "similar but not quite" cards built by different people.

### Consistency checklist
- [ ] Every button style traces back to a single defined component with variants
- [ ] Every input field has consistent height, padding, border, and focus state app-wide
- [ ] Icon sizes are limited to 2–3 fixed sizes (e.g., 16px, 20px, 24px) used consistently by context
- [ ] Corner radius tokens are limited to 2–3 values scaled by element size

---

## 13. Dashboard Design Best Practices

- **Lead with the answer, not the data.** A dashboard's top zone should answer "how are we doing?" in one glance (key metrics + trend), before diving into breakdowns.
- **Group by decision, not by data source.** Don't dump every available metric — curate around the 3–5 questions this dashboard exists to answer.
- **Limit simultaneous focal points.** One hero chart or KPI row, supporting detail below — not 12 equally-weighted widgets competing for attention.
- **Respect scan patterns.** Most important KPI top-left; trends and comparisons to the right; detail tables below the fold.
- **Avoid "widget soup."** If a dashboard needs a legend to explain what each of 15 colored boxes means, it has failed at hierarchy.

### Dashboard checklist
- [ ] Top row answers "how are we doing" in under 3 seconds
- [ ] No more than 4–6 KPI cards above the fold
- [ ] Every chart has a clear, single takeaway a user could state in one sentence
- [ ] Detail/drill-down tables live below summary visuals, not competing with them

---

## 14. Table Design Best Practices

- Right-align numeric columns; left-align text columns; center-align only short categorical/status columns.
- Use **row height consistency** — dense tables (WMS/IMS) ~36–40px rows; comfortable tables ~48–56px rows. Pick one per context and stay consistent.
- Zebra striping is optional and often unnecessary if row height and dividers are well-set — a hairline border (1px, gray-100) between rows is usually enough.
- **Sticky headers** for any table that scrolls beyond one viewport.
- **Sortable columns** should show sort direction clearly (arrow icon, not just color change).
- Truncate long text with ellipsis + tooltip, never let text wrap unpredictably and break row height consistency.
- Bulk actions: checkbox column pinned left; when a row is selected, a contextual action bar should appear (not require a scroll to a bottom "submit" button).
- Empty/zero-value cells: use a neutral dash "–" rather than leaving them blank (blank cells read as broken/loading).

### Table checklist
- [ ] Numeric columns right-aligned with tabular numerals
- [ ] Consistent row height across the entire table
- [ ] Sticky header on scroll
- [ ] Clear sort indicators
- [ ] Truncation + tooltip for overflow text
- [ ] Zero/empty values shown explicitly, not left blank

---

## 15. Form Design Best Practices

- **Single column forms** outperform multi-column for completion rate and comprehension — reserve 2-column layouts for genuinely paired fields (City / State / ZIP).
- **Label above field**, not beside it — scans faster, works better responsively.
- Group related fields under a subheading with consistent spacing (24px between groups, 12–16px between a label and its field, 16px between fields in the same group).
- **Inline validation** on blur, not on every keystroke (keystroke-level validation feels punitive).
- Required/optional: mark the *minority* case. If most fields are required, only mark "(optional)" fields — reduces visual clutter of asterisks everywhere.
- Primary action (Save/Submit) is bottom-right or bottom-full-width on mobile; destructive/cancel actions are visually quieter (ghost/text button), positioned to the left of the primary action.
- Helper text sits below the field, not as a tooltip that hides guidance until hovered — for anything essential to filling the field correctly.

### Form checklist
- [ ] Single-column layout unless fields are logically paired
- [ ] Labels above fields
- [ ] Inline validation on blur/submit, not per-keystroke
- [ ] Only the minority required/optional state is labeled
- [ ] Primary CTA visually dominant; cancel/secondary visually quiet

---

## 16. Filter & Search UX

- **Persistent filter bar** for frequently-used filters (status, date range, category) — don't bury common filters inside a modal.
- **Progressive disclosure for advanced filters** — show 3–5 common filters by default, "More filters" reveals the rest (see Hick's Law, §27).
- Active filters should render as **removable chips** near the results, so users always see what's currently applied without opening the filter panel again.
- Search should support **debounced live results** (250–400ms) for lookup-style search, and an explicit submit for complex/boolean search.
- Always show a **result count** and a clear **"Clear all filters"** action.
- Empty search results should suggest a next step (broaden filters, check spelling, browse categories) — never just "No results."

### Filter/search checklist
- [ ] Common filters are always visible; advanced filters are progressively disclosed
- [ ] Active filters shown as chips with individual remove (×)
- [ ] Result count visible and updates live
- [ ] Empty state offers a next action, not a dead end

---

## 17. Data Visualization Best Practices

- **Choose the chart type by the question being answered**, not by variety: trend over time → line; comparison across categories → bar; part-to-whole (≤5 segments) → stacked bar or simple pie; distribution → histogram; relationship → scatter.
- **Avoid 3D charts, excessive gridlines, and decorative gradients on data.** Flat, single-hue bars with one accent for the highlighted series read as more credible/professional.
- **Direct-label when possible** instead of relying solely on a separate legend — reduces the eye's back-and-forth.
- **Limit color count per chart to 4–6 max**; beyond that, group into "Other."
- Axis labels should never be rotated 90° if avoidable — prefer horizontal bars over rotated x-axis labels for long category names.
- Always include units and a clear, human-readable title stating the takeaway (e.g., "Stockouts fell 18% this quarter" rather than "Stockout Chart").

### Data viz checklist
- [ ] Chart type matches the question, not just visual variety
- [ ] No 3D effects, no unnecessary gridlines/gradients
- [ ] ≤6 colors per chart, with a clear "Other" bucket
- [ ] Every chart has a takeaway-oriented title

---

## 18. Empty, Loading, Error & Success States

Generic UIs treat these as afterthoughts. Senior designers treat them as **first-class screens** — often the states users see most often (a new IMS account, an empty search, a network blip).

| State | Purpose | Must include |
|---|---|---|
| **Empty** | First-use or zero-results | Explanation of *why* it's empty + one clear next action (not generic "No data") |
| **Loading** | System is working | Skeleton screens matching final layout (preferred over spinners for content-heavy views); spinners only for short, indeterminate actions |
| **Error** | Something failed | Plain-language explanation, what the user can do next, and a retry action — never a raw error code alone |
| **Success** | Confirms completion | Brief, specific confirmation ("Purchase order #4021 created") — avoid generic "Success!" toasts with no context |

### States checklist
- [ ] Empty states explain cause + offer a next step, styled consistently across the app
- [ ] Skeleton loaders match the shape of final content (not a generic centered spinner) for data-heavy views
- [ ] Errors are actionable and human-readable, technical detail available but not primary
- [ ] Success messages reference the specific object/action completed

---

## 19. Notification & Alert Design

- **Severity hierarchy:** info (blue) < success (green) < warning (amber) < critical/error (red). Visual weight should scale with severity — critical alerts get persistent placement, info toasts auto-dismiss.
- Toasts for transient confirmations (auto-dismiss 4–6s); banners for persistent, page-level conditions (e.g., "Sync failed 3 hours ago — retry"); inline messages for field/row-level issues.
- Never stack more than 2–3 toasts at once — batch or summarize ("3 items updated") instead.
- Destructive confirmations (delete, bulk remove) always require an explicit confirm step — never a single click with no undo path. Prefer **undo** over **confirm dialogs** where the action is reversible (faster UX, same safety).

### Notification checklist
- [ ] Severity is visually distinguishable at a glance (color + icon, not color alone — accessibility)
- [ ] Transient vs persistent messages use the correct pattern (toast vs banner vs inline)
- [ ] Destructive actions have confirm-or-undo, never silent execution
- [ ] Notifications batch instead of stacking endlessly

---

## 20. Navigation Patterns

- **Top nav** for cross-cutting global switches (workspace, org, notifications, account). **Side nav** for primary product sections. Don't duplicate the same items in both.
- **Breadcrumbs** for deep hierarchies (Warehouse > Zone B > Bin 14) — critical in WMS/IMS contexts where users navigate nested physical/logical structures.
- **Tabs** for switching between views of the *same* object (Order Details / Order History / Order Notes) — not for switching between unrelated sections.
- Active state must be unmistakable: background fill + weight change, not color alone.
- Keep top-level nav to 5–7 items max (Miller's Law, §27) — group overflow under a sensible parent ("More" is a last resort, not a first choice).

---

## 21. Sidebar Organization

```
┌────────────────────────┐
│ [Logo]           [⌄]   │  ← workspace switcher
├────────────────────────┤
│ 🔍 Search              │
├────────────────────────┤
│ ● Dashboard            │  ← active state: filled indicator + bg
│   Inventory            │
│     Products           │  ← nested, indented 16px
│     Stock Levels       │
│     Adjustments        │
│   Orders               │
│   Warehouses           │
│   Reports               │
├────────────────────────┤
│   Settings              │
│   Help & Support         │
├────────────────────────┤
│ [Avatar] User Name  [⌄] │
└────────────────────────┘
```

Rules:
- Primary items ungrouped at top; utility items (Settings, Help, Account) separated at the bottom with a divider.
- Section groups (2nd-level nav) shown via indentation, not by icon alone.
- Collapsed/icon-only sidebar state still needs tooltips on hover — never icon-only with zero text anywhere.
- Badge counts (unread, pending) attach to the relevant nav item — right-aligned, small, using semantic color only if it needs action.

---

## 22. Icon Usage Guidelines

- **One icon set, one stroke weight, one corner style** app-wide (e.g., all outline 1.5px stroke, or all filled — never mixed).
- Icons support text labels; they rarely replace them in dense enterprise software (exception: universally understood icons — search, close, settings — in space-constrained toolbars).
- Icon size scales with context: 16px inline with body text, 20px in nav items, 24px for feature/empty-state illustrations.
- Never use emoji as functional UI icons in professional software — emoji render inconsistently across OS/browser and read as informal.
- Every icon should be immediately recognizable without a label on first use, or it shouldn't be an icon-only affordance.

---

## 23. Button Hierarchy & CTA Prioritization

| Level | Style | Usage | Max per view |
|---|---|---|---|
| Primary | Solid, brand color | The one main action | 1 |
| Secondary | Outlined/bordered | Alternative but important actions | 1–2 |
| Tertiary/Ghost | Text-only or subtle | Low-emphasis actions | Several |
| Destructive | Solid or outlined red | Delete/remove/irreversible | As needed, always confirmed |

- **Only one primary (solid, high-contrast) button per view/section.** If a screen has 4 solid blue buttons, none of them reads as "the" action.
- Button size communicates importance: primary actions can be slightly larger; don't make secondary/tertiary buttons the same visual weight as primary.
- Order: in LTR contexts, primary action is typically rightmost in a button group (or full-width on mobile); cancel/back sits to its left, visually quieter.
- Icon-only buttons need a tooltip and an accessible label, always.

---

## 24. Interaction Design & Micro-interactions

- Motion should **clarify state change**, not decorate. Use easing curves that feel physical (ease-out for entering, ease-in for exiting), 150–250ms for most UI transitions — longer feels sluggish, shorter feels jarring.
- Hover states on every interactive element (even if subtle — background tint shift, border color) — absence of hover feedback reads as "unfinished."
- Loading states should appear within 100ms of an action if the response might take longer, so the interface never feels unresponsive.
- Use motion to preserve spatial continuity (e.g., a clicked row expands in place rather than a fully separate page reloading) where it helps users maintain context.
- **Avoid motion for motion's sake** — bouncing icons, parallax on dashboards, or animated gradients in a data-dense enterprise tool undermine perceived seriousness/reliability.

---

## 25. Accessibility Principles

- Color contrast: minimum WCAG AA — 4.5:1 for body text, 3:1 for large text (18px+/bold 14px+) and UI component boundaries.
- Never convey meaning by color alone — pair with icon, label, or pattern (critical for status/semantic color in tables and charts).
- All interactive elements reachable and operable via keyboard, with a visible focus ring (not `outline: none` with nothing replacing it).
- Touch targets minimum 44×44px on touch interfaces (WMS handheld scanners, mobile POS) — a common IMS/WMS failure is copying desktop-density tables directly onto tablet/scanner UIs.
- Form fields have programmatically associated labels (not placeholder-as-label — placeholders disappear on input and fail screen readers).
- Respect `prefers-reduced-motion` for users sensitive to animation.

---

## 26. Responsive Design Considerations

- Enterprise/dense apps often have a genuine **desktop-first** reality (warehouse ops on a monitor, inventory managers on a workstation) — but touch/tablet contexts (scanners, floor tablets) need distinct, not just "shrunk," layouts.
- Tables on narrow viewports: prioritize 2–3 essential columns, move the rest behind a row-expand or a "view details" action — never force horizontal scroll as the primary interaction.
- Sidebar collapses to icon-only or a hamburger drawer below a defined breakpoint (commonly ~1024px); don't just shrink it proportionally until it's unreadable.
- Sticky/fixed elements (headers, action bars) must not stack and consume more than ~20% of a small viewport's height.

---

## 27. UX Psychology Principles

- **Gestalt Principles** — Proximity (items close together are perceived as related), Similarity (same style = same function), Common Region (a shared border/background groups items), Continuity (aligned elements are read as a sequence). Use these instead of dividers/labels wherever possible — grouping via spacing is cleaner than grouping via boxes.
- **Hick's Law** — decision time increases with the number of choices. Reduce visible options (progressive disclosure, "More filters," grouped menus) rather than presenting every possible action at once.
- **Fitts's Law** — the time to acquire a target depends on its size and distance. Frequent/critical actions should be larger and closer to the user's current focus (e.g., row-level actions inline, not requiring a trip to a toolbar far away).
- **Progressive Disclosure** — show only what's needed now; defer advanced/rare options behind an explicit expansion. Prevents "settings-page-itis" where every screen tries to expose every possible option.
- **Miller's Law** — working memory holds ~7 (±2) items. Nav sections, filter categories, and stat groupings should chunk into groups of 5–7 rather than long flat lists.
- **Jakob's Law** — users spend most of their time on *other* products, and expect yours to work the same way. Don't reinvent standard patterns (hamburger = menu, magnifying glass = search) purely for novelty — novelty here reads as friction, not craft.

---

## 28. Visual Rhythm & Balance

- **Rhythm** comes from repeating consistent spacing/sizing intervals down a page — like a musical beat. Inconsistent gaps between sections (32px, then 40px, then 28px) create a subtle sense of disorder even if a user can't articulate why.
- **Balance** doesn't require symmetry. A dense data table on the left can be balanced by a lighter, well-spaced summary panel on the right — visual weight (density + color + size) should feel evenly distributed across the composition, not mirrored.
- Repetition of a small set of shapes/proportions (card widths, radii, icon sizes) across a layout creates a coherent visual "voice" — mixing many one-off proportions is what makes assembled/generated UIs feel disjointed.

---

## 29. Content Density & Scanning Behavior

- Match density to **user expertise and session frequency**: a warehouse operator scanning bins hundreds of times a day needs a dense, low-motion, high-contrast UI; a monthly executive dashboard viewer benefits from a lighter, more spacious layout.
- Professionals scan in **F-patterns** for text-heavy content and **Z-patterns** for sparse/landing-style content — align key information (titles, primary metric, primary action) to these paths.
- **Dense ≠ cluttered.** Density done well uses tight, *consistent* spacing and strong alignment; clutter is inconsistent spacing plus competing visual weights. Bloomberg Terminal-style density is legible because of rigorous alignment, not despite density.

---

## 30. Enterprise Dashboard Design Standards

- **Stability over novelty.** Enterprise users return daily — layout should stay stable release over release; don't reshuffle nav or dashboard modules without a strong reason (violates Jakob's Law and breaks muscle memory).
- **Role-based views.** An ops manager, a warehouse floor lead, and a CFO need different defaults from the same underlying data — avoid one-size-fits-all dashboards.
- **Export & auditability.** Enterprise contexts expect CSV/PDF export, timestamped data ("as of 14:32"), and clear data provenance (which system/integration a number came from).
- **Permission-aware UI.** Hide or disable (with explanation) actions a user's role can't perform, rather than showing an error only after they click.
- **Predictable, unsurprising interactions** — enterprise trust is built on the software behaving exactly the same way every time, not on delightful surprises.

---

## 31. SaaS Application Design Patterns

- **Progressive onboarding** — checklist-style setup guidance that disappears once complete, not a permanent nag.
- **Contextual empty states double as onboarding** — an empty "Products" table should explain what a product is and offer "Add your first product," not just say "No products found."
- **Settings organized by scope** — Personal → Workspace/Team → Billing → Integrations → Security, in that order of proximity to the individual user.
- **Command palette (Cmd/Ctrl+K)** for power users — a hallmark of modern SaaS (Linear, Notion, Vercel, GitHub) that dramatically reduces navigation friction for frequent users.
- **In-product changelog/"What's new"** rather than external-only release notes, keeps returning users oriented after updates.

---

## 32. Domain-Specific Guidance

### 32.1 Inventory Management Systems (IMS)

- Stock levels are the primary object — always show **current / incoming / allocated / available** distinctly, never collapse them into a single ambiguous "quantity."
- Low-stock and out-of-stock states need **semantic color + explicit label** ("3 left — below reorder point"), not color alone.
- SKU-heavy tables need strong search/filter (by SKU, category, location, supplier) and bulk actions (bulk reorder, bulk adjust).
- Barcode/SKU fields should support scanner input (auto-submit on scan) as a first-class interaction, not an afterthought on top of a mouse-first UI.
- Reorder point, lead time, and supplier information should be visible in context (row expansion or side panel) without a full page navigation.

### 32.2 Warehouse Management Systems (WMS)

- Floor-facing UIs (scanners, tablets) need **large touch targets, high contrast, minimal text entry**, and clear large-scale confirmation of scanned actions (visual + optional audio/haptic).
- Task-based flows (pick, pack, putaway, cycle count) should be **single-task-per-screen**, step-by-step, with a persistent progress indicator — not a dense multi-field form.
- Location/bin hierarchies (Warehouse > Zone > Aisle > Shelf > Bin) need consistent breadcrumb-style representation across every screen that references a location.
- Exceptions (damaged item, mismatch, short pick) need a fast, clearly-labeled escape path from the main flow — don't force a happy-path-only design.

### 32.3 Decision Support Systems (DSS)

- Lead with the **recommendation or insight**, then supporting data — not raw data first, conclusion buried at the bottom.
- Always expose **confidence/uncertainty** and the underlying assumptions or data window behind a recommendation — a DSS that hides its reasoning erodes trust.
- Provide **"what changed"** context (vs. last period, vs. forecast) alongside any recommended action, so users can sanity-check before acting.
- Support a clear **accept / override / request more detail** interaction pattern for AI/model-driven suggestions — never force blind trust in a single click.

### 32.4 Analytics Dashboards

- Default to the **most commonly asked question** as the landing view; let users customize/save views rather than forcing everyone through the same generic overview.
- Comparative context always accompanies a raw number (vs. last period, vs. target, vs. benchmark) — a number with no comparison is hard to interpret.
- Support **drill-down without losing place** (clicking a chart segment filters the same view rather than navigating away entirely).
- Time range and comparison-period controls should be persistent and global to the dashboard, not buried per-widget.

---

## 33. Real Product Design Principles

*(Used at Stripe, Linear, Notion, Vercel, GitHub, Figma, and Apple — observed patterns, described generally rather than reproduced verbatim.)*

- **Stripe** — extremely disciplined type scale and spacing; documentation-grade clarity; restrained color used almost entirely for semantic/status meaning; heavy use of monospace for technical/financial data.
- **Linear** — near-monochrome UI with a single accent color reserved for primary actions and active states; keyboard-first interaction model; motion is fast (100–150ms) and purposeful, never decorative.
- **Notion** — content-first design where the "chrome" (nav, toolbars) recedes almost entirely, letting user-generated content be the visual star; minimal icons, text-forward.
- **Vercel** — high contrast black/white base with a single accent, generous use of monospace, sharp/minimal corner radii, strong grid discipline.
- **GitHub** — data-dense, utilitarian tables and diffs; consistent, small icon set; status conveyed via compact colored badges/labels rather than large banners.
- **Figma** — toolbar-centric UI with clear, consistent iconography; contextual property panels that only show relevant controls for the selected object (progressive disclosure in action).
- **Apple (HIG)** — deep commitment to platform-consistent controls, generous but *purposeful* whitespace, restrained motion, and content that is never fighting chrome for attention.

**The common thread across all of them:** a small, disciplined design language applied with extreme consistency — not more decoration, less arbitrary variation.

---

## 34. Human Design Patterns AI Usually Misses

- **Optical corrections** — nudging an icon 1–2px off mathematical center so it *looks* centered; adjusting letter-spacing on all-caps labels; these micro-adjustments come from trained eyes, not templates.
- **Real content stress-testing** — designing with the longest realistic name, a $0 value, a 4-digit vs 7-digit number, a missing image — not idealized dummy data.
- **Domain-accurate terminology and icons** — a real IMS designer knows the difference between "allocated," "committed," and "available" stock; generic tools blur these into one "quantity" field.
- **Restraint as a decision** — deliberately choosing *not* to add a chart, a badge, or a color because it doesn't serve the user's task, even though it would "look nice."
- **Context-aware density** — the same designer will make a floor-scanner screen sparse/huge-touch-target and an analyst's table dense/compact, because they understand who's using it and how.
- **Consistent voice in microcopy** — tone, punctuation, capitalization style held consistent across every button/label/error in the entire product (Title Case vs sentence case, "Cancel" vs "Dismiss" — picked once and never mixed).
- **Knowing when to break the grid** — a senior designer will occasionally break an alignment rule for a specific, explainable reason (e.g., a hero metric slightly oversized to anchor a page) — never arbitrarily, and always with a justification they could defend in a design review.

---

## 35. Before vs After Examples

### Example A — Stat Card

**Before (generic/AI):**
```
┌────────────────────────────┐
│  🟣 (icon in gradient blob) │
│                             │
│      1,204                 │
│   Total Orders 🚀           │
│                             │
└────────────────────────────┘
(soft 20px shadow, 20px radius, purple/blue gradient icon badge)
```

**After (intentional/enterprise):**
```
┌────────────────────────────┐
│ Total Orders                │  ← 12px, gray-500, medium
│ 1,204                       │  ← 28px, gray-900, semibold, tabular nums
│ ↑ 4.2% vs last 30 days      │  ← 12px, green-600, with icon
└────────────────────────────┘
(1px gray-200 border, no shadow, 8px radius, no decorative icon/emoji)
```

### Example B — Navigation

**Before:** icon-only sidebar, all icons same weight/color, no active state distinction beyond a slightly darker icon, tooltip missing.

**After:** labeled sidebar, active item has filled background (gray-100) + left accent bar (2px, brand color) + medium-weight label; inactive items are gray-600 regular weight.

### Example C — Table Row Actions

**Before:** actions hidden in a "⋮" menu on every row, requiring 2 clicks for the most common action (e.g., "Edit").

**After:** the single most common action (Fitts's Law) rendered as a visible icon-button inline on hover/focus; less-common actions remain in the overflow menu.

---

## 36. Design Smells That Should Be Fixed

| Smell | Why it's a problem | Fix |
|---|---|---|
| More than 3 accent colors on one screen | Competing focal points, no hierarchy | Reduce to 1 accent + semantic set |
| Buttons with inconsistent heights across the app | Reads as unpolished/assembled | Standardize button component + variants |
| Shadows on every card, every depth | Flattens perceived hierarchy (everything "floats" equally) | Reserve shadow/elevation for genuinely overlapping/modal content |
| Center-aligned data tables | Slower scanning, unprofessional in dense data contexts | Left-align text, right-align numbers |
| Icons without labels in primary nav | Ambiguous, relies on memorization | Always pair icon + label in primary nav |
| Long, ungrouped settings pages | Violates Miller's Law, overwhelming | Chunk into 5–7 item groups with headers |
| Empty states that just say "No data" | Dead end, no path forward | Explain why + offer a next action |
| All spacing values eyeballed / inconsistent | Undermines rhythm and professionalism | Enforce the 8px spacing scale everywhere |

---

## 37. Professional Dashboard Layout Patterns

### Pattern 1 — Executive Summary
```
┌───────────────────────────────────────────────────────────┐
│ Page Title                                    [Date Range] │
├───────────────────────────────────────────────────────────┤
│ ┌─────────┬─────────┬─────────┬─────────┐                  │
│ │ KPI 1   │ KPI 2   │ KPI 3   │ KPI 4   │  ← one module     │
│ └─────────┴─────────┴─────────┴─────────┘                  │
├───────────────────────────────────────────────────────────┤
│ ┌───────────────────────────────┐ ┌───────────────────────┐│
│ │ Primary trend chart (2/3 wide)│ │ Ranked breakdown list ││
│ │                                │ │ (1/3 wide)             ││
│ └───────────────────────────────┘ └───────────────────────┘│
├───────────────────────────────────────────────────────────┤
│ Detail table (full width, below the fold)                  │
└───────────────────────────────────────────────────────────┘
```

### Pattern 2 — Operational / WMS Floor View
```
┌───────────────────────────────────────────────────────────┐
│ Current Task: Pick Order #4021           [Step 2 of 5]     │
├───────────────────────────────────────────────────────────┤
│                                                             │
│              Bin: A-14-03                                  │
│              Item: Widget X — Qty 4                        │
│                                                             │
│         [   large scan/confirm button   ]                  │
│                                                             │
│  Skip / Report Issue (quiet, secondary)                    │
└───────────────────────────────────────────────────────────┘
```

### Pattern 3 — Analyst Deep-Dive
```
┌───────────────────────────────────────────────────────────┐
│ Filters: [Date▾] [Category▾] [Location▾]   [+ More Filters]│
├───────────────────────────────────────────────────────────┤
│ Active filters: [Q3 2026 ×] [Warehouse B ×]   Clear all     │
├───────────────────────────────────────────────────────────┤
│ Chart (full width)                                          │
├───────────────────────────────────────────────────────────┤
│ Data table, sortable, exportable, sticky header             │
└───────────────────────────────────────────────────────────┘
```

---

## 38. Senior Designer Checklist Before Shipping

**Hierarchy & Focus**
- [ ] There is exactly one primary focal point per screen
- [ ] Hierarchy is legible at a glance without reading every word

**Grid & Spacing**
- [ ] All spacing uses the 8px scale, no arbitrary values
- [ ] Consistent padding within every instance of the same component

**Typography**
- [ ] No more than 2 typefaces, no more than 8 type-size tokens
- [ ] Tabular numerals in all tables/metrics

**Color**
- [ ] 60-30-10 balance roughly respected
- [ ] Semantic colors never used decoratively
- [ ] Contrast passes WCAG AA everywhere

**Components**
- [ ] Every button/input/card traces to one shared component definition
- [ ] Icon set, stroke weight, and sizing are consistent app-wide

**States**
- [ ] Empty, loading, error, and success states designed for every major view — not just the "happy path"
- [ ] Destructive actions have confirm-or-undo

**Content Realism**
- [ ] Designed and tested with real/edge-case content (long names, large numbers, zero states)

**Domain Fit**
- [ ] Density, terminology, and interaction model match the actual users (floor operator vs analyst vs executive)

**The Final Gut-Check**
- [ ] If you removed the logo/brand color, could someone still tell this wasn't a generic template?
- [ ] Could you defend every visual decision on this screen in a design review, one by one?
- [ ] Does the screen feel *inevitable* — like there was no other sensible way to design it?

---

*End of guide. Treat this as a living document — revisit and refine it as your product and design system mature, but never let its discipline erode into "close enough."*
