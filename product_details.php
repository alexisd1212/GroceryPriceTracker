<!DOCTYPE html>
<html>

<head>
  <title>Product Details</title>
  <link rel="stylesheet" href="css/reset.css" />
  <link rel="stylesheet" href="css/landing.css" />
  <!-- Include Chart.js Library -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <header>
    <?php
    require_once 'header_min.php';
    ?>
  </header>

  <main>
    <?php
    // Connect to the database
    require_once 'connect.php';


    // grocery_items table (id, name, brand, category_name, description)
    // grocery_item_prices table (id, grocery_item_id, store_id, price, price_date)
    // price_history table (price_history_id, price_id, price, history_date)
    // stores table (id, name, address, city, state, zip)
    // select everything from all tables and select grocery_items.name as grocery_item_name
    $sql = "SELECT grocery_items.id, grocery_items.name, grocery_items.brand, grocery_items.description, grocery_items.image_url, grocery_item_prices.price, stores.name as store_name, grocery_item_prices.price_date, grocery_item_prices.price, grocery_item_prices.price_date, grocery_item_prices.price
              FROM grocery_items
              INNER JOIN grocery_item_prices ON grocery_items.id = grocery_item_prices.grocery_item_id
              INNER JOIN stores ON grocery_item_prices.store_id = stores.id
              WHERE grocery_items.id = ? AND stores.name = ?
              ORDER BY grocery_item_prices.price_date DESC";

    // Prepare and execute query
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_GET['id'], $_GET['store']]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="breadcrumb">
      <?php
      $name = $result[0]['name'];
      echo "<p><a href='landing.php'>Home</a> > <a href='search_results.php'>Search Results</a> > $name</p>";
      ?>
    </div>
    <div class="body-container">
      <div class="right-container">
        <?php
        // Check for results
        if (count($result) > 0) {
          // Display product details
          $row = $result[0];
          echo "<div class='product-details'>";
          echo "<h1>" . $row["name"] . "</h1>";
          echo "<img src='" . $row["image_url"] . "' alt='" . $row["name"] . "' width='100'>";
          echo "<h2>Brand: " . $row["brand"] . "</h2>";
          echo "<h2>From " . $row["store_name"] . "</h2>";
          echo "<h2> $" . $row["price"] . "</h2>";
          echo "<p>Description: " . $row["description"] . "</p>";
          echo "<p>ID: " . $_GET['id'] . "</p>";
          echo "</div>";
        } else {
          // No results found
          echo "No product details found.";
        }

        if (isset($_SESSION['user'])) {
          echo '<form action="add_to_cart.php" method="post">';
          echo '<input type="hidden" name="product_id" value="' . $_GET['id'] . '">';
          echo '<input type="submit" value="Add to Watchlist">';
          echo '</form>';
        } else {
          echo '<p>Please <a href="login.php">log in</a> to add this item to your watchlist.</p>';
        }
        ?>

      </div>
      <div class="left-container">
        <div class="chart-container">
          <?php
          echo "<p>Last Updated: " . date('F j, Y', strtotime($row["price_date"]));
          // show how long ago that was 
          $date = new DateTime($row["price_date"]);
          $now = new DateTime();
          $interval = $date->diff($now);

          echo " (";
          // show the difference in days, hours, or minutes as well as the unit
          if ($interval->y > 0) {
            echo $interval->y . " year";
            if ($interval->y > 1) {
              echo "s";
            }
            echo " ago) </p>";
          } else if ($interval->m > 0) {
            echo $interval->m . " month";
            if ($interval->m > 1) {
              echo "s";
            }
            echo " ago) </p>";
          } else if ($interval->d > 0) {
            echo $interval->d . " day";
            if ($interval->d > 1) {
              echo "s";
            }
            echo " ago) </p>";
          }


          // Generate price history data for product at store using 
          // price_history table (price_history_id, price_id FK to grocery_items_price.id, price, history_date)
          // grocery_item_prices table (id, grocery_item_id FK to grocery_items.id, store_id FK to stores.id, price, price_date)
          // stores table (id, name, address, city, state, zip)
          $sql = "SELECT price_history.price, price_history.history_date
                    FROM price_history
                    INNER JOIN grocery_item_prices ON price_history.price_id = grocery_item_prices.id
                    INNER JOIN stores ON grocery_item_prices.store_id = stores.id
                    WHERE grocery_item_prices.grocery_item_id = ? AND stores.name = ?
                    ORDER BY price_history.history_date ASC";

          // Prepare and execute query
          $stmt = $pdo->prepare($sql);
          $stmt->execute([$_GET['id'], $_GET['store']]);
          $price_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

          // Generate price history labels and data
          $price_history_labels = [];
          $price_history_data = [];
          foreach ($price_history as $row) {
            $price_history_labels[] = date('m/d/y', strtotime($row['history_date']));
            $price_history_data[] = $row['price'];
          }



          // Display chart
          echo "<div class='price-history-chart'>";
          echo "<canvas id='priceHistoryChart'></canvas>";
          echo "</div>";
          echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const priceHistoryLabels = " . json_encode(array_reverse($price_history_labels)) . ";
                        const priceHistoryData = " . json_encode(array_reverse($price_history_data)) . ";

                        const ctx = document.getElementById('priceHistoryChart').getContext('2d');
                        const priceHistoryChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: priceHistoryLabels,
                                datasets: [{
                                    label: 'Price History',
                                    data: priceHistoryData,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    tension: 0.1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    });
                    </script>";
          ?>
        </div>
      </div>
    </div>
    <!-- <div class="comments-container"> -->
    <!-- body container -->
    <div class="body-container">
      <div class="left container">
        <?php
        if (isset($_SESSION['user'])) {
          // h2 add a review
          echo '<h2>Add a Review:</h2>';

          // use a table for form
          echo '<table>';
          echo '<form action="add_review.php" method="post">';
          echo '<tr><td>Rating:</td><td><input type="number" name="rating" min="1" max="5" required></td></tr>';
          echo '<tr><td>Comment:</td><td><textarea name="comment" rows="5" cols="50" required></textarea></td></tr>';
          echo '<tr><td><input type="hidden" name="product_id" value="' . $_GET['id'] . '"></td></tr>';
          echo '<tr><td><input type="submit" value="Submit"></td></tr>';
          echo '</form>';
          echo '</table>';
        } else {
          echo '<p>Please <a href="login.php">log in</a> to add a review.</p>';
        }
        ?>
      </div>
      <div class="left-container">
        <?php
        // connect 
        require_once 'connect.php';

        // Fetch reviews for the current product
        $sql_reviews = "SELECT user_reviews.rating, user_reviews.comment, users.username
        FROM user_reviews
        INNER JOIN users ON user_reviews.user_id = users.id
        WHERE user_reviews.grocery_item_id = ?";
        $stmt_reviews = $pdo->prepare($sql_reviews);
        $stmt_reviews->execute([$_GET['id']]);
        $result_reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

        if (count($result_reviews) > 0) {
          echo '<h2>Reviews:</h2>';
          // show reviews in a table 
          echo '<table>';
          echo '<tr><th>Username</th><th>Rating</th><th>Comment</th></tr>';
          foreach ($result_reviews as $review_row) {
            echo '<tr>';
            echo '<td>' . $review_row['username'] . '</td>';
            echo '<td>' . $review_row['rating'] . '</td>';
            echo '<td>' . $review_row['comment'] . '</td>';
            echo '</tr>';
          }
          echo '</table>';
        } else {
          echo '<p>No reviews yet. Be the first to add one!</p>';
        }
        ?>
      </div>
    </div>
    <!-- </div> -->
  </main>
</body>

</html>