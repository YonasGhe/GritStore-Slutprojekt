<?php
require_once "database.php";

$connection = getDatabase();
createTablesIfNotExist($connection);

function createTablesIfNotExist($connection)
{
    $tables = array(
        "products" => "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50),
            price DECIMAL(10, 2),
            image VARCHAR(255) NOT NULL
        )",

        "customers" => "CREATE TABLE IF NOT EXISTS customers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50),
            last_name VARCHAR(50),
            social_number VARCHAR(20),
            telephone VARCHAR(50),
            address VARCHAR(50),
            zip_code VARCHAR(10),
            city VARCHAR(20),
            email VARCHAR(50)
        )",

        "orders" => "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT,
            status ENUM('Processing', 'Completed', 'Shipped', 'Canceled') NOT NULL,
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            total_amount DECIMAL(10, 2) NOT NULL,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        )",

        "order_items" => "CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT,
            product_id INT,
            quantity INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )"
    );

    foreach ($tables as $tableName => $createTableSQL) {
        $result = $connection->query("SHOW TABLES LIKE '$tableName'");
        if ($result->num_rows == 0) {
            $connection->query($createTableSQL);
            echo "Table '$tableName' created successfully.<br>";
        }
    }
}
?>
