<?php
require_once "database.php";

$connection = getDatabase();
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePost($connection);
}

$orders = getAllOrders($connection);

echo "<table border='1'>";
echo "<tr><th>Order ID</th>";
echo "<th>Date</th>";
echo "<th>Status</th>";
echo "<th>Total amount</th>";
echo "<th>Customer name</th>";
echo "<th>last name</th>";
echo "<th>Action</th>";
echo "</tr>";

foreach ($orders as $order) {
    echo "<tr>";
    echo "<td>" . ($order['order_id'] ?? '') . "</td>";
    echo "<td>" . ($order['order_date'] ?? '') . "</td>";
    echo "<td>" . ($order['status'] ?? '') . "</td>";
    echo "<td>" . ($order['total_amount'] ?? '') . " kr</td>";
    echo "<td>" . ($order['customer_first_name'] ?? '') . "</td>";
    echo "<td>" . ($order['customer_last_name'] ?? '') . "</td>";
    echo "<td>";

    echo "<form method='post' action='admin.php'>";
    echo "<input type='hidden' name='delete_order' value='" . ($order['order_id'] ?? '') . "'>";
    echo "<input type='submit' value='Delete'>";
    echo "</form>";

    echo "<form method='post' action='admin.php'>";
    echo "<input type='hidden' name='update_status' value='" . ($order['order_id'] ?? '') . "'>";
    echo "<label for='new_status'>";
    echo "<select name='new_status' id='new_status'>";
    echo "<option value='Processing' " . ($order['status'] === 'Processing' ? 'selected' : '') . ">Processing</option>";
    echo "<option value='Completed' " . ($order['status'] === 'Completed' ? 'selected' : '') . ">Completed</option>";
    echo "<option value='Shipped' " . ($order['status'] === 'Shipped' ? 'selected' : '') . ">Shipped</option>";
    echo "<option value='Canceled' " . ($order['status'] === 'Canceled' ? 'selected' : '') . ">Canceled</option>";
    echo "</select>";
    echo "</label>";
    echo "<input type='submit' value='Update'>";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

function handlePost($connection){
    if (isset($_POST['delete_order'])) {
        $orderIdToDelete = $_POST['delete_order'];
        deleteOrder($connection, $orderIdToDelete);
        header('Location: admin.php');
        exit;
    } elseif (isset($_POST['update_status'])) {
        $orderIdToUpdate = $_POST['update_status'];
        $newStatus = $_POST['new_status'];
        updateOrderStatus($connection, $orderIdToUpdate, $newStatus);
        header('Location: admin.php');
        exit;
    }
}

function getAllOrders($connection)
{
    $query = "SELECT orders.id AS order_id, orders.order_date, orders.status, orders.total_amount,
              customers.first_name AS customer_first_name, customers.last_name AS customer_last_name
              FROM orders
              JOIN customers ON orders.customer_id = customers.id
              ORDER BY orders.order_date DESC";

    $result = $connection->query($query);

    if (!$result) {
        die("Error in query: " . $connection->error);
    }

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}

function deleteOrder($connection, $orderId)
{
    $query = "DELETE FROM orders WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $stmt->close();
}

function updateOrderStatus($connection, $orderId, $newStatus)
{
    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("si", $newStatus, $orderId);
    $stmt->execute();
    $stmt->close();
}
