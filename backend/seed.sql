USE myfinalproject;

-- 1. Insert 2 users: admin and regular user
INSERT INTO userdetails (user_id, name, email, phone, password, address, gender, role, registration_date) 
VALUES 
(1, 'Admin User', 'ramanyogesh@gmail.com', '1234567890', '$2y$10$iHgETs8eTx7dFtwMM5lxuuE69OHhakPh8XATbHxDvteJBrzJE9DEy', 'Kathmandu', 'Male', 'admin', '2023-10-01'),
(2, 'Test User', 'user@sastobazar.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pokhara', 'Female', 'user', '2023-10-02');

-- 2. Insert dummy products
INSERT INTO productdetails (user_id, product_name, category_name, product_price, product_image, product_age, product_bio, sell_status, display_home)
VALUES 
(2, 'Gaming Laptop', 'Electronics', '85000', 'laptop.jpg', '2 years', 'Good condition HP gaming laptop with RTX 3050.', 'available', 1),
(2, 'Smartphone', 'Electronics', '25000', 'phone.jpg', '1 year', 'Samsung Galaxy, slight scratch on the back.', 'available', 1),
(2, 'Comfortable Sofa', 'Furniture', '15000', 'sofa.jpg', '3 years', '3-seater sofa, very comfortable and clean.', 'available', 1),
(2, 'Mountain Bicycle', 'Vehicles', '12000', 'bicycle.jpg', '6 months', 'Hardly used mountain bike, 21 gears.', 'available', 1),
(2, 'Engineering Textbooks', 'Books', '1500', 'textbook.jpg', '4 years', 'Complete set of mechanical engineering textbooks.', 'available', 1),
(2, 'Winter Jacket', 'Clothing', '2000', 'jacket.jpg', '1.5 years', 'Warm North Face jacket, size L.', 'available', 1),
(2, 'DSLR Camera', 'Electronics', '40000', 'camera.jpg', '3 years', 'Canon DSLR with 18-55mm lens. Great for beginners.', 'available', 1),
(2, 'Smart TV 43 inch', 'Electronics', '30000', 'tv.jpg', '2 years', 'LG 43 inch Smart TV, works perfectly.', 'available', 1),
(2, 'Acoustic Guitar', 'Others', '5000', 'guitar.jpg', '2 years', 'Yamaha acoustic guitar, fresh strings attached.', 'available', 1),
(2, 'Mini Refrigerator', 'Electronics', '10000', 'refrigerator.jpg', '4 years', 'Perfect for small apartments or dorms.', 'available', 1);

