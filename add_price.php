<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
try {
    require_once 'connect.php';

    //    get post data from form (product id, store id, price, date)
    $product = $_POST['product'];
    $store = $_POST['store'];
    $price = $_POST['price'];
    $date = $_POST['date'];

    //    sanitize data
    $price = trim($price);
    $date = trim($date);
    // format date
    $date = date('Y-m-d', strtotime($date));


    // add price to price_history table (price_history_id, price_id (FK) to grocery_item_prices.id, price, history_date)
    // grocery_item_prices (id, grocery_item_id FK to grocery_items.id, store_id, price, price_date)
    // grocery_items (id, name)

    //    create sql statement
    $sql = "INSERT INTO price_history (price_id, price, history_date) VALUES ((SELECT id FROM grocery_item_prices WHERE grocery_item_id = $product AND store_id = $store), $price, '$date')";
    $statement = $pdo->prepare($sql);
    $statement->execute();

    // //   add price to grocery_item_prices table (id, grocery_item_id, store_id, price, price_date)
    // $sql = "INSERT INTO grocery_item_prices (grocery_item_id, store_id, price, price_date) VALUES (:product, :store, :price, :date)";
    // $statement = $pdo->prepare($sql);
    // $statement->bindValue(':product', $product);
    // $statement->bindValue(':store', $store);
    // $statement->bindValue(':price', $price);
    // $statement->bindValue(':date', $date);
    // $statement->execute();

    //    redirect to preferences.php with success message
    header("Location: preferences.php?success=1");
} catch (PDOException $e) {
    die($e->getMessage());
}
