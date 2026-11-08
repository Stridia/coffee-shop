-- Seed data for cafe
INSERT INTO cafe (name, address, contact_number) VALUES
('Fulvian Cafe', 'Jl. Keputih Tegal Timur I', '081234567890');

-- Seed data for roles
INSERT INTO roles (id, name) VALUES
('owner'), ('staff'), ('customer');

-- Seed data for types
INSERT INTO types (name) VALUES
('coffee'), ('non-coffee');

-- Seed data for products
INSERT INTO products (cafe_id, type_id, name, description, price, image) VALUES
(1, 1, 'Espresso', 'Rich and concentrated classic espresso', 20000,
'https://plus.unsplash.com/premium_photo-1674327105076-36c4419864cf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D'),
(1, 1, 'Americano', 'Espresso diluted with hot water', 22000,
'https://images.unsplash.com/photo-1551030173-122aabc4489c?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D'),
(1, 1, 'Cappucino', 'Espresso with steamed milk and thick foam', 30000,
'https://images.unsplash.com/photo-1777013486360-699af7c80ff3?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D'),
(1, 1, 'Mocha', 'Espresso blended with chocolate and steamed milk', 35000,
'https://plus.unsplash.com/premium_photo-1668970851336-6c81cc888ba7?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D'),
(1, 2, 'Lemon Tea', 'Refreshing iced tea with fresh lemon slices', 25000,
'https://images.unsplash.com/photo-1470752354724-60a1d2b1907f?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D'),
(1, 2, 'Thai Tea', 'Sweet and creamy traditional Thai iced tea', 28000,
'https://images.unsplash.com/photo-1781663904565-cfdf6a21a9d9?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D');