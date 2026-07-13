<?php

$pdo = new PDO(
    'pgsql:host=ep-billowing-glitter-apal87to.c-7.us-east-1.aws.neon.tech;port=5432;dbname=GuGe_test;sslmode=require',
    'neondb_owner',
    'npg_KvyAOICsPQ49'
);

$tables = [];
$res = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename");
foreach ($res as $r) {
    $tables[] = $r[0];
}
echo 'Tables: '.implode(', ', $tables)."\n\n";

// Get all columns for key tables
foreach (['users', 'regions', 'products', 'creators', 'stories'] as $t) {
    $cols = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='$t' ORDER BY ordinal_position")->fetchAll(PDO::FETCH_COLUMN);
    echo "$t: ".implode(', ', $cols)."\n";
}

echo "\n--- Applying missing columns ---\n";

// regions: add status, featured
$regionCols = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='regions'")->fetchAll(PDO::FETCH_COLUMN);
if (! in_array('status', $regionCols)) {
    $pdo->exec("ALTER TABLE regions ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'published'");
    echo "regions.status: added\n";
} else {
    echo "regions.status: exists\n";
}

if (! in_array('featured', $regionCols)) {
    $pdo->exec('ALTER TABLE regions ADD COLUMN featured BOOLEAN NOT NULL DEFAULT false');
    echo "regions.featured: added\n";
} else {
    echo "regions.featured: exists\n";
}

// products: add status, featured, hidden
$prodCols = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='products'")->fetchAll(PDO::FETCH_COLUMN);
if (! in_array('status', $prodCols)) {
    $pdo->exec("ALTER TABLE products ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'published'");
    echo "products.status: added\n";
} else {
    echo "products.status: exists\n";
}

if (! in_array('featured', $prodCols)) {
    $pdo->exec('ALTER TABLE products ADD COLUMN featured BOOLEAN NOT NULL DEFAULT false');
    echo "products.featured: added\n";
} else {
    echo "products.featured: exists\n";
}

if (! in_array('hidden', $prodCols)) {
    $pdo->exec('ALTER TABLE products ADD COLUMN hidden BOOLEAN NOT NULL DEFAULT false');
    echo "products.hidden: added\n";
} else {
    echo "products.hidden: exists\n";
}

// users: add status
$userCols = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='users'")->fetchAll(PDO::FETCH_COLUMN);
if (! in_array('status', $userCols)) {
    $pdo->exec("ALTER TABLE users ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'active'");
    echo "users.status: added\n";
} else {
    echo "users.status: exists\n";
}

// stories: check all needed columns
$storyCols = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='stories'")->fetchAll(PDO::FETCH_COLUMN);
$neededStory = ['category', 'content', 'featured_image', 'gallery', 'status', 'featured', 'language', 'seo_title', 'seo_description', 'view_count'];
foreach ($neededStory as $col) {
    if (! in_array($col, $storyCols)) {
        $type = match ($col) {
            'category', 'language', 'status', 'featured_image', 'seo_title', 'seo_description' => 'VARCHAR(255)',
            'content', 'gallery' => 'TEXT',
            'featured' => 'BOOLEAN NOT NULL DEFAULT false',
            'view_count' => 'INTEGER NOT NULL DEFAULT 0',
            default => 'VARCHAR(255)'
        };
        $pdo->exec("ALTER TABLE stories ADD COLUMN $col $type");
        echo "stories.$col: added\n";
    } else {
        echo "stories.$col: exists\n";
    }
}

echo "\nDone.\n";
