-- SQL script to set up the database for the tracking website project

-- Create the `orders` table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tracking_number VARCHAR(255) NOT NULL UNIQUE,
    postal_tracking_number VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending', 'In-Progress', 'Pending Dispatch', 'Dispatched') NOT NULL DEFAULT 'Pending',
    customer_name VARCHAR(255) NOT NULL, -- Added customer_name column
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    file_name VARCHAR(255) DEFAULT NULL, -- Column added to store the name of the uploaded file
    email VARCHAR(255) DEFAULT NULL -- Column added to store the optional email address
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create the `admin_users` table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert an admin user (replace 'admin' and 'password' with your desired credentials)
-- IMPORTANT: Use a password hash in production instead of plain text
INSERT INTO admin_users (username, password) VALUES ('admin', MD5('password'));

-- Note: Adjust the VARCHAR lengths according to your needs.
-- The `postal_tracking_number` can be NULL until a postal tracking number is assigned.
-- The `status` column uses an ENUM type for predefined status values.
-- `created_at` and `updated_at` timestamps are included for record management.
-- The `customer_name` column is added to store the customer's name with each order.
-- The `file_name` column is added to store the name of the uploaded .zip file.
-- The `email` column is added to store the optional email address.

