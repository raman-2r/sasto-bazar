-- SastoBazar Database Schema
CREATE DATABASE IF NOT EXISTS myfinalproject;
USE myfinalproject;

DROP TABLE IF EXISTS paymenttable;
DROP TABLE IF EXISTS ordertable;
DROP TABLE IF EXISTS productdetails;
DROP TABLE IF EXISTS userdetails;

-- Table: userdetails
CREATE TABLE userdetails (
  user_id INT(11) PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100),
  email VARCHAR(50) UNIQUE,
  phone VARCHAR(15),
  password VARCHAR(100),
  token VARCHAR(500),
  address VARCHAR(30),
  gender TEXT,
  role VARCHAR(10),
  registration_date VARCHAR(20),
  image VARCHAR(500)
);

-- Table: productdetails
CREATE TABLE productdetails (
  Product_id INT(11) PRIMARY KEY AUTO_INCREMENT,
  user_id INT(11),
  product_name VARCHAR(100),
  category_name VARCHAR(100),
  product_price VARCHAR(500),
  product_image VARCHAR(300),
  product_age VARCHAR(10),
  product_bio VARCHAR(500),
  sell_status VARCHAR(100),
  display_home INT(1),
  FOREIGN KEY (user_id) REFERENCES userdetails(user_id) ON DELETE CASCADE
);

-- Table: ordertable
CREATE TABLE ordertable (
  order_id INT(11) PRIMARY KEY AUTO_INCREMENT,
  Product_id INT(11),
  order_date VARCHAR(100),
  payment_status VARCHAR(11),
  user_id INT(11),
  customer_id INT(11),
  FOREIGN KEY (Product_id) REFERENCES productdetails(Product_id) ON DELETE CASCADE
);

-- Table: paymenttable
CREATE TABLE paymenttable (
  payment_id INT(11) PRIMARY KEY AUTO_INCREMENT,
  order_id INT(11),
  payment_status VARCHAR(100),
  product_id INT(11),
  FOREIGN KEY (order_id) REFERENCES ordertable(order_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES productdetails(Product_id) ON DELETE CASCADE
);
