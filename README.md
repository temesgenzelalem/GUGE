# GUGE — Travel Ethiopia. Buy Ethiopia.

A full-stack platform connecting Ethiopian travel, culture, and authentic local
products through region-based discovery.

**Stack:**
- **Frontend** — Next.js 14 (App Router), Tailwind CSS, TanStack Query
- **Backend**  — Laravel 11 REST API
- **Database** — Neon PostgreSQL (serverless)

---

## 1. Set up the Neon database (do this first)

1. Open your Neon project at **console.neon.tech**
2. Select the **GuGe** database
3. Click **SQL Editor** in the left sidebar
4. Open the file `backend/database/neon_complete_setup.sql`
5. **Paste the entire file** into the SQL editor
6. Click **Run** — it will create all tables and seed all data
7. You should see at the bottom:
   ```
   regions  | 22
   products | 32
   stories  | 12
   creators |  8
   ```

---

## 2. Backend — Laravel 11

### Prerequisites
- PHP 8.2+
- Composer

### Install
```bash
cd backend

# Install dependencies
composer install

# Copy env (already configured for your Neon database)
cp .env .env.local     # .env already has the Neon credentials

# Generate app key
php artisan key:generate

# Test the database connection
php artisan tinker
>>> DB::select('SELECT COUNT(*) FROM regions');

# Start the API server
php artisan serve --port=8000
```

### Test API endpoints
```bash
# Health check
curl http://localhost:8000/api/health

# All regions
curl http://localhost:8000/api/regions

# Single region
curl http://localhost:8000/api/regions/lalibela

# Region products
curl http://localhost:8000/api/regions/harar/products

# Products filtered by category
curl "http://localhost:8000/api/products?category=coffee"

# All stories
curl http://localhost:8000/api/stories

# Search
curl "http://localhost:8000/api/search?q=coffee"
```

---

## 3. Frontend — Next.js 14

### Prerequisites
- Node.js 18+
- npm or yarn

### Install
```bash
cd frontend

# Install dependencies
npm install

# The .env.local already points to localhost:8000
# Start dev server
npm run dev
```

Open **http://localhost:3000**

### Build for production
```bash
npm run build
npm start
```

---

## Project structure

```
guge/
├── frontend/                     # Next.js 14
│   ├── app/
│   │   ├── page.tsx              # Homepage
│   │   ├── regions/
│   │   │   ├── page.tsx          # All regions
│   │   │   └── [slug]/page.tsx   # Single region detail
│   │   ├── marketplace/
│   │   │   ├── page.tsx          # All products
│   │   │   └── [slug]/page.tsx   # Single product detail
│   │   ├── stories/
│   │   │   ├── page.tsx          # All stories
│   │   │   └── [slug]/page.tsx   # Single story
│   │   └── creators/
│   │       └── page.tsx          # All creators
│   ├── components/
│   │   ├── layout/
│   │   │   ├── Navbar.tsx
│   │   │   └── Providers.tsx
│   │   ├── ui/
│   │   │   └── WikiImage.tsx     # Image with skeleton loader
│   │   └── sections/
│   │       ├── HeroSection.tsx
│   │       ├── SearchBar.tsx     # Live search dropdown
│   │       ├── RegionCard.tsx    # tile + full variants
│   │       ├── ProductCard.tsx
│   │       ├── StoryCard.tsx
│   │       ├── RegionFilters.tsx
│   │       ├── CategoryFilters.tsx
│   │       └── RegionTabs.tsx
│   ├── lib/
│   │   ├── api.ts                # All API calls (axios)
│   │   └── utils.ts              # cn(), slugify(), getWikiImageUrl()
│   └── types/index.ts            # All TypeScript interfaces
│
├── backend/                      # Laravel 11
│   ├── app/
│   │   ├── Models/
│   │   │   ├── Region.php
│   │   │   ├── Product.php
│   │   │   ├── Story.php
│   │   │   └── Creator.php
│   │   └── Http/Controllers/Api/
│   │       ├── RegionController.php
│   │       ├── ProductController.php
│   │       ├── StoryController.php
│   │       ├── CreatorController.php
│   │       └── SearchController.php
│   ├── routes/api.php            # All API routes
│   ├── config/
│   │   ├── database.php          # Neon SSL config
│   │   └── cors.php              # Allow Next.js origin
│   ├── database/
│   │   └── neon_complete_setup.sql  # ← RUN THIS IN NEON SQL EDITOR
│   └── .env                      # Neon credentials pre-configured
```

---

## API Reference

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/health` | Health check |
| GET | `/api/regions` | List all regions (filter: `?direction=north`) |
| GET | `/api/regions/{slug}` | Single region |
| GET | `/api/regions/{slug}/products` | Products in a region |
| GET | `/api/regions/{slug}/stories` | Stories in a region |
| GET | `/api/products` | List all products (filter: `?category=coffee`) |
| GET | `/api/products/{slug}` | Single product + related |
| GET | `/api/stories` | List all stories (filter: `?type=travel`) |
| GET | `/api/stories/{slug}` | Single story + related |
| GET | `/api/creators` | List all creators |
| GET | `/api/creators/{slug}` | Single creator + their stories |
| GET | `/api/search?q=...` | Search regions, products, stories |

---

## Data in the database (after running the SQL)

| Table | Records |
|-------|---------|
| Regions | 22 (Lalibela, Gondar, Harar, Aksum, Bahir Dar, Gojjam, Debre Markos, Yirgacheffe, Jimma, Omo Valley, Wolayta, Bale Mts, Simien Mts, Konso, Silte, Tiya, Danakil, Awash Valley, Gambela, Lake Langano, Tigray Churches, Dire Dawa) |
| Products | 32 (coffee, food, honey, craft, clothing) |
| Stories | 12 (full long-form articles) |
| Creators | 8 (photographers, writers, weavers, beekeepers) |

---

## Images

All images are loaded dynamically from **Wikipedia's free API**:
```
https://en.wikipedia.org/w/api.php?action=query&titles={article}&prop=pageimages
```
- Images are cached by Next.js ISR (1 hour revalidation)
- Skeleton shimmer loaders show while images fetch
- No image hosting required — zero storage cost
- All images are Wikimedia Commons licensed (free to use)

---

## Deployment

### Backend (Laravel) — any PHP host
```bash
# On your server
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan serve --host=0.0.0.0 --port=8000
```

### Frontend (Next.js) — Vercel (recommended)
```bash
# Push to GitHub, connect to Vercel
# Set environment variable in Vercel dashboard:
NEXT_PUBLIC_API_URL=https://your-laravel-api.com/api
```

---

## Neon database connection string
```
postgresql://neondb_owner:***@ep-billowing-glitter-apal87to-pooler.c-7.us-east-1.aws.neon.tech/GuGe?sslmode=require
```
Already configured in `backend/.env`.
