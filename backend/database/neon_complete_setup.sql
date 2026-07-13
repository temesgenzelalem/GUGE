-- ═══════════════════════════════════════════════════════════════
--  GUGE — Complete Database Setup for Neon PostgreSQL
--  Paste this entire file into the Neon SQL Editor and run.
--  Database: GuGe
-- ═══════════════════════════════════════════════════════════════

-- ── EXTENSIONS ──────────────────────────────────────────────
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pg_trgm"; -- for fast LIKE search

-- ── DROP (clean re-run) ──────────────────────────────────────
DROP TABLE IF EXISTS story_product  CASCADE;
DROP TABLE IF EXISTS story_region   CASCADE;
DROP TABLE IF EXISTS product_region CASCADE;
DROP TABLE IF EXISTS stories        CASCADE;
DROP TABLE IF EXISTS products       CASCADE;
DROP TABLE IF EXISTS creators       CASCADE;
DROP TABLE IF EXISTS regions        CASCADE;
DROP TABLE IF EXISTS migrations     CASCADE;

-- ═══════════════════════════════════════════════════════════════
--  TABLE: regions
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE regions (
    id           SERIAL PRIMARY KEY,
    name         VARCHAR(120)  NOT NULL,
    slug         VARCHAR(140)  NOT NULL UNIQUE,
    zone         VARCHAR(120)  NOT NULL,
    direction    VARCHAR(10)   NOT NULL CHECK (direction IN ('north','south','east','west')),
    description  TEXT          NOT NULL,
    tagline      VARCHAR(220)  NOT NULL,
    wiki_article VARCHAR(220)  NOT NULL,
    image_url    TEXT,
    tags         JSONB         NOT NULL DEFAULT '[]',
    stats        JSONB         NOT NULL DEFAULT '[]',
    created_at   TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    updated_at   TIMESTAMPTZ   NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_regions_slug      ON regions (slug);
CREATE INDEX idx_regions_direction ON regions (direction);
CREATE INDEX idx_regions_tags      ON regions USING GIN (tags);

-- ═══════════════════════════════════════════════════════════════
--  TABLE: creators
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE creators (
    id               SERIAL PRIMARY KEY,
    name             VARCHAR(200),
    full_name        VARCHAR(200) NOT NULL,
    username         VARCHAR(100) UNIQUE,
    slug             VARCHAR(140) NOT NULL UNIQUE,
    region_id        INTEGER REFERENCES regions(id) ON DELETE SET NULL,
    role             VARCHAR(120),
    bio              TEXT,
    status           VARCHAR(40) NOT NULL DEFAULT 'published',
    specialties      JSONB,
    languages        JSONB,
    social_links     JSONB,
    contact_email    VARCHAR(180),
    website_url      TEXT,
    portfolio_url    TEXT,
    wiki_article     VARCHAR(220),
    image_url        TEXT,
    rating           NUMERIC(3,2) NOT NULL DEFAULT 0,
    review_count     INTEGER NOT NULL DEFAULT 0,
    story_count      INTEGER NOT NULL DEFAULT 0,
    product_count    INTEGER NOT NULL DEFAULT 0,
    meta_title       VARCHAR(120),
    meta_description TEXT,
    created_at       TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    updated_at       TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    deleted_at       TIMESTAMPTZ
);

CREATE INDEX idx_creators_slug   ON creators (slug);
CREATE INDEX idx_creators_region ON creators (region_id);

-- ═══════════════════════════════════════════════════════════════
--  TABLE: products
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE products (
    id           SERIAL PRIMARY KEY,
    name         VARCHAR(180)  NOT NULL,
    slug         VARCHAR(200)  NOT NULL UNIQUE,
    region_id    INTEGER       NOT NULL REFERENCES regions(id) ON DELETE CASCADE,
    category     VARCHAR(30)   NOT NULL CHECK (category IN ('coffee','food','craft','honey','clothing')),
    description  TEXT          NOT NULL,
    story        TEXT          NOT NULL,
    wiki_article VARCHAR(220)  NOT NULL,
    image_url    TEXT,
    tags         JSONB         NOT NULL DEFAULT '[]',
    how_to_order TEXT          NOT NULL DEFAULT 'Contact seller via WhatsApp or email.',
    created_at   TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    updated_at   TIMESTAMPTZ   NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_products_slug      ON products (slug);
CREATE INDEX idx_products_region    ON products (region_id);
CREATE INDEX idx_products_category  ON products (category);
CREATE INDEX idx_products_tags      ON products USING GIN (tags);

-- ═══════════════════════════════════════════════════════════════
--  TABLE: stories
-- ═══════════════════════════════════════════════════════════════
CREATE TABLE stories (
    id           SERIAL PRIMARY KEY,
    title        VARCHAR(260)  NOT NULL,
    slug         VARCHAR(280)  NOT NULL UNIQUE,
    region_id    INTEGER       NOT NULL REFERENCES regions(id) ON DELETE CASCADE,
    creator_id   INTEGER       REFERENCES creators(id) ON DELETE SET NULL,
    type         VARCHAR(30)   NOT NULL CHECK (type IN ('travel','product-origin','culture','festival','history','craft')),
    excerpt      TEXT          NOT NULL,
    body         TEXT          NOT NULL,
    wiki_article VARCHAR(220)  NOT NULL,
    image_url    TEXT,
    read_minutes INTEGER       NOT NULL DEFAULT 5,
    published_at TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    created_at   TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    updated_at   TIMESTAMPTZ   NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_stories_slug    ON stories (slug);
CREATE INDEX idx_stories_region  ON stories (region_id);
CREATE INDEX idx_stories_type    ON stories (type);

-- ── UPDATE TRIGGER ───────────────────────────────────────────
CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER AS $$
BEGIN NEW.updated_at = NOW(); RETURN NEW; END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_regions_updated  BEFORE UPDATE ON regions  FOR EACH ROW EXECUTE FUNCTION update_updated_at();
CREATE TRIGGER trg_products_updated BEFORE UPDATE ON products FOR EACH ROW EXECUTE FUNCTION update_updated_at();
CREATE TRIGGER trg_stories_updated  BEFORE UPDATE ON stories  FOR EACH ROW EXECUTE FUNCTION update_updated_at();
CREATE TRIGGER trg_creators_updated BEFORE UPDATE ON creators FOR EACH ROW EXECUTE FUNCTION update_updated_at();

-- ═══════════════════════════════════════════════════════════════
--  SEED: REGIONS (22 regions)
-- ═══════════════════════════════════════════════════════════════
INSERT INTO regions (name, slug, zone, direction, description, tagline, wiki_article, tags, stats) VALUES

('Lalibela','lalibela','Amhara Region','north',
 'Eleven 12th-century rock-hewn churches carved from single blocks of red volcanic rock. One of Africa''s greatest sacred sites, still active with priests, pilgrims and incense smoke.',
 'Where stone becomes cathedral',
 'Church_of_Saint_George,_Lalibela',
 '["Rock churches","UNESCO","Pilgrimage","Orthodox"]',
 '[{"label":"Churches carved from rock","value":"11"},{"label":"Years of continuous worship","value":"850+"},{"label":"UNESCO inscribed","value":"1978"},{"label":"Annual pilgrims","value":"100k+"}]'),

('Gondar','gondar','Amhara Region','north',
 'City of castles — Fasil Ghebbi''s royal enclosure, Gondarine fresco painting, and the annual Timkat festival at Fasilides Bath. The Jerusalem of Africa.',
 'City of castles and ancient kings',
 'Fasil_Ghebbi',
 '["Castles","UNESCO","Timkat","Gondarine art"]',
 '[{"label":"Royal castle complex (hectares)","value":"70,000"},{"label":"UNESCO inscribed","value":"1979"},{"label":"Churches inside enclosure","value":"44"},{"label":"Timkat pilgrims annually","value":"50k+"}]'),

('Aksum','aksum','Tigray Region','north',
 'Capital of the Aksumite Empire. Towering granite stelae, Queen of Sheba''s palace ruins, and the legendary resting place of the Ark of the Covenant.',
 'Where empires were born in stone',
 'Obelisk_of_Axum',
 '["Obelisks","Ancient empire","UNESCO","Ark of Covenant"]',
 '[{"label":"Tallest standing stele (m)","value":"24"},{"label":"Years of empire","value":"1000+"},{"label":"UNESCO inscribed","value":"1980"},{"label":"Known stelae","value":"175+"}]'),

('Bahir Dar','bahir-dar','Amhara Region','north',
 'Gateway to the Blue Nile Falls (Tis Isat) and Lake Tana''s 37 island monasteries, home to illuminated manuscripts, medieval frescoes and centuries of monastic life.',
 'Where the Nile begins its journey',
 'Blue_Nile_Falls',
 '["Blue Nile","Lake Tana","Monasteries","Falls"]',
 '[{"label":"Island monasteries on Lake Tana","value":"37"},{"label":"Fall height (m)","value":"45"},{"label":"Lake area (km²)","value":"3,156"},{"label":"Ancient manuscripts housed","value":"500+"}]'),

('Gojjam','gojjam','Amhara Region','north',
 'Fertile highland plateau of the Gojjam Amhara — source of Ethiopia''s finest white teff. Surrounded by the great bend of the Blue Nile and rolling farmland.',
 'The white teff heartland of Ethiopia',
 'Gojjam',
 '["Nech Teff","Blue Nile","Farming","Amhara culture"]',
 '[{"label":"Teff varieties grown","value":"6+"},{"label":"Blue Nile border (km)","value":"300+"},{"label":"Altitude range (m)","value":"1800–3000"},{"label":"Major market towns","value":"12"}]'),

('Debre Markos','debre-markos','East Gojjam, Amhara','north',
 'Capital of East Gojjam and the beating heart of nech teff farming. Ancient Orthodox churches, the Abay (Blue Nile) gorge nearby, and the Gojjam highland market culture.',
 'Capital of the white teff plains',
 'Debre_Markos',
 '["Nech Teff","Orthodox","Market town","Gojjam"]',
 '[{"label":"Altitude (m)","value":"2,446"},{"label":"Distance from Addis (km)","value":"299"},{"label":"Major churches","value":"7+"},{"label":"Weekly market days","value":"2"}]'),

('Harar','harar','Harari Region','east',
 'Fourth holiest city of Islam in Africa. 82 mosques packed inside 16th-century walls, the hyena feeding tradition, Harari basket weaving, and one of the oldest coffees in the world.',
 'Walled city of saints, hyenas and ancient craft',
 'Harar',
 '["UNESCO","Walled city","Coffee","Islamic heritage","Basket weaving"]',
 '[{"label":"Mosques inside the walls","value":"82"},{"label":"Years the wall has stood","value":"368"},{"label":"Languages spoken inside","value":"6"},{"label":"Local product types","value":"24"}]'),

('Dire Dawa','dire-dawa','Dire Dawa City','east',
 'Built by the Addis–Djibouti railway in 1902. A cosmopolitan trading crossroads where Somali, Oromo, Amhara and Afar cultures converge around a colonial-era market.',
 'Ethiopia''s railway crossroads city',
 'Dire_Dawa',
 '["Railway","Trade hub","Multicultural","Market"]',
 '[{"label":"Ethnic groups in city","value":"8+"},{"label":"Year railway arrived","value":"1902"},{"label":"Distance to Djibouti (km)","value":"311"},{"label":"Altitude (m)","value":"1,180"}]'),

('Danakil Depression','danakil-depression','Afar Region','east',
 'One of the hottest and lowest places on Earth. Erta Ale''s permanent lava lake has burned for over a century. Salt caravans cross the Danakil daily as they have for millennia.',
 'The hottest place on Earth, alive with fire',
 'Danakil_Depression',
 '["Lava lake","Hottest place","Afar","Salt caravans"]',
 '[{"label":"Metres below sea level","value":"125"},{"label":"Average daily temp (°C)","value":"34"},{"label":"Years Erta Ale has burned","value":"100+"},{"label":"Salt camel caravans weekly","value":"50+"}]'),

('Awash Valley','awash-valley','Afar Region','east',
 'Ethiopia''s first national park. Volcanic hot springs, oryx, baboons and the gateway to Afar nomadic territory along the Awash River. Lucy was found near here.',
 'Where the Rift Valley begins',
 'Awash_National_Park',
 '["Wildlife","Hot springs","Afar culture","National park"]',
 '[{"label":"Park area (km²)","value":"756"},{"label":"Bird species","value":"400+"},{"label":"Mammal species","value":"79"},{"label":"Year Lucy was found nearby","value":"1974"}]'),

('Yirgacheffe','yirgacheffe','Sidama Region','south',
 'Birthplace of the world''s finest arabica. Smallholder farms at 1,800m altitude, the Kochere cooperative forests, traditional coffee ceremonies, and the Sidama cultural heartland.',
 'Where coffee was born and perfected',
 'Yirgacheffe',
 '["Coffee origin","Sidama culture","Farms","Single-origin"]',
 '[{"label":"Farm altitude (m)","value":"1,700–2,200"},{"label":"Cooperative members","value":"5,000+"},{"label":"Coffee varietals","value":"12+"},{"label":"Years of coffee cultivation","value":"600+"}]'),

('Jimma','jimma','Oromia Region','west',
 'Birthplace of coffee as a beverage. Ancient wild coffee forests, Oromo highland culture, and the historic kingdom of Kaffa where the word ''coffee'' itself originated.',
 'The kingdom where coffee was born',
 'Jimma',
 '["Wild coffee","Oromo","Forest","Kaffa kingdom"]',
 '[{"label":"Wild coffee forest (km²)","value":"400+"},{"label":"Coffee varieties in forest","value":"1000s"},{"label":"Altitude (m)","value":"1,760"},{"label":"Historic kingdom age (years)","value":"500+"}]'),

('Omo Valley','omo-valley','South Ethiopia Region','south',
 'Home to over 16 distinct ethnic groups — the Mursi, Hamar, Karo and Dassanech — with traditions and body art practices unchanged for millennia.',
 'The living museum of human diversity',
 'Omo_Valley',
 '["Mursi","Hamar","Tribes","Body art","UNESCO"]',
 '[{"label":"Distinct ethnic groups","value":"16+"},{"label":"River length (km)","value":"760"},{"label":"UNESCO Biosphere","value":"1980"},{"label":"Languages spoken","value":"20+"}]'),

('Wolayta','wolayta','South Ethiopia Region','south',
 'Kingdom of enset culture and Dorze master weavers. The false banana plant (enset) feeds over 20 million Ethiopians in the southern highlands.',
 'The enset kingdom of the south',
 'Wolayita',
 '["Enset","Dorze weaving","Culture","Sodo"]',
 '[{"label":"People fed by enset","value":"20M+"},{"label":"Weaving family traditions (years)","value":"500+"},{"label":"Altitude (m)","value":"1,500–3,000"},{"label":"Enset varieties cultivated","value":"200+"}]'),

('Bale Mountains','bale-mountains','Oromia Region','south',
 'Africa''s second-highest plateau. Last refuge of the Ethiopian wolf, giant mole rats and Afroalpine moorland. A UNESCO Biosphere Reserve unlike anywhere on Earth.',
 'The last home of the Ethiopian wolf',
 'Bale_Mountains_National_Park',
 '["UNESCO","Ethiopian wolf","Cloud forest","Trekking"]',
 '[{"label":"Ethiopian wolves remaining","value":"500+"},{"label":"Park area (km²)","value":"2,200"},{"label":"Bird species","value":"281"},{"label":"Highest peak (m)","value":"4,377"}]'),

('Simien Mountains','simien-mountains','Amhara Region','north',
 'UNESCO World Heritage dramatic escarpments, home to the gelada baboon found only in Ethiopia, and the Walia ibex. East Africa''s finest mountain trekking.',
 'Roof of Africa, home of the gelada',
 'Simien_Mountains_National_Park',
 '["UNESCO","Gelada baboon","Trekking","Escarpment"]',
 '[{"label":"UNESCO inscribed","value":"1978"},{"label":"Gelada baboons","value":"5,000+"},{"label":"Highest peak Ras Dashen (m)","value":"4,550"},{"label":"Park area (km²)","value":"412"}]'),

('Silte','silte','Central Ethiopia Region','south',
 'Known for traditional Silte weaving on hand looms, vibrant music traditions, and a unique cultural identity blending Oromo, Amhara and Silte heritage.',
 'Where the loom speaks in color',
 'Silte_Zone',
 '["Weaving","Music","Culture","Hand-loom"]',
 '[{"label":"Weaving families","value":"10,000+"},{"label":"Loom types used","value":"3"},{"label":"Distinct fabric patterns","value":"50+"},{"label":"Years of weaving tradition","value":"300+"}]'),

('Konso','konso','South Ethiopia Region','south',
 'UNESCO-listed Konso Cultural Landscape — dry-stone terraced hillsides and the extraordinary waga wooden ancestor sculptures placed over warriors'' graves.',
 'Stone terraces and ancestor sculptures',
 'Konso',
 '["UNESCO","Waga sculpture","Terraces","Prehistoric"]',
 '[{"label":"UNESCO inscribed","value":"2011"},{"label":"Terrace levels on hillsides","value":"50+"},{"label":"Waga sculptures standing","value":"500+"},{"label":"Years of terrace farming","value":"400+"}]'),

('Tiya','tiya','Gurage Zone, Central Ethiopia','south',
 'UNESCO-listed field of 36 decorated monolithic stelae from a prehistoric culture. The symbols carved on each stone remain only partially understood by archaeologists.',
 'Ancient stones with unread messages',
 'Tiya',
 '["UNESCO","Stelae","Prehistoric","Mystery"]',
 '[{"label":"UNESCO inscribed","value":"1980"},{"label":"Decorated stelae","value":"36"},{"label":"Stone age (years)","value":"1000+"},{"label":"Symbols deciphered","value":"partial"}]'),

('Lake Langano','lake-langano','Oromia Region','south',
 'The only bilharzia-free lake in Ethiopia''s Great Rift Valley. Reddish waters, weaver bird colonies, pelicans, and sweeping views across to the Bale Mountains escarpment.',
 'The Rift Valley''s safest lake',
 'Lake_Langano',
 '["Rift Valley","Swimming","Wildlife","Resort"]',
 '[{"label":"Lake area (km²)","value":"230"},{"label":"Bird species recorded","value":"180+"},{"label":"Altitude (m)","value":"1,585"},{"label":"Bilharzia risk","value":"None"}]'),

('Gambela','gambela','Gambela Region','west',
 'Remote western region bordering South Sudan. The Gambela National Park hosts Africa''s second-largest wildlife migration — one of the least-visited wild places in Africa.',
 'Africa''s secret wildlife migration',
 'Gambela',
 '["Nuer people","Wildlife","Wetlands","Migration"]',
 '[{"label":"Antelopes in migration","value":"1M+"},{"label":"Park area (km²)","value":"5,061"},{"label":"Ethnic groups","value":"5"},{"label":"Distance from Addis (km)","value":"769"}]'),

('Tigray Rock Churches','tigray-rock-churches','Tigray Region','north',
 'Over 120 rock-hewn churches scattered across Tigray''s dramatic sandstone cliffs and mountains, many accessible only by rope. Older and more remote than Lalibela.',
 'Hidden churches carved in cliffs',
 'Tigray_churches',
 '["Rock churches","Cliff","Orthodox","Remote"]',
 '[{"label":"Known rock churches","value":"120+"},{"label":"Oldest church (years)","value":"1600+"},{"label":"Accessible by rope only","value":"Many"},{"label":"Frescoes surviving","value":"100+"}]');

-- ═══════════════════════════════════════════════════════════════
--  SEED: CREATORS (8 creators)
-- ═══════════════════════════════════════════════════════════════
INSERT INTO creators (name, full_name, slug, role, bio, wiki_article) VALUES
('Dawit Abebe','Dawit Abebe','dawit-abebe','Documentary photographer','Addis-born photographer spending months each year embedded in coffee-farming communities across Yirgacheffe and Jimma. His work has appeared in National Geographic and BBC Travel.','Coffee_production_in_Ethiopia'),
('Amina Suleiman','Amina Suleiman','amina-suleiman','Visual storyteller','Born inside the Jugol walls. Documents everyday life of Harari women — the weavers, the coffee sellers, and the hyena keepers at the city gate at dusk.','Harar'),
('Tigist Alemu','tigist-alemu','Culture writer & historian','Historian and essayist specializing in Ethiopian Orthodox heritage. Author of three books on Gondarine architecture and the role of the church in highland life.','Fasil_Ghebbi'),
('Bekele Worku','bekele-worku','Artisan weaver & seller','Third-generation Dorze master weaver from the Gamo highlands. His family has woven shemma cotton for 80 years. Ships directly to diaspora buyers in Europe and North America.','Dorze_people'),
('Fatuma Abdullahi','fatuma-abdullahi','Master basket weaver','Third-generation Harari basket weaver whose family patterns are among the most recognized in the region. Teaches the craft to Harari schoolgirls each weekend.','Harar'),
('Yonas Tesfaye','yonas-tesfaye','Travel writer & guide','Grew up in Arba Minch. Has spent 12 years guiding anthropologists, photographers and travelers through the Omo Valley''s 16 ethnic communities.','Omo_Valley'),
('Mulu Haile','mulu-haile','Beekeeper & honey producer','Traditional log-hive beekeeper in the Tigray escarpment. Harvests twice yearly, seals in beeswax, and ships raw dark honey to a small international network of buyers.','Beekeeping_in_Ethiopia'),
('Selam Girma','selam-girma','Food producer & storyteller','Wolayta farmer and community educator who documents the enset food system — kocho, bulla, amicho — and advocates for its recognition as a UNESCO food heritage.','Ensete_ventricosum');

-- ═══════════════════════════════════════════════════════════════
--  SEED: PRODUCTS (32 products)
--  region_id references the SERIAL ids from the INSERT above
--  We use subqueries to get ids by slug for safety
-- ═══════════════════════════════════════════════════════════════
INSERT INTO products (name, slug, region_id, category, description, story, wiki_article, tags, how_to_order) VALUES

-- COFFEE
('Yirgacheffe Natural Coffee','yirgacheffe-natural-coffee',
 (SELECT id FROM regions WHERE slug='yirgacheffe'),
 'coffee',
 'Sun-dried at 1,800m on raised beds. Floral, jasmine, blueberry — a cup with a geography.',
 'This coffee is not just from Yirgacheffe — it is of Yirgacheffe. Grown by smallholder farmers in the Sidama forests, sun-dried on raised beds for 15 days. The natural process leaves the fruit''s sweetness in every bean. Floral, blueberry, jasmine. A coffee with a geography you can taste.',
 'Coffee_production_in_Ethiopia',
 '["Single-origin","Natural processed","Specialty","Sidama"]',
 'Contact the Kochere Cooperative directly via WhatsApp: +251 91 XXX XXXX'),

('Jimma Wild Forest Coffee','jimma-wild-forest-coffee',
 (SELECT id FROM regions WHERE slug='jimma'),
 'coffee',
 'Coffee as it was before cultivation — wild trees in the ancient forests of Kaffa.',
 'In the forests around Jimma, coffee grows wild. No cultivation, no fertilizer — trees that have grown in the same spot for centuries, producing beans of extraordinary complexity. This is coffee as it was before humans started farming it. Earthy, wine-like, with deep berry and spice notes.',
 'Coffea_arabica',
 '["Wild-grown","Forest coffee","Organic","Kaffa"]',
 'Contact Jimma Forest Cooperative via email for export orders'),

('Harrar Long Berry Coffee','harrar-long-berry-coffee',
 (SELECT id FROM regions WHERE slug='harar'),
 'coffee',
 'The wine of Ethiopian coffees — dry processed, aged, berry and leather.',
 'Grown on ancient farms in eastern Ethiopia, Harrar Long Berry is dry-processed and aged in burlap sacks. The result is a wild, complex cup — berry, wine, leather, chocolate. Called the "mocha" of Ethiopia, it has been traded from the Port of Mocha for 500 years.',
 'Harar_(city)',
 '["Dry processed","Mocha","Aged","Long berry"]',
 'Order via Harrar Coffee Traders Association'),

('Habesha Coffee Ceremony Set','coffee-ceremony-set',
 (SELECT id FROM regions WHERE slug='yirgacheffe'),
 'coffee',
 'Clay jebena pot, finjan cups, charcoal burner and grass tray — the complete bunna set.',
 'A complete Ethiopian coffee ceremony set: a clay jebena pot, small finjan cups, charcoal burner, incense holder and grass-lined serving tray. The ritual that coffee was born for — three rounds, each with a different name: abol, tona, baraka. To refuse the third round is to refuse a blessing.',
 'Coffee_ceremony_(Ethiopia)',
 '["Ceremony","Handmade","Clay","Ritual"]',
 'Contact Addis Ababa craft market sellers via GUGE marketplace'),

-- FOOD
('Nech Teff (White Teff) Grain','nech-teff-white-teff',
 (SELECT id FROM regions WHERE slug='gojjam'),
 'food',
 'The finest white teff from Gojjam highlands — lighter, more aromatic, prized for injera.',
 'In the highlands around Debre Markos, farmers have grown white teff for generations. The finest variety — nech teff — is lighter in color, more aromatic, and commands a premium across Ethiopia. Stone-milled and traditionally fermented for three days to make the lightest, most sour injera in the country.',
 'Eragrostis_tef',
 '["White teff","Gluten-free","Gojjam","Premium grain"]',
 'Contact Gojjam Farmers Cooperative for bulk orders'),

('Injera (Fermented Teff Flatbread)','injera',
 (SELECT id FROM regions WHERE slug='gojjam'),
 'food',
 'Plate and food in one — sourdough teff baked on a mitad griddle.',
 'Injera is not just Ethiopia''s national bread — it is the plate itself. A sourdough-fermented teff batter poured onto a hot mitad clay griddle. The result: a spongy, sour flatbread used to scoop every stew, salad and sauce at every Ethiopian meal. The fermentation takes three days. The tradition takes a lifetime to master.',
 'Injera',
 '["Fermented","National dish","Teff","Sourdough"]',
 'Available from local Ethiopian restaurants and diaspora food suppliers'),

('Berbere Spice Blend','berbere-spice-blend',
 (SELECT id FROM regions WHERE slug='gondar'),
 'food',
 'Chili, fenugreek, cardamom, ginger — every family''s own ancient recipe.',
 'Berbere is not one recipe — it is a thousand. Dried chilies, fenugreek, cardamom, ginger, cumin, cinnamon, rue. Every household in Ethiopia keeps its own ratio, passed from mother to daughter. The Amhara berbere of Gondar is different from the Tigrayan blend, which is different from the Oromo mitmita. This is living culinary heritage.',
 'Berbere_(spice_mixture)',
 '["Spice blend","Chili","Family recipe","Essential"]',
 'Order from Ethiopian spice traders or contact GUGE marketplace'),

('Shiro (Chickpea Powder)','shiro-chickpea-powder',
 (SELECT id FROM regions WHERE slug='gondar'),
 'food',
 'Ground spiced chickpea — the backbone of Ethiopian fasting cuisine.',
 'Shiro is ground chickpea and spice powder, slow-cooked with water and onion into a thick stew. It is the backbone of Ethiopian fasting cuisine — eaten on 200+ religious fasting days each year when meat and dairy are forbidden. Simple, nutritious, deeply comforting. The great equalizer of Ethiopian food.',
 'Shiro_(food)',
 '["Fasting food","Chickpea","Vegan","Lent"]',
 'Available from Ethiopian grocery stores worldwide'),

('Niter Kibbeh (Spiced Clarified Butter)','niter-kibbeh',
 (SELECT id FROM regions WHERE slug='gondar'),
 'food',
 'Clarified butter infused with onion, garlic, turmeric and fenugreek — the base of every stew.',
 'Every great Ethiopian stew begins with niter kibbeh — clarified butter slowly infused with onion, garlic, ginger, turmeric, fenugreek, cardamom and black cumin. The spices are strained out after hours of simmering, leaving a golden aromatic cooking fat that is the signature flavor of Ethiopian cuisine.',
 'Niter_kibbeh',
 '["Spiced butter","Cooking fat","Aromatic","Amhara"]',
 'Order from specialty Ethiopian food suppliers'),

('Kocho (Enset Bread)','kocho-enset-bread',
 (SELECT id FROM regions WHERE slug='wolayta'),
 'food',
 'Fermented false banana bread — the staple of 20 million Ethiopians in the south.',
 'Kocho is made from the fermented pulp of the false banana plant (enset). The process takes years: the plant is harvested, the pulp scraped and fermented underground in a pit for up to three years. The result is a dense, slightly sour bread that can be stored for years. It feeds over 20 million Ethiopians in the south as the primary starch.',
 'Ensete_ventricosum',
 '["Enset","Fermented","South Ethiopia","Wolayta"]',
 'Available from Wolayta community food cooperatives'),

('Mitmita (Ethiopian Bird Eye Spice)','mitmita-spice',
 (SELECT id FROM regions WHERE slug='jimma'),
 'food',
 'Powdered bird''s eye chili, cardamom and cloves — sharp, fragrant heat.',
 'Mitmita is Ethiopia''s fiery finishing spice: powdered bird''s eye chili blended with cardamom, cloves and African bird pepper. It is sprinkled on raw kitfo (Ethiopian beef tartare) and tibs at the table. The heat is immediate and intense; the cardamom perfume lingers. A few grains transform everything they touch.',
 'Mitmita',
 '["Mitmita","Bird eye chili","Finishing spice","Hot"]',
 'Order from Jimma spice traders or Ethiopian grocery suppliers'),

('Doro Wat (Spiced Chicken Stew)','doro-wat',
 (SELECT id FROM regions WHERE slug='gondar'),
 'food',
 'Ethiopia''s national dish — chicken slow-stewed in berbere, served on Timkat.',
 'Doro Wat is Ethiopia''s national dish: chicken pieces slow-stewed for hours in a deep red berbere and kibbeh sauce, enriched with a whole hard-boiled egg per person. It is the dish of celebrations — Timkat, Ethiopian Christmas, weddings and homecomings. To cook a proper doro wat requires 12 hours and the right hand.',
 'Doro_wat',
 '["National dish","Berbere","Chicken","Celebration"]',
 'Served in Ethiopian restaurants worldwide'),

-- HONEY & TEJ
('Gondar Tej (Ethiopian Honey Wine)','gondar-tej',
 (SELECT id FROM regions WHERE slug='gondar'),
 'honey',
 'Fermented with white honey and gesho shrub in clay pots, served in birille flasks.',
 'Tej is Ethiopia''s ancient honey wine. White honey and gesho shrub (a bitter buckthorn) are fermented together in clay pots for weeks, then strained into tall glass birille flasks. In Gondar''s tej bet bars, it has been served this way since the Aksumite era. Lightly sweet, gently bitter, mildly alcoholic — the wine of kings.',
 'Tej_(drink)',
 '["Mead","Honey wine","Gesho","Ceremonial","Gondar"]',
 'Order from Gondar tej producers via GUGE marketplace'),

('Tigray Highland Honey (Raw)','tigray-highland-honey',
 (SELECT id FROM regions WHERE slug='aksum'),
 'honey',
 'Dark raw honey sealed in beeswax from traditional log-hive beekeepers in Tigray cliffs.',
 'Log hives are hung in the branches of cliff-face trees in Tigray''s escarpment. Harvesters climb without protective equipment, guided by smoke and knowledge. The honey is dark, thick, and raw — sealed in beeswax as it has been for 3,000 years. This tradition appears in rock art dated to the Aksumite period. The flavor is complex: wild herbs, mountain flowers, beeswax.',
 'Beekeeping_in_Ethiopia',
 '["Raw honey","Log hive","Traditional","Dark honey","Tigray"]',
 'Contact Mulu Haile (GUGE Creator) directly for orders'),

('Lalibela Forest Honey','lalibela-forest-honey',
 (SELECT id FROM regions WHERE slug='lalibela'),
 'honey',
 'Harvested by traditional beekeepers in the highland forests around Lalibela.',
 'The forests above Lalibela are home to some of Ethiopia''s most skilled traditional beekeepers. Their honey is harvested twice yearly from log hives suspended in highland trees, then carried down mountain paths to market. The flavor reflects the forest: eucalyptus, wildflowers, highland herbs. Thick and amber, it is eaten straight from the comb.',
 'Honey',
 '["Forest honey","Lalibela","Highland","Artisan"]',
 'Contact Lalibela beekeepers cooperative via GUGE marketplace'),

('Axum Beeswax Honey','axum-beeswax-honey',
 (SELECT id FROM regions WHERE slug='aksum'),
 'honey',
 'Wax-sealed dark honey from the Tigray plateau — same method as ancient Aksumite carvings.',
 'Produced by highland beekeepers in the Tigray plateau using traditional log hives — the same method depicted in ancient Aksumite stone carvings. The honey is sealed inside beeswax cells just as the bees leave it. Dark, rich and complex with a slight bitterness from the wild highland flora.',
 'Axum',
 '["Wax-sealed","Ancient tradition","Aksumite","Premium"]',
 'Order from Axum artisan honey producers'),

-- CRAFT
('Harari Woven Baskets','harari-woven-baskets',
 (SELECT id FROM regions WHERE slug='harar'),
 'craft',
 'Woven by Harari women with geometric patterns encoding social and cultural meaning.',
 'Harari basket weaving is not craft — it is language. Each geometric pattern is a message: a family name, a season, a ceremonial occasion. The patterns are woven from split palm leaves and sewn with colored thread in designs that have been passed between women for generations. No two baskets carry exactly the same meaning.',
 'Harar',
 '["Handwoven","Symbolic patterns","Artisan","Harari women"]',
 'Contact Fatuma Abdullahi (GUGE Creator) to commission a basket'),

('Mesob (Ethiopian Woven Table)','mesob-woven-table',
 (SELECT id FROM regions WHERE slug='gondar'),
 'craft',
 'A tall woven basket-table that holds the communal injera platter at every family meal.',
 'The mesob is both furniture and ritual object. A tall woven basket with a wide flat lid, it holds the communal injera at every Ethiopian meal. Everyone eats from the same surface, reaching across to share food — the mesob embodies Ethiopia''s communal food culture. Each one is hand-woven in geometric patterns specific to the maker''s region.',
 'Mesob',
 '["Traditional","Woven","Communal","Functional art"]',
 'Order from Ethiopian craft cooperatives or GUGE marketplace'),

('Konso Waga Ancestor Sculpture','konso-waga-sculpture',
 (SELECT id FROM regions WHERE slug='konso'),
 'craft',
 'UNESCO-listed carved wooden ancestor figures placed over the graves of Konso warriors.',
 'Waga are carved wooden figures placed over the graves of Konso warriors and community leaders after death. Each waga is unique — carved to represent the achievements of the deceased, their rank, enemies defeated, and animals hunted. UNESCO lists the waga tradition as an Intangible Cultural Heritage. The finest waga carvers are among Ethiopia''s most respected artists.',
 'Konso',
 '["UNESCO","Ancestor figure","Wood carving","Ritual"]',
 'Commission through Konso cultural association — not mass produced'),

('Ethiopian Silver Cross Jewelry','ethiopian-silver-cross',
 (SELECT id FROM regions WHERE slug='lalibela'),
 'craft',
 'Hand-cast silver crosses in hundreds of regional styles — each carrying its region''s theology.',
 'The Ethiopian Orthodox cross exists in hundreds of regional variations — Lalibela, Aksum, Gondar, Tigray. Each design carries the distinctive theological emphasis of its origin church. Hand-cast by silversmiths who learned from their fathers, each cross is slightly different. A Lalibela cross bought at the church gate was blessed by the priest who sold it.',
 'Ethiopian_cross',
 '["Silver","Orthodox","Heritage","Handcast","Lalibela"]',
 'Purchase directly from Lalibela and Aksum church silversmiths'),

('Omo Valley Beaded Jewelry','omo-valley-beaded-jewelry',
 (SELECT id FROM regions WHERE slug='omo-valley'),
 'craft',
 'Intricate Hamar and Mursi beaded necklaces encoding status, age and tribal identity.',
 'In the Omo Valley, jewelry is biography. Hamar women wear copper coils on their neck to indicate marriage. Mursi women stretch their lower lips with clay discs. Beaded necklaces map social standing, age group, ceremonial participation and family lineage. Each piece is made by the wearer or their closest kin. No two are identical.',
 'Omo_Valley',
 '["Beaded","Hamar","Mursi","Identity","Body art"]',
 'Contact Yonas Tesfaye (GUGE Creator) for ethical sourcing information'),

('Sidama Clay Pottery','sidama-clay-pottery',
 (SELECT id FROM regions WHERE slug='yirgacheffe'),
 'craft',
 'Hand-built clay pots by Sidama women for fermenting tella, serving food and carrying water.',
 'Sidama pottery is built without a wheel — hand-coiled and shaped by women who learned the technique from their mothers. Each pot shape has a specific purpose: the wide-mouthed gela for fermenting tella beer, the narrow jebena for coffee, the heavy water jar for storage. Decorated with geometric incisions before firing, they are functional and beautiful.',
 'Sidama_people',
 '["Handmade","Clay","Sidama women","Functional"]',
 'Order from Sidama craft cooperatives via GUGE marketplace'),

('Beeswax Timkat Candles','beeswax-timkat-candles',
 (SELECT id FROM regions WHERE slug='gondar'),
 'craft',
 'Hand-rolled beeswax candles carried by pilgrims through the night at Timkat.',
 'At Timkat, Ethiopia''s most spectacular festival, thousands of pilgrims carry hand-rolled beeswax candles through the night as the Ark of the Covenant tabots are processed through the streets. The candles burn for hours. They are made by church communities using pure beeswax, rolled by hand and blessed before the ceremony. The smell of beeswax and incense defines Timkat.',
 'Timkat',
 '["Ceremonial","Beeswax","Orthodox","Timkat","Natural"]',
 'Available from church communities in Gondar and Lalibela'),

-- CLOTHING
('Habesha Kemis (Traditional Dress)','habesha-kemis',
 (SELECT id FROM regions WHERE slug='gondar'),
 'clothing',
 'White cotton dress with embroidered Tibeb border — worn for weddings and church.',
 'The habesha kemis is Ethiopia''s national dress for women: white cotton woven on hand looms, with a colored embroidered border called tibeb along the hem and cuffs. No two tibeb patterns are identical — each family''s weaver has their own designs. Worn for Ethiopian Christmas, Easter, weddings and Sunday church, it is the most recognizable garment in the country.',
 'Habesha_kemis',
 '["Traditional dress","Embroidered","Cotton","Tibeb","Wedding"]',
 'Order from Addis Ababa traditional dress weavers or diaspora suppliers'),

('Dorze Hand-Woven Shemma Cotton','dorze-hand-woven-shemma',
 (SELECT id FROM regions WHERE slug='wolayta'),
 'clothing',
 'Ethiopia''s finest hand-woven cotton from the Dorze master weavers of the Gamo highlands.',
 'The Dorze people of the Gamo highlands above Arba Minch are Ethiopia''s master weavers. Their shemma — a white cotton cloth with colored stripes — is woven on pit looms, a technology unchanged in 500 years. The weaver sits in a pit below ground level, working the treadles with their feet while their hands throw the shuttle. The result is the finest hand-woven cotton in Ethiopia.',
 'Dorze_people',
 '["Master weavers","Cotton","Gamo highlands","Pit loom","Shemma"]',
 'Contact Bekele Worku (GUGE Creator) directly for orders'),

('Ethiopian Gabi (Heavy Linen Wrap)','ethiopian-gabi',
 (SELECT id FROM regions WHERE slug='gondar'),
 'clothing',
 'Heavy white linen wrap worn by highland Ethiopians through cool mornings and cold nights.',
 'The gabi is the great equalizer of Ethiopian highland clothing. A thick double-woven cotton wrap with a colored border, it is worn by everyone — farmers, priests, city workers — draped over shoulders against the morning cold. In Gondar and the Amhara highlands, the gabi is also a prayer shawl, a baby carrier, and a shelter from rain. Its weight and weave tell you where it came from.',
 'Shamma_(clothing)',
 '["Linen","Handwoven","Highlands","Amhara","Gabi"]',
 'Available from Amhara highland weavers and diaspora textile suppliers'),

('Silte Woven Fabric','silte-woven-fabric',
 (SELECT id FROM regions WHERE slug='silte'),
 'clothing',
 'Bold geometric patterns on hand-loom cotton from the Silte Zone weavers.',
 'Silte weaving is done on traditional pit looms by women who have learned the patterns from their mothers. The fabrics feature bold geometric designs in cotton thread — reds, greens, yellows on white — worn for the Silte new year and weddings. The Silte Zone has thousands of weaving families, each with their own pattern vocabulary.',
 'Silte_Zone',
 '["Hand-loom","Geometric","Patterned","Silte","Wedding"]',
 'Contact Silte Zone Women Weavers Cooperative');

-- ═══════════════════════════════════════════════════════════════
--  SEED: STORIES (12 stories)
-- ═══════════════════════════════════════════════════════════════
INSERT INTO stories (title, slug, region_id, creator_id, type, excerpt, body, wiki_article, read_minutes) VALUES

('Inside the rock-hewn churches of Lalibela',
 'inside-rock-hewn-churches-lalibela',
 (SELECT id FROM regions WHERE slug='lalibela'),
 (SELECT id FROM creators WHERE slug='tigist-alemu'),
 'travel',
 'Carved from single blocks of red volcanic rock in the 12th century, Lalibela''s churches are not ruins — they are alive, filled with priests, pilgrims, and incense rising through ancient corridors.',
 'The priest is barefoot. He has removed his shoes before the threshold, as everyone must here, and his white robe sweeps the floor of the corridor as he leads us deeper into the rock. The walls are close on both sides — barely a shoulder''s width apart in places — and the ceiling drips with the cold breath of the mountain above us.

Lalibela''s churches were not built. They were subtracted from the world. King Lalibela ordered his craftsmen to carve eleven churches directly from the volcanic rock of the Ethiopian highlands in the 12th century, and what they created — working downward, inward, removing stone to reveal space — has never been replicated anywhere on Earth.

The Church of Bete Giyorgis sits at the bottom of a 12-metre pit, carved in the shape of a perfect Greek cross. When you stand at the pit''s edge and look down, it seems to glow — the red stone catching the light differently from every angle. Ethiopian pilgrims come here from across the country for Timkat and Genna, sleeping in the rock corridors with their white shamas wrapped around them.

These are not monuments. They are churches. Services are held every day.',
 'Church_of_Saint_George,_Lalibela', 8),

('The farm behind the cup: tracing Yirgacheffe coffee to its source',
 'farm-behind-cup-yirgacheffe',
 (SELECT id FROM regions WHERE slug='yirgacheffe'),
 (SELECT id FROM creators WHERE slug='dawit-abebe'),
 'product-origin',
 'Follow a single coffee bean from a smallholder farm at 1,800m, through the washing station, onto raised drying beds, and into the hands of a cooperative that exports to the world.',
 'Kedir Ahmed wakes at 4am to check his coffee trees. He farms two hectares on a hillside above the Yirgacheffe kebele, at 1,840 metres above sea level, in soil that has been producing coffee for 400 years. He knows each of his trees individually — which ones produce the heaviest cherries, which ones the most fragrant flowers.

The cherry harvest runs from October to January. Each cherry is hand-picked — only the red ones, never the green. Kedir''s family picks together, and at the end of each day the harvest is carried to the washing station in large plastic bags on the backs of donkeys.

At the Kochere washing station, the cherries are sorted, pulped, fermented in clean water for 36 hours, then washed and laid on raised mesh beds to dry in the highland sun. This is the natural process — the one that makes Yirgacheffe coffee taste like blueberries and jasmine.

Kedir receives 10 birr per kilo of cherry. The coffee he produces will sell for $25 per pound in Tokyo.',
 'Coffee_production_in_Ethiopia', 5),

('The women who weave Harar',
 'women-who-weave-harar',
 (SELECT id FROM creators WHERE slug='amina-suleiman'),
 (SELECT id FROM creators WHERE slug='amina-suleiman'),
 'culture',
 'Harari basket weaving is not craft — it is language. Each geometric pattern encodes a message. The women who weave them are also the women who remember.',
 'Fatuma Abdullahi learned to weave when she was seven years old. Her grandmother sat with her on the floor of their house in the old city and guided her fingers through the first pattern — a diamond grid that her family has used for three generations. She is 42 now, and she teaches the same pattern to the schoolgirls who come to her house on Saturday mornings.

Harari baskets are woven from split palm leaves, stitched with colored thread in geometric patterns that carry meaning specific to the weaver''s family and social context. A basket made for a wedding has different patterns than one made for daily use. A basket given as a gift carries patterns that communicate the relationship between giver and receiver.

The weaving tradition is registered with UNESCO as part of the Harari intangible cultural heritage. But the real preservation happens here, in this room, with Fatuma''s hands guiding younger hands through a pattern that is older than any registry.',
 'Harar', 6),

('Nech Teff: the white grain of Gojjam',
 'nech-teff-white-grain-gojjam',
 (SELECT id FROM regions WHERE slug='gojjam'),
 NULL,
 'product-origin',
 'In the highlands around Debre Markos, farmers have grown white teff for generations. The finest variety — nech teff — commands a premium across Ethiopia and is the secret of the best injera.',
 'Teff is the smallest grain in the world. A single seed of Eragrostis tef is smaller than a poppy seed, and a teaspoon holds more than 150 of them. But this tiny grain feeds all of Ethiopia — it is the foundation of injera, the sour flatbread that is both plate and food at every Ethiopian meal.

In Gojjam, the highland plateau west of Lake Tana, farmers grow the rarest and most prized variety: nech teff, white teff. It is lighter in color than the red variety, more aromatic, and produces an injera with a finer texture and a more complex sour flavor. The finest injera in Addis Ababa is made from Gojjam nech teff.

The grain is sown in July and harvested in September. Farmers thresh it by walking cattle in circles over the dry stalks on stone threshing floors. The grain is then winnowed in the highland wind. A good farmer knows by the sound of the grain falling whether it is clean.',
 'Eragrostis_tef', 4),

('Timkat: Ethiopia''s most spectacular festival',
 'timkat-ethiopia-most-spectacular-festival',
 (SELECT id FROM regions WHERE slug='gondar'),
 (SELECT id FROM creators WHERE slug='tigist-alemu'),
 'festival',
 'Every January, Ethiopia re-enacts the baptism of Jesus. In Gondar, thousands of white-robed pilgrims descend to Fasilides Bath. The Ark is processed through the city at midnight.',
 'At midnight on the eve of Timkat, the tabots — representations of the Ark of the Covenant — are brought out of every church in Ethiopia, wrapped in brocaded cloth and carried on the heads of priests. The procession moves through the streets to the sound of drums, sistrum rattles and the chanting of priests who have been awake since sunset.

In Gondar, the tabots are carried to Fasilides Bath — a rectangular pool built by Emperor Fasilides in the 17th century, flanked by stone towers and filled especially for Timkat. By dawn, the pool is surrounded by tens of thousands of white-robed pilgrims. The deacon blesses the water. Then everyone jumps in.

Timkat is officially a religious festival — the Ethiopian Orthodox celebration of the baptism of Jesus. But it is also a social event, a reunion, a moment of national identity. People travel from across the country to celebrate it in Gondar, where the combination of the royal bath, the castles, and the huge crowd creates something that exists nowhere else on Earth.',
 'Timkat', 7),

('Life on the Omo: meeting the Hamar people',
 'life-on-omo-meeting-hamar',
 (SELECT id FROM regions WHERE slug='omo-valley'),
 (SELECT id FROM creators WHERE slug='yonas-tesfaye'),
 'culture',
 'The Hamar have lived along the Omo River for centuries. Their bull-jumping ceremony marks the transition to manhood. Their land and way of life face increasing pressure.',
 'The bull jumping ceremony begins at sunrise. The young man who is to jump has been prepared for weeks — his body rubbed with ochre and fat, his hair braided. Before he can jump, the women of his family must endure ceremonial whipping from the maz — young men who have already completed their jumps. The women bear the scars proudly. They are proof of the family''s commitment.

Then the cattle are lined up — eight to ten bulls, standing flank to flank. The young man runs at them, springs onto the back of the first, and runs across them all, back and forth, four times without falling. If he succeeds, he is a man. If he falls, he must wait another year.

The Hamar have practiced this ceremony for centuries. They live by cattle, and cattle define everything — wealth, marriage, identity, ceremony. A Hamar man without cattle is a man without standing. But the Gibe III dam, 500km upstream, has changed the flooding patterns of the Omo River that irrigate the Hamar''s grazing lands.',
 'Omo_Valley', 9),

('Danakil: walking on another planet',
 'danakil-walking-another-planet',
 (SELECT id FROM regions WHERE slug='danakil-depression'),
 NULL,
 'travel',
 'The Danakil Depression is the hottest inhabited place on Earth. Erta Ale''s lava lake has burned for over a century. The Afar people mine salt there daily as they have for millennia.',
 'The sulfur springs of Dallol are yellow, green and orange — colors that seem impossible in nature, produced by the chemistry of superheated brine forced up through volcanic rock. The temperature at midday is 45 degrees. The air smells of sulfur dioxide and salt. Nothing alive is visible, except us.

The Danakil Depression sits 125 metres below sea level at its lowest point, making it one of the lowest places on Earth. It is also one of the hottest — the average daily temperature across the year is 34 degrees Celsius. The Afar people have lived here for millennia, subsisting on salt mining, camel herding and trade with the highland people.

Erta Ale is a shield volcano that has maintained an active lava lake in its summit crater for over 100 years. To reach it requires a night march across lava fields, guided by Afar men who carry Kalashnikovs for protection against territorial disputes. At the crater rim, the heat is enormous and the smell of sulfur overwhelming. The lava churns and splits below you, red and orange against the black rock, 50 metres down.',
 'Danakil_Depression', 10),

('Dorze weavers: the finest cotton in Ethiopia',
 'dorze-weavers-finest-cotton-ethiopia',
 (SELECT id FROM regions WHERE slug='wolayta'),
 (SELECT id FROM creators WHERE slug='bekele-worku'),
 'craft',
 'In the Gamo highlands above Arba Minch, the Dorze people have woven cotton shemma fabric for centuries. Their pit looms are a technology unchanged in 500 years.',
 'Bekele Worku''s loom is in a pit below his house. He sits in the pit — waist-deep in the earth — with his feet on the treadles and his hands on the shuttle. The ceiling above him is the underside of his wooden floor. The weaving takes place in a narrow space that his grandfather built and his father improved, and which he will pass to his son.

The Dorze are the master weavers of Ethiopia. Their shemma — a white cotton cloth with colored stripes — is the fabric from which the habesha kemis and the netela shawl are made. The finest shemma is woven with 120 threads per centimeter. You can see light through it when you hold it up.

The weaving tradition among the Dorze is over 500 years old. It requires seven years of apprenticeship before a young man is considered competent. Bekele learned from his father, who learned from his father. The patterns are memorized, not written.',
 'Dorze_people', 5),

('The ancient honey tradition of Tigray',
 'ancient-honey-tradition-tigray',
 (SELECT id FROM regions WHERE slug='aksum'),
 (SELECT id FROM creators WHERE slug='mulu-haile'),
 'product-origin',
 'Log hives are hung in cliff-face trees in Tigray. Harvesters climb without protection. The honey is dark, thick, and sealed in beeswax. The tradition appears in 3,000-year-old rock art.',
 'Mulu Haile climbs the tree without a protective suit. He carries only a clay pot of smoldering dung to smoke the bees, slung over his shoulder on a rope. The log hive is 15 metres above the ground, wedged in the fork of a wild olive tree on a cliff face above the Tigray escarpment.

The bees are aggressive — Apis mellifera jemenitica, the highland Ethiopian bee, which is smaller and fiercer than European honeybees. Mulu moves slowly, deliberately, blowing smoke into the hive entrance. The bee activity around him is intense. He does not hurry.

The honey he harvests is dark — almost black in the comb — with a complex bitter-sweet flavor that comes from the wild highland flora: wild thyme, sage, acacia flowers. He seals the comb in beeswax as the bees left it, and carries it down the cliff face in the clay pot.

This practice is depicted in rock art at ancient Aksumite sites dated to 1000 BCE. Mulu is doing exactly what those artists carved into stone.',
 'Beekeeping_in_Ethiopia', 6),

('Konso: the terraced world of the south',
 'konso-terraced-world-south',
 (SELECT id FROM regions WHERE slug='konso'),
 NULL,
 'culture',
 'The Konso built dry-stone terraces on steep hillsides 400 years ago. Their waga ancestor sculptures stand over graves. The entire cultural landscape is UNESCO-listed.',
 'The Konso terrace system is a feat of collective engineering. On steep hillsides that would otherwise be bare rock and eroding soil, the Konso have built hundreds of dry-stone walls, creating flat terraces on gradients of up to 60 degrees. The walls are built without mortar, maintained by the community through a system of collective labor called debo.

The terraces are not just agricultural infrastructure — they are cultural landscape. Between the fields, Konso towns are enclosed by further stone walls, with gates and watchtowers. Inside each town is the morah, a central gathering space where the generation grades of Konso men meet to discuss community affairs.

The waga — carved wooden ancestor figures — are placed over the graves of men who achieved warrior status or killed a dangerous animal in their lifetime. Each waga is carved to represent the specific achievements of the deceased: the number of enemies and animals carved around the base, the weapons held in the figure''s hands.

UNESCO inscribed the Konso Cultural Landscape in 2011. The Konso say it should have been inscribed 400 years ago.',
 'Konso', 7),

('The Ethiopian coffee ceremony: a ritual in three rounds',
 'ethiopian-coffee-ceremony-three-rounds',
 (SELECT id FROM regions WHERE slug='yirgacheffe'),
 (SELECT id FROM creators WHERE slug='dawit-abebe'),
 'culture',
 'Three rounds of coffee — abol, tona, baraka — roasted, ground, and brewed in a jebena clay pot. The ceremony lasts hours. To refuse the third round is to refuse a blessing.',
 'The green coffee beans are washed first. Then they go into the pan — a flat iron pan held over charcoal — and the roasting begins. The person performing the ceremony, almost always a woman, stirs the beans constantly with a long-handled spoon, watching their color move from green to yellow to caramel to the deep brown of readiness.

When they are done, she holds the pan out for guests to wave the smoke toward them — inhaling the first gift of the coffee, its perfume. The beans are then ground in a wooden mortar, and the grounds go into the jebena, a clay pot with a long neck, narrow at the top, filled with water and set over the charcoal.

When the coffee boils, it is poured into small handleless cups — finjans. This is the first round: abol. It is the strongest. The second round, tona, is made from the same grounds with fresh water — lighter, sweeter. The third, baraka — "blessing" — is the lightest of all, and the most important. To refuse it is considered unlucky. The ceremony can take two hours.',
 'Coffee_ceremony_(Ethiopia)', 5),

('The Aksumite obelisks and what they mean',
 'aksumite-obelisks-what-they-mean',
 (SELECT id FROM regions WHERE slug='aksum'),
 (SELECT id FROM creators WHERE slug='tigist-alemu'),
 'history',
 'The monolithic stelae of Aksum were carved from single granite blocks without wheels. The tallest standing is 24 metres. One was stolen by Mussolini and returned in 2008.',
 'The stelae of Aksum are engineering problems with no good answer. A single block of granite, 24 metres tall and weighing 160 tonnes, was quarried from a hillside 4 kilometres from the field where it stands. There were no wheels, no cranes, no draft animals capable of moving this weight. And yet it was moved, raised upright, and planted precisely in the earth — sometime around the 4th century CE.

Archaeologists have various theories about how the Aksumites managed this. None of them are entirely convincing.

The stelae mark the tombs of Aksumite kings. The tallest one ever raised — 33 metres — fell and shattered, probably during its erection or shortly after. Its pieces lie where they fell, arranged by the impact. The stele still standing at 24 metres is the tallest ancient monolith still upright on Earth.

The Rome Stele — 24 metres, 160 tonnes — was taken to Rome by Mussolini''s troops in 1937 as war loot. It stood in front of the UN Food and Agriculture Organization in Rome for 68 years. It was finally returned to Ethiopia in 2008 and re-erected in Aksum, where it belongs.',
 'Obelisk_of_Axum', 8);

-- ═══════════════════════════════════════════════════════════════
--  VERIFY
-- ═══════════════════════════════════════════════════════════════
SELECT 'regions' AS table_name, COUNT(*) AS rows FROM regions
UNION ALL
SELECT 'products', COUNT(*) FROM products
UNION ALL
SELECT 'stories',  COUNT(*) FROM stories
UNION ALL
SELECT 'creators', COUNT(*) FROM creators;
