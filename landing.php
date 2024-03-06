<!DOCTYPE html>
<link rel="stylesheet" href="css/reset.css" />
<link rel="stylesheet" href="css/landing.css" />
<html lang="en">

<head>
  <title>Pantry</title>
  <link rel="stylesheet" href="css/reset.css" />
  <link rel="stylesheet" href="css/landing.css" />
</head>

<body>
  <header>
    <?php
    require_once 'header.php';
    ?>
  </header>
  <main>
    <div class="container">
      <h1>Browse by category</h1>
      <div class="category-container">
        <?php
        require_once 'connect.php';
        $sql = "SELECT * FROM categories";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $categories = $statement->fetchAll(PDO::FETCH_ASSOC);
        // display list with each category as a button class that links to search_results.php
        foreach ($categories as $category) {
          // use a form with post method to send category name to search_results.php
          echo '<form action="search_results.php" method="post">
          <input type="hidden" name="category" value="' . $category['name'] . '">
          <input class="category" type="submit" value="' . $category['name'] . '">
          </form>';
        }
        ?>
      </div>
    </div>
    <div class="body-container">
      <div class="left-container">
        <h1>Items currently at great prices</h1>
        <!-- <?php
              require_once 'connect.php';

              $sql = "SELECT * FROM grocery_items LIMIT 10";
              $statement = $pdo->prepare($sql);
              $statement->execute();
              $products = $statement->fetchAll(PDO::FETCH_ASSOC);
              echo '<ul>';
              foreach ($products as $product) {
                echo '<li><a href="product_details.php?id=' . $product['id'] . '">' . $product['name'] . '</a></li>';
              }
              echo '</ul>';
              ?> -->

        <?php
        require_once 'connect.php';
        // table grocery_items( id 	name 	brand 	category_name 	description 	image_url 	weight) 
        // table grocery_item_prices( id 	grocery_item_id 	store_id 	price 	price_date)
        // table stores(id 	name 	address 	city 	state 	zip 	)
        // for each item, use the price date and price to get an average price, then show items sorted by latest price most greatly discounted from average price
        $sql = "SELECT grocery_items.id, grocery_items.name, grocery_items.brand, grocery_items.image_url, grocery_item_prices.price, grocery_item_prices.price_date, stores.name as store_name FROM grocery_items JOIN grocery_item_prices ON grocery_items.id = grocery_item_prices.grocery_item_id JOIN stores ON grocery_item_prices.store_id = stores.id ORDER BY grocery_item_prices.price_date DESC LIMIT 10";


        $statement = $pdo->prepare($sql);
        $statement->execute();
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <!-- create div class for each product, with name of product, current price, 52week low, 52week high,  -->
        <div class="product-container">
          <?php
          foreach ($products as $product) {
            // use a form with post method to send product id and store name to search_results.php
            echo '<a href="product_details.php?id=' . $product['id'] . '&store=' . $product['store_name'] . '">';
            echo '<div class="product">';
            echo '<h2>' . $product['name'] . '</h2>';
            echo '<p>' . $product['brand'] . '</p>';
            echo '<p> $' . $product['price'] . '</p>';
            echo '<p>' . $product['store_name'] . '</p>';
            echo '</div>';
            echo '</a>';
          }
          ?>
        </div>
      </div>
      <div class="right-container">
        <h1>Watchlist</h1>
        <?php
        require_once 'connect.php';
        // table grocery_items( id 	name 	brand 	category_name 	description 	image_url 	weight)
        // table grocery_item_prices( id 	grocery_item_id 	store_id 	price 	price_date)
        // table cart( id 	user_id 	grocery_item_id 	quantity 	) as watchlist

        // check if user is logged in
        if (isset($_SESSION['user'])) {
          $user_id = $_SESSION['user'];
        } else {
          echo 'You must be logged in to view your watchlist';
        }
        // get list of items in watchlist and price from grocery_item_prices
        $sql = "SELECT * FROM cart WHERE user_id = $user_id";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $watchlist = $statement->fetchAll(PDO::FETCH_ASSOC);
        // display list of items in watchlist
        foreach ($watchlist as $item) {
          $product_id = $item['product_id'];
          // get product info from grocery_items and store name from stores table
          $sql = "SELECT grocery_items.id, grocery_items.name, grocery_items.brand, grocery_items.category_name, grocery_items.description, grocery_items.image_url, grocery_items.weight, grocery_item_prices.price, grocery_item_prices.price_date, stores.name AS store_name
                  FROM grocery_items
                  INNER JOIN grocery_item_prices ON grocery_items.id = grocery_item_prices.grocery_item_id
                  INNER JOIN stores ON grocery_item_prices.store_id = stores.id
                  WHERE grocery_items.id = $product_id";
          $statement = $pdo->prepare($sql);
          $statement->execute();
          $product = $statement->fetch(PDO::FETCH_ASSOC);
          // use a form with post method to send product id and store name to search_results.php
          echo '<a href="product_details.php?id=' . $product['id'] . '&store=' . $product['store_name'] . '">';
          echo '<div class="product">';
          echo '<h2>' . $product['name'] . '</h2>';
          echo '<p>' . $product['brand'] . '</p>';
          echo '<p> $' . $product['price'] . '</p>';
          echo '<p>' . $product['store_name'] . '</p>';
          echo '</div>';
          echo '</a>';
        }

        ?>
      </div>
    </div>
    </div>
    </div>
  </main>
</body>

</html>