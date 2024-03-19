# Order Tracker

The Order Tracker website is designed to manage and track customer orders for a printing service. It allows for the creation of new orders, updating order statuses, and tracking orders through a unique tracking number. Customers can enter their tracking number to view the current status of their order, including any postal tracking information once dispatched. Administrators can manage orders, update statuses, and handle file uploads associated with each order.

## Features

- **Order Creation:** Generate new orders with unique tracking numbers and upload related files.
- **Order Management:** View, update status, and delete orders from an admin interface.
- **Order Tracking:** Customers can track their order status using a unique tracking number.
- **File Handling:** Supports uploading and downloading of files related to orders.
- **Security:** Admin authentication to manage orders.

This website streamlines the process of managing and tracking orders for both customers and administrators.

## Database Setup

To set up the database for the Order Tracker website, you need to import the following SQL script into your SQL server. This script creates the necessary tables and inserts initial data for the application to function correctly.

### Tables Created:

- **`orders`**: Stores information about each order, including tracking numbers, status, customer names, and file names associated with the orders.
- **`admin_users`**: Contains admin user credentials for accessing the admin interface.

### Initial Data:

- An initial admin user is inserted into the `admin_users` table. You should replace the default credentials with your own and use a password hash in production for security.

### SQL Script:

The SQL script `database_setup.sql` should be imported into your SQL server. This script includes commands to create the tables and insert the initial data mentioned above.

Ensure you have the correct permissions to create tables and insert data into your database before running the script.

This setup is crucial for the proper functioning of the Order Tracker website, enabling features such as order creation, management, tracking, and file handling.

### Default Login Details

The default login details would be `admin` and `password`.
Please change these for security reasons.

### Coming Soon

- Upload Multiple Files (DONE)
- Ability to upload more files in the orders page
- SMTP email notifications on status changes 
- Settings page to edit website name on pages and email headers
- Ability to change admin password on the settings page
- Support ticket system for customers


