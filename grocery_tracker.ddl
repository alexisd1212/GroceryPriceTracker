-- Create the 'users' table
CREATE TABLE users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  PRIMARY KEY (id)
);
-- Create the 'user_images' table
CREATE TABLE user_images (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  image_url VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);
-- Create the 'stores' table
CREATE TABLE stores (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL,
  city VARCHAR(255) NOT NULL,
  state VARCHAR(255) NOT NULL,
  zip VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);
-- Create the 'categories' table
CREATE TABLE categories (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);
-- Create the 'grocery_items' table
CREATE TABLE grocery_items (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  brand VARCHAR(255) NOT NULL,
  category_name VARCHAR(255) NOT NULL,
  description TEXT,
  image_url VARCHAR(255),
  weight DECIMAL(10, 2),
  PRIMARY KEY (id),
  FOREIGN KEY (category_name) REFERENCES categories(name)
);
-- Create the 'grocery_item_prices' table
CREATE TABLE grocery_item_prices (
  id INT(11) NOT NULL AUTO_INCREMENT,
  grocery_item_id INT(11) NOT NULL,
  store_id INT(11) NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  price_date DATE NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (grocery_item_id) REFERENCES grocery_items(id),
  FOREIGN KEY (store_id) REFERENCES stores(id)
);
-- Create the 'price_history' table
CREATE TABLE price_history (
  price_history_id INT NOT NULL AUTO_INCREMENT,
  price_id INT NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  history_date DATETIME NOT NULL,
  PRIMARY KEY (price_history_id),
  FOREIGN KEY (price_id) REFERENCES grocery_item_prices(id)
);
-- Create the 'user_reviews' table
CREATE TABLE user_reviews (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  grocery_item_id INT(11) NOT NULL,
  rating INT(11) NOT NULL,
  comment TEXT,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (grocery_item_id) REFERENCES grocery_items(id)
);

CREATE TABLE cart (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  product_id INT(11) NOT NULL,
  quantity INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (product_id) REFERENCES grocery_items(id)
);

-- Insert sample data into the 'users' table
INSERT INTO users (username, email, password, role)
VALUES (
    'john_doe',
    'john@example.com',
    'password',
    'admin'
  ),
  (
    'jane_doe',
    'jane@example.com',
    'password',
    'user'
  );
-- Insert sample data into the 'stores' table
INSERT INTO stores (id, name, address, city, state, zip)
VALUES (
    1,
    'Walmart',
    '123 Main St',
    'Kelowna',
    'BC',
    'V1V0A2'
  ),
  (
    2,
    'Save-On Foods',
    '560 Dalhousie St',
    'Edmonton',
    'AB',
    'T7B 2F3'
  ),
  (
    3,
    'Canadian Supermarket',
    '567 Down St',
    'Toronto',
    'ON',
    'M1H 1A1'
  ),
  (
    4,
    'Costco',
    '234 Kings St',
    'Vancouver',
    'BC',
    'V9I9J2'
  ),
  (
    5,
    'Loblaws',
    '789 Third St',
    'Montreal',
    'QC',
    'H3A 1J2'
  ),
  (
    6,
    'Metro',
    '321 Fourth St',
    'Calgary',
    'AB',
    'T2P 2M5'
  ),
  (
    7,
    'No Frills',
    '654 Fifth St',
    'Halifax',
    'NS',
    'B3H 3C3'
  );
-- Insert sample data into the 'categories' table
INSERT INTO categories (name)
VALUES ('Produce'),
  ('Dairy'),
  ('Meat'),
  ('Bakery');
-- Insert sample data into PriceHistory table
INSERT INTO price_history (price_id, price, history_date)
VALUES (1, 1.29, '2022-03-13 09:00:00'),
  (2, 0.99, '2022-03-13 09:00:00'),
  (3, 2.99, '2022-03-13 09:00:00'),
  (4, 3.49, '2022-03-13 09:00:00'),
  (5, 0.39, '2022-03-13 09:00:00'),
  (6, 6.99, '2022-03-13 09:00:00'),
  (7, 1.99, '2022-03-13 09:00:00'),
  (8, 2.79, '2022-03-13 09:00:00'),
  (9, 0.59, '2022-03-13 09:00:00'),
  (10, 0.79, '2022-03-13 09:00:00'),
  (1, 1.49, '2022-03-12 09:00:00'),
  (2, 1.09, '2022-03-12 09:00:00'),
  (3, 3.29, '2022-03-12 09:00:00'),
  (4, 3.79, '2022-03-12 09:00:00'),
  (5, 0.49, '2022-03-12 09:00:00'),
  (6, 7.99, '2022-03-12 09:00:00'),
  (7, 2.49, '2022-03-12 09:00:00'),
  (8, 2.99, '2022-03-12 09:00:00');
-- Insert sample data into the 'grocery_items' table
INSERT INTO grocery_items (
    id,
    name,
    brand,
    category_name,
    description,
    image_url
  )
VALUES (
    1,
    'Apples',
    'Red Delicious',
    'Produce',
    'Fresh and crisp',
    'img/apple.jpg'
  ),
  (
    2,
    'Milk',
    'Organic Valley',
    'Dairy',
    'Whole milk',
    'img/milk.jpg'
  ),
  (
    3,
    'Chicken Breasts',
    'Maple Leaf',
    'Meat',
    'Boneless, skinless chicken breasts',
    'img/chicken_breast.jpg'
  ),
  (
    4,
    'Ground Beef',
    '80% lean',
    'Meat',
    'Freshly ground beef',
    'img/groundbeef.jpg'
  ),
  (
    5,
    'Onions',
    'Great Value',
    'Produce',
    'Yellow onions',
    'img/onions.jpg'
  ),
  (
    6,
    'Potatoes',
    'Your Fresh Market',
    'Produce',
    'Russet potatoes',
    'img/potatoes.jpg'
  ),
  (
    7,
    'Butter',
    'Dairyland',
    'Dairy',
    'Salted butter',
    'img/butter.jpg'
  );
-- Insert sample data into the 'grocery_item_prices' table
INSERT INTO grocery_item_prices (grocery_item_id, store_id, price, price_date)
VALUES (1, 1, 0.99, '2022-03-13'),
  (1, 2, 1.29, '2022-03-16'),
  (1, 2, 1.10, '2022-02-16'),
  (1, 2, 1.09, '2022-01-16'),
  (2, 1, 3.49, '2022-03-13'),
  (2, 1, 3.09, '2022-02-13'),
  (2, 2, 3.99, '2022-03-16'),
  (2, 2, 3.01, '2022-01-16'),
  (3, 1, 4.99, '2022-03-13'),
  (3, 2, 5.49, '2022-03-18'),
  (3, 3, 4.99, '2022-02-18'),
  (3, 3, 5.29, '2022-01-18'),
  (4, 2, 1.29, '2022-03-13'),
  (4, 2, 1.09, '2022-02-13'),
  (6, 1, 3.49, '2022-03-13'),
  (5, 2, 3.99, '2022-03-13'),
  (4, 3, 4.99, '2022-03-18'),
  (6, 2, 5.49, '2022-03-19');
  
-- Insert sample data into the 'user_reviews' table
INSERT INTO user_reviews (user_id, grocery_item_id, rating, comment)
VALUES (
    2,
    1,
    4,
    'These apples were really fresh and delicious!'
  ),
  (2, 2, 3, 'The milk was good, but a bit pricey.'),
  (
    2,
    4,
    5,
    'This ground beef was excellent quality and a great price!'
  );