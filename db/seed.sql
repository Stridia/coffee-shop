-- Seed data for cafe
INSERT INTO cafe (id, name, address, contact_number) VALUES
(1, 'Fulvian Cafe', 'Jl. Keputih Tegal Timur I', '081234567890');

-- Seed data for roles
INSERT INTO roles (id, name) VALUES
(1, 'owner'),
(2, 'staff'),
(3, 'customer');

-- Seed data for types
INSERT INTO types (id, name) VALUES
(1, 'coffee'),
(2, 'non-coffee');

-- Seed data for products
INSERT INTO products (id, cafe_id, type_id, name, description, price, image) VALUES
(1, 1, 1, 'Espresso', 'Rich and concentrated classic espresso', 20000,
'https://plus.unsplash.com/premium_photo-1674327105076-36c4419864cf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D'),
(2, 1, 1, 'Americano', 'Espresso diluted with hot water', 22000,
'https://images.unsplash.com/photo-1551030173-122aabc4489c?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D'),
(3, 1, 1, 'Cappucino', 'Espresso with steamed milk and thick foam', 30000,
'https://images.unsplash.com/photo-1777013486360-699af7c80ff3?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D'),
(4, 1, 1, 'Mocha', 'Espresso blended with chocolate and steamed milk', 35000,
'https://plus.unsplash.com/premium_photo-1668970851336-6c81cc888ba7?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D'),
(5, 1, 2, 'Lemon Tea', 'Refreshing iced tea with fresh lemon slices', 25000,
'https://images.unsplash.com/photo-1470752354724-60a1d2b1907f?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D'),
(6, 1, 2, 'Thai Tea', 'Sweet and creamy traditional Thai iced tea', 28000,
'https://images.unsplash.com/photo-1781663904565-cfdf6a21a9d9?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D');