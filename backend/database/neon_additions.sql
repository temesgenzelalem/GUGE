-- ═══════════════════════════════════════════════════════════════
--  GUGE — Additional Tables
--  Run in Neon SQL Editor AFTER the main neon_complete_setup.sql
-- ═══════════════════════════════════════════════════════════════

-- ── Newsletter subscribers ────────────────────────────────────
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id         SERIAL PRIMARY KEY,
    email      VARCHAR(180) NOT NULL UNIQUE,
    created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

-- ── Contact form submissions ──────────────────────────────────
CREATE TABLE IF NOT EXISTS contact_submissions (
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    email      VARCHAR(180) NOT NULL,
    topic      VARCHAR(200) NOT NULL,
    message    TEXT         NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

-- ── Update triggers ───────────────────────────────────────────
CREATE TRIGGER trg_newsletter_updated
  BEFORE UPDATE ON newsletter_subscribers
  FOR EACH ROW EXECUTE FUNCTION update_updated_at();

CREATE TRIGGER trg_contact_updated
  BEFORE UPDATE ON contact_submissions
  FOR EACH ROW EXECUTE FUNCTION update_updated_at();

-- ── Full-text search index (for fast ILIKE queries) ───────────
CREATE INDEX IF NOT EXISTS idx_regions_fts
  ON regions USING gin(to_tsvector('english', name || ' ' || description || ' ' || zone));

CREATE INDEX IF NOT EXISTS idx_products_fts
  ON products USING gin(to_tsvector('english', name || ' ' || description || ' ' || story));

CREATE INDEX IF NOT EXISTS idx_stories_fts
  ON stories USING gin(to_tsvector('english', title || ' ' || excerpt));

-- ── Verify ────────────────────────────────────────────────────
SELECT 'newsletter_subscribers' AS table_name, COUNT(*) FROM newsletter_subscribers
UNION ALL
SELECT 'contact_submissions',                  COUNT(*) FROM contact_submissions;
