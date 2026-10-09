-- need to do if laragon or xampp doesnt have the database yet, then create it

CREATE DATABASE IF NOT EXISTS portfolio_hub
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE portfolio_hub;

CREATE TABLE IF NOT EXISTS portfolio_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    creator_name VARCHAR(120) NOT NULL,
    category VARCHAR(80) NOT NULL,
    image_filename VARCHAR(255) NOT NULL,
    likes_count INT UNSIGNED NOT NULL DEFAULT 0,
    views_count INT UNSIGNED NOT NULL DEFAULT 0,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    display_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_portfolio_order (display_order, id),
    INDEX idx_portfolio_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO portfolio_items
    (title, creator_name, category, image_filename, likes_count, views_count, is_featured, display_order)
SELECT
    'Sculpting silence', 'Maya Chen', 'Photography', 'architecture-blue.svg', 2800, 18400, 1, 1
WHERE NOT EXISTS (SELECT 1 FROM portfolio_items WHERE title = 'Sculpting silence');

INSERT INTO portfolio_items
    (title, creator_name, category, image_filename, likes_count, views_count, is_featured, display_order)
SELECT
    'Form / Function', 'Jon Bell', 'UI/UX', 'geometry-light.svg', 1900, 12100, 0, 2
WHERE NOT EXISTS (SELECT 1 FROM portfolio_items WHERE title = 'Form / Function');

INSERT INTO portfolio_items
    (title, creator_name, category, image_filename, likes_count, views_count, is_featured, display_order)
SELECT
    'Noma identity', 'Amira Wells', 'Branding', 'geometry-dark.svg', 3200, 21600, 0, 3
WHERE NOT EXISTS (SELECT 1 FROM portfolio_items WHERE title = 'Noma identity');

INSERT INTO portfolio_items
    (title, creator_name, category, image_filename, likes_count, views_count, is_featured, display_order)
SELECT
    'Rhythm in white', 'Theo Park', 'Architecture', 'lines.svg', 1400, 9800, 0, 4
WHERE NOT EXISTS (SELECT 1 FROM portfolio_items WHERE title = 'Rhythm in white');

INSERT INTO portfolio_items
    (title, creator_name, category, image_filename, likes_count, views_count, is_featured, display_order)
SELECT
    'Tessellated futures', 'Priya Shah', '3D & Motion', 'tessellation.svg', 2100, 14200, 0, 5
WHERE NOT EXISTS (SELECT 1 FROM portfolio_items WHERE title = 'Tessellated futures');

INSERT INTO portfolio_items
    (title, creator_name, category, image_filename, likes_count, views_count, is_featured, display_order)
SELECT
    'Concrete language', 'Noah Martin', 'Photography', 'architecture-cyan.svg', 980, 7300, 0, 6
WHERE NOT EXISTS (SELECT 1 FROM portfolio_items WHERE title = 'Concrete language');
