.PHONY: install dev api web db-setup help

help:
	@echo "GUGE Development Commands"
	@echo "========================="
	@echo "make install    — Install all dependencies"
	@echo "make api        — Start Laravel API on :8000"
	@echo "make web        — Start Next.js frontend on :3000"
	@echo "make dev        — Start both (requires 2 terminals)"
	@echo "make db-setup   — Print Neon SQL setup instructions"

install:
	@echo "Installing backend dependencies..."
	cd backend && composer install
	@echo "Installing frontend dependencies..."
	cd frontend && npm install
	@echo "Generating Laravel app key..."
	cd backend && php artisan key:generate
	@echo "✅ Done! Run 'make api' in one terminal and 'make web' in another."

api:
	cd backend && php artisan serve --port=8000

web:
	cd frontend && npm run dev

db-setup:
	@echo ""
	@echo "═══════════════════════════════════════════════════"
	@echo "  GUGE — Neon Database Setup"
	@echo "═══════════════════════════════════════════════════"
	@echo ""
	@echo "1. Go to console.neon.tech"
	@echo "2. Select the GuGe database"
	@echo "3. Click SQL Editor"
	@echo "4. Paste contents of: backend/database/neon_complete_setup.sql"
	@echo "5. Click Run"
	@echo "6. Then paste:         backend/database/neon_additions.sql"
	@echo ""
	@echo "Expected result: regions=22, products=32, stories=12, creators=8"
	@echo ""
