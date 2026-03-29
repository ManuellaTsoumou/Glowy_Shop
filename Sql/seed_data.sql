-- 3 catégories
INSERT INTO categories (nom) VALUES 
('Soin visage'), ('Maquillage'), ('Cheveux');

-- 10 produits (avec skin_types et concerns en JSON)
INSERT INTO products (nom, marque, prix, stock, categorie_id, image_url, skin_types, concerns, is_vegan) VALUES
('Serum Anti-Acne', 'GlowLab', 29.90, 15, 1, 'serum.jpg', '["mixte","grasse"]', '["acne","pores"]', TRUE),
('Creme Hydratante', 'PureSkin', 22.50, 25, 1, 'creme.jpg', '["seche","normale"]', '["hydratation"]', TRUE),
('Fond de Teint Mat', 'MakeUpPro', 39.90, 8, 2, 'fond-teint.jpg', '["mixte","grasse"]', '["pores"]', FALSE),
('Huile Cheveux Repair', 'HairGlow', 28.00, 12, 3, 'huile.jpg', '["tous"]', '["secheresse"]', TRUE),
('Masque Purifiant', 'CleanFace', 18.90, 30, 1, 'masque.jpg', '["grasse","mixte"]', '["pores","acne"]', TRUE);

-- 2 utilisateurs de test
INSERT INTO users (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'Lucie', 'lucie@glowshop.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'), -- password: password
('Test', 'Emma', 'emma@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client');
