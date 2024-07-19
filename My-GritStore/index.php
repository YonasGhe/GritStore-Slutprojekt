<?php

require_once "database.php";
require_once "product.php";
require_once "order.php";
require_once "sql.php";

$connection = getDatabase();
createTablesIfNotExist($connection);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grit Store</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="grit-store">

        <h1>Grit Store</h1>
        <form action="" method="post">
            <label for="product">Add orders</label>
            <br>
            <label for="first-name">First Name:</label>
            <input type="text" name="first_name" id="first_name" required>
            <br>
            <label for="last-name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" required>
            <br>
            <label for="social-number">Social security number:</label>
            <input type="text" name="social_number" id="social_number">
            <br>
            <label for="phone">Telephone:</label>
            <input type="tel" id="telephone" name="telephone" required>
            <br>
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" required>
            <br>
            <label for="zipCode">ZIP Code:</label>
            <input type="tel" id="zip_code" name="zip_code" required>
            <br>
            <label for="city">City:</label>
            <input type="text" name="city" id="city" required>
            <br>
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            <br>

            <?php
            $products = getProducts($connection);
            ?>
            <h3>Products</h3>
            <div class="container">
                <?php foreach ($products as $product) : ?>
                    <div class="products">
                        <img class="images" src="<?= $product['imageUrl'] ?>" alt="<?= $product['name'] ?>">
                        <div>
                            <h3><?= $product['name'] ?> - <?= $product['price'] . "kr" ?> </h3>
                            <label for="product<?= $product['id'] ?>">Quantity:
                                <input name="quantity[<?= $product['id'] ?>]" type="number" value="0" min="0">
                            </label>
                            <input type="hidden" name="product_price[<?= $product['id'] ?>]" value="<?= $product['price'] ?>">
                            <input type="hidden" name="product_id[<?= $product['id'] ?>]" value="<?= $product['id'] ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="submit" value="Add Order">
        </form>
    </div>
</body>

</html>