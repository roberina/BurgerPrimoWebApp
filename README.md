# BurgerPrimo Frontend Update — Setup Guide

## What Changed

### New Design (from viewfilescorrect.zip)
- **Navbar** — anchor scroll tracking with active dot indicator, Wolt/Bolt food delivery links, smooth glass blur on scroll
- **Footer** — 4-column layout with opening hours, collapsible mobile sections, scroll animations
- **Welcome page** — fullscreen blurred background image, AnnouncementBanner component
- **Admin Dashboard** — simplified stats + recent items list
- **Admin Menu Items** — category-grouped collapsible layout (much cleaner)
- **Menu page** — highlight item support, improved search

### New Features
- **AnnouncementBanner** — scrollable banner below navbar for promotions/news
- **ReviewModal** — customer review submission with star rating
- **Scroll animations** — `useScrollAnimation` composable for fade-up/stagger effects
- **New pages** — `/kontakt` and `/meelelahutus` standalone pages

### Bug Fixes (all `confirm()` / `alert()` replaced)
| File | Fix |
|------|-----|
| `Orders/Index.vue` | Custom modal for cancel/delete/dismiss |
| `BurgerBuilder/Index.vue` | Toast warning for max burger limit |
| `Admin/Ingredients/Index.vue` | Custom modal for delete |
| `Admin/Menu/Categories/Index.vue` | Custom modal for delete |
| `Admin/Menu/Items/Index.vue` | Custom modal for delete |
| `Admin/Reviews/Index.vue` | Custom modal for delete |
| `Admin/Announcements/Index.vue` | Custom modal for delete |
| `Payment/Checkout.vue` | Toast on success instead of `alert()` |
| `Admin/Orders/Index.vue` | Toast on status change |

### Toast Notifications Added
| Action | Toast |
|--------|-------|
| Add item to cart | ✓ "ItemName lisatud ostukorvi! 🛒" |
| Remove from cart | "Toode eemaldatud ostukorvist" |
| Clear cart | "Ostukorv tühjendatud" |
| Save burger | ✓ "Burger salvestatud! 🍔" |
| Order cart item | ✓ "Burger lisatud ostukorvi! 🛒" |
| Payment success | ✓ "Makse õnnestus! Tellimus #X esitatud ✓" |
| Profile saved | ✓ "Profiil edukalt uuendatud ✓" |
| Password changed | ✓ "Parool edukalt muudetud ✓" |
| Admin: status update | ✓ "Tellimus märgitud: Kinnitatud" |
| Admin: review approve | ✓ "Arvustus kinnitatud ✓" |
| Admin: addon toggle | ✓ "Saadavus uuendatud" |
| Admin: announcement save | ✓ "Teadaanne lisatud/uuendatud ✓" |

### New: Addon Items System (Database-backed)
Previously drinks, sizes, sauces, fries were **hardcoded** in the frontend.
Now they are managed from the admin panel at `/admin/addons`.

---

## Installation Steps

### 1. Copy Frontend Files
Replace your `resources/js/` folder with the contents of `resources/js/` from this package.
Replace your `resources/css/app.css` with the one from this package.

### 2. Run Database Migration
```bash
php artisan migrate
```
This creates the `addon_items` table.

### 3. Seed Default Addons
```bash
php artisan db:seed --class=AddonItemSeeder
```
This populates sizes, drinks, sauces, and fries with sensible defaults.

### 4. Update Routes (`routes/web.php`)
See `ROUTES_INSTRUCTIONS.php` for exact route code to add.

**Summary of routes to add:**
- `GET /api/addons` — public endpoint for addon modal
- `GET|POST|PUT|DELETE /admin/addons` — addon CRUD
- `GET|POST /admin/reviews` — review moderation
- `GET|POST|PUT|DELETE /admin/announcements` — announcement manager
- `GET /kontakt` — standalone contact page
- `GET /meelelahutus` — standalone entertainment page
- `POST /reviews` — public review submission
- Update `/` home route to pass `reviews` and `announcements`

### 5. Create Missing Backend Controllers
The following controllers need to be created (or already exist in your project):
- `App\Http\Controllers\Admin\AddonItemController` ✅ (included in this package)
- `App\Http\Controllers\Admin\ReviewController` — needs creating
- `App\Http\Controllers\Admin\AnnouncementController` — needs creating
- `App\Http\Controllers\ReviewController` — public review submission
- `App\Models\AddonItem` ✅ (included)
- `App\Models\Review` — needs creating + migration
- `App\Models\Announcement` — needs creating + migration

### 6. Build Assets
```bash
npm install
npm run build
# or for dev:
npm run dev
```

---

## File Structure of This Package

```
resources/
├── css/
│   └── app.css                          ← Updated with animation CSS
└── js/
    ├── composables/
    │   ├── useToast.ts                  ← NEW: Global toast system
    │   ├── useScrollAnimation.ts        ← NEW: Scroll animations
    │   └── ...existing...
    ├── components/
    │   ├── ToastContainer.vue           ← NEW: Toast renderer
    │   ├── AnnouncementBanner.vue       ← NEW: Promo banner
    │   ├── ReviewModal.vue              ← NEW: Customer reviews
    │   ├── Navbar.vue                   ← UPDATED
    │   ├── Footer.vue                   ← UPDATED (major redesign)
    │   └── Menu/
    │       ├── MenuItem.vue             ← UPDATED (loads real addons)
    │       └── AddonModal.vue           ← NEW: Extracted addon modal
    ├── layouts/
    │   └── AdminLayout.vue              ← UPDATED (Addons nav item)
    └── pages/
        ├── Welcome.vue                  ← UPDATED
        ├── Kontakt/Index.vue            ← NEW
        ├── Meelelahutus/Index.vue       ← NEW
        ├── Orders/Index.vue             ← FIXED (no more confirm())
        ├── BurgerBuilder/Index.vue      ← FIXED (no more alert())
        ├── Payment/Checkout.vue         ← FIXED (no more alert())
        ├── settings/
        │   ├── Profile.vue              ← FIXED + toasts
        │   └── Password.vue             ← FIXED + toasts
        └── Admin/
            ├── Addons/Index.vue         ← NEW: Full addon CRUD
            ├── Reviews/Index.vue        ← NEW + FIXED
            ├── Announcements/Index.vue  ← NEW + FIXED
            ├── Dashboard/Index.vue      ← UPDATED
            ├── Menu/Items/Index.vue     ← UPDATED + FIXED
            └── Menu/Categories/Index.vue ← UPDATED + FIXED

app/
├── Models/
│   └── AddonItem.php                    ← NEW
└── Http/Controllers/Admin/
    └── AddonItemController.php          ← NEW

database/
├── migrations/
│   └── 2026_04_06_000001_create_addon_items_table.php  ← NEW
└── seeders/
    └── AddonItemSeeder.php              ← NEW
```
