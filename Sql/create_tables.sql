-- Supprimer si existe déjà
DROP TABLE IF EXISTS order_items, orders, reviews, beauty_profiles, products, categories, users;

-- Table users
CREATE TABLE users (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('client','admin') DEFAULT 'client',
    telephone VARCHAR(20),
    avatar_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table categories
CREATE TABLE categories (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table products
CREATE TABLE products (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    stock INT UNSIGNED DEFAULT 0,
    categorie_id INT UNSIGNED,
    marque VARCHAR(100) NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    note_moyenne DECIMAL(3,2) DEFAULT 0,
    nb_avis INT UNSIGNED DEFAULT 0,
    skin_types JSON NOT NULL,
    concerns JSON NOT NULL,
    is_vegan BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id)
);

-- Table beauty_profiles
CREATE TABLE beauty_profiles (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNSIGNED UNIQUE,
    skin_type ENUM('seche','grasse','mixte','normale','sensible') NOT NULL,
    skin_tone ENUM('claire','medium','mate','foncee') NOT NULL,
    concerns JSON,
    preferences JSON,
    budget_range ENUM('low','medium','high') DEFAULT 'medium',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table orders
CREATE TABLE orders (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNSIGNED,
    statut ENUM('en_attente','confirmee','expediee','livree','annulee') DEFAULT 'en_attente',
    total_ttc DECIMAL(10,2) NOT NULL,
    adresse_livraison JSON NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table order_items
CREATE TABLE order_items (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id INT UNSIGNED,
    product_id INT UNSIGNED,
    quantite INT UNSIGNED NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE favoris (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at DATETIME DEFAULT NOW(),
  UNIQUE KEY unique_favori (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table reviews
CREATE TABLE reviews (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNSIGNED,
    product_id INT UNSIGNED,
    note TINYINT UNSIGNED CHECK (note BETWEEN 1 AND 5),
    titre VARCHAR(255) NOT NULL,
    commentaire TEXT NOT NULL,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
DELIMITER //
CREATE PROCEDURE UpdateProductRating(IN p_product_id INT)
BEGIN
    DECLARE avg_note DECIMAL(3,2);
    DECLARE total_reviews INT;
    SELECT AVG(note), COUNT(*) INTO avg_note, total_reviews
    FROM reviews
    WHERE product_id = p_product_id AND is_approved = TRUE;
    UPDATE products
    SET note_moyenne = COALESCE(avg_note, 0),
        nb_avis = total_reviews
    WHERE id = p_product_id;
END //
DELIMITER ;
