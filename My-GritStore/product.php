<?php

require_once "database.php";
require_once "index.php";

$connection = getDatabase();

    function getId($connection, $id)
    {
        $query = "SELECT price FROM products WHERE id = $id";
        $result = $connection->query($query);

        if (!$result) {
            echo "Error executing query: " . $connection->error;
            return 0.00;
        }

        if ($row = $result->fetch_assoc()) {
            echo "Debug: Retrieved price for product ID $id: " . $row['price'] . "<br>";
            return $row['price'];
        }

        echo "Debug: No price found for product ID $id<br>";
        return 0.00;
    }

function getProducts($connection)
{
    $query = "SELECT id, name, price, image FROM products";
    $result = $connection->query($query);

    if (!$result) {
        echo "Error executing query: " . $connection->error;
        return array();
    }

    $products = array();

    while ($row = $result->fetch_assoc()) {
        $row['imageUrl'] = 'media/' . $row['image'];
        $products[] = $row;
    }

    $result->free_result();

    return $products;
}
