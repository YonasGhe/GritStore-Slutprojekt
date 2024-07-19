<?php
require_once "database.php";

$connection = getDatabase();

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = customerData($connection);

    date_default_timezone_set('Europe/Stockholm');
    if ($customerId !== false) {
        $status = "Processing";
        $date = date("Y-m-d H:i:s");

        $productQuantities = isset($_POST['quantity']) && is_array($_POST['quantity']) ? $_POST['quantity'] : [];
        $productPrices = isset($_POST['product_price']) && is_array($_POST['product_price']) ? $_POST['product_price'] : [];

        if (!empty($productQuantities)) {
            $orderId = insertOrder($connection, $customerId, $status, $date, 0.0);

            if ($orderId !== false) {
                $orderItems = insertOrderItems($connection, $orderId, $productQuantities);

                if ($orderItems) {
                    $totalAmount = calculateTotalAmount($orderItems);
                    updateOrder($connection, $orderId, $totalAmount);
                    echo "Order added successfully!";
                } else {
                    echo "Error inserting order items";
                }
            } 
        }
    }
}

function calculateTotalAmount($orderItems)
{
    $totalAmount = 0.0;

    foreach ($orderItems as $item) {
        $totalAmount += $item['amount'];
    }

    return $totalAmount;
}

function insertOrderItems($connection, $orderId, $productQuantities)
{
    if (!is_array($productQuantities)) {
        return false;
    }

    $orderItems = [];

    foreach ($productQuantities as $product_id => $quantity) {
        global $productPrices;

        $price = isset($productPrices[$product_id]) ? $productPrices[$product_id] : 0.0;
        $amount = $price * $quantity;

        $orderItems[] = [
            'order_id' => $orderId,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'amount' => $amount,
        ];
    }

    $query = "INSERT INTO order_items (order_id, product_id, quantity, amount) VALUES (?, ?, ?, ?)";
    $stmt = $connection->prepare($query);

    foreach ($orderItems as $item) {
        $stmt->bind_param("iiid", $item['order_id'], $item['product_id'], $item['quantity'], $item['amount']);
        $stmt->execute();
    }

    $stmt->close();

    return $orderItems;
}

function insertOrder($connection, $customerId, $status, $date, $totalAmount)
{
    $query = "INSERT INTO orders (customer_id, status, order_date, total_amount) VALUES (?, ?, ?, ?)";

    $stmt = $connection->prepare($query);
    $stmt->bind_param("isss", $customerId, $status, $date, $totalAmount);

    if ($stmt->execute()) {
        $orderId = $stmt->insert_id;
        $stmt->close();
        return $orderId;
    } else {
        return false;
    }
}

function updateOrder($connection, $orderId, $totalAmount)
{
    if ($totalAmount === null) {
        $totalAmount = 0.0;
    }

    if ($orderId !== null) {
        $query = "UPDATE orders SET total_amount = ? WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("di", $totalAmount, $orderId);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function customerData($connection)
{
    $customerName = $_POST["first_name"] ?? null;
    $customerLastName = $_POST["last_name"] ?? null;
    $customerEmail = $_POST["email"] ?? null;
    $customerSsn = $_POST["social_number"] ?? null;
    $customerTelephone = $_POST["telephone"] ?? null;
    $customerAddress = $_POST["address"] ?? null;
    $customerZipCode = $_POST["zip_code"] ?? null;
    $customerCity = $_POST["city"] ?? null;

    $query = "INSERT INTO customers (first_name, last_name, social_number, telephone, address, zip_code, city, email)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $connection->prepare($query);

    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ssssssss", $customerName, $customerLastName, $customerSsn, $customerTelephone, $customerAddress, $customerZipCode, $customerCity, $customerEmail);

    $result = $stmt->execute();

    if ($result) {
        $customerId = $connection->insert_id;
        $stmt->close();
        return $customerId;
    } else {
        $stmt->close();
        return false;
    }
}



