<!DOCTYPE html>
<html>

<head>
  <title>Search Results</title>
  <link rel="stylesheet" href="css/reset.css" />
  <link rel="stylesheet" href="css/landing.css">
  <script src="js/livesearch.js"></script>

  <script src="showHint.js">
  </script>
</head>

<body>
  <header>
    <?php
    require_once 'header_min.php';
    ?>
  </header>
  <main>
    <div class="breadcrumb">
      <?php
      echo "<p><a href='landing.php'>Home</a> > Search Results</p>";
      ?>
    </div>
    <div class="body-container">
      <div class="right-container">
        <h1>Search </h1>
        <!-- form to search, filter, and sort -->
        <!-- use a table -->
        <form action="search_results.php" method="post">
          <table>
            <tr>
              <td>
                <label for="item-name">Item Name</label>
              </td>
              <td>
                <input type="text" name="item-name" id="item-name" onkeyup="showHint(this.value)">
              </td>
            </tr>
            <p>Suggestions: <span id="txtHint"></span></p>
            <tr>
              <td>
                <label for="location">Location</label>
              </td>
              <td>
                <select name="location" id="location">
                  <option value="">All</option>
                  <?php
                  // Include the connect.php file to establish a connection using the $pdo variable
                  require_once 'connect.php';

                  // Build query
                  $sql = "SELECT DISTINCT city FROM stores ORDER BY city ASC";

                  // Prepare and execute query
                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();
                  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  // Check for results
                  if (count($result) > 0) {
                    // Display cities
                    foreach ($result as $row) {
                      echo "<option value='" . $row['city'] . "'>" . $row['city'] . "</option>";
                    }
                  } else {
                    // No results found
                    echo "<option value=''>No cities found.</option>";
                  }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td>
                <label for="store">Store</label>
              </td>
              <td>
                <select name="store" id="store">
                  <option value="">All</option>
                  <?php
                  // Include the connect.php file to establish a connection using the $pdo variable
                  require_once 'connect.php';

                  // Build query
                  $sql = "SELECT id, name FROM stores ORDER BY name ASC";

                  // Prepare and execute query
                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();
                  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  // Check for results
                  if (count($result) > 0) {
                    // Display stores
                    foreach ($result as $row) {
                      echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                  } else {
                    // No results found
                    echo "<option value=''>No stores found.</option>";
                  }
                  ?>
                </select>
              </td>
            </tr>
            <!-- category -->
            <tr>
              <td>
                <label for="category">Category</label>
              </td>
              <td>
                <select name="category" id="category">
                  <option value="">All</option>
                  <?php
                  // Include the connect.php file to establish a connection using the $pdo variable
                  require_once 'connect.php';
                  // table categories (id, name)
                  // Build query
                  $sql = "SELECT id, name FROM categories ORDER BY name ASC";

                  // Prepare and execute query
                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();
                  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                  // Check for results
                  if (count($result) > 0) {
                    // Display categories
                    foreach ($result as $row) {
                      echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                    }
                  } else {
                    // No results found
                    echo "<option value=''>No categories found.</option>";
                  }
                  ?>
                </select>
              </td>
            </tr>

            <tr>
              <td>
                <label for="sort">Sort</label>
              </td>
              <td>
                <select name="sort" id="sort">
                  <option value="name-asc">Name (A-Z)</option>
                  <option value="name-desc">Name (Z-A)</option>
                  <option value="price-asc">Price (Low to High)</option>
                  <option value="price-desc">Price (High to Low)</option>
                </select>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <input type="submit" value="Search">
              </td>
            </tr>
          </table>
        </form>

      </div>

      <div class="left-container">

        <h1>Results </h1>
        <?php
        // Include the connect.php file to establish a connection using the $pdo variable
        require_once 'connect.php';

        // if category is in url from clicking on category link, set it to a variable
        if (isset($_GET['category'])) {
          $selected_category = $_GET['category'];
        } else {
          $selected_category = '';
        }

        // Get search query and selected store and city from form submission
        $search_query = '%' . $_POST['item-name'] . '%';
        $selected_city = $_POST['location'];
        $selected_store = $_POST['store'];
        $selected_category = $_POST['category'];

        // if null values are passed, set them to empty strings
        if ($selected_city == null) {
          $selected_city = '';
        }
        if ($selected_store == null) {
          $selected_store = '';
        }
        if ($selected_category == null) {
          $selected_category = '';
        }

        // trim whitespace from search query
        $search_query = trim($search_query);

        // check for sort
        if (isset($_POST['sort'])) {
          $sort = $_POST['sort'];
          switch ($sort) {
            case 'name-asc':
              $sort = 'grocery_items.name ASC';
              break;
            case 'name-desc':
              $sort = 'grocery_items.name DESC';
              break;
            case 'price-asc':
              $sort = 'grocery_item_prices.price ASC';
              break;
            case 'price-desc':
              $sort = 'grocery_item_prices.price DESC';
              break;
            default:
              $sort = 'grocery_items.name ASC';
              break;
          }
        } else {
          $sort = 'grocery_items.name ASC';
        }


        // Build and execute SQL query using prepared statements
        $sql = "SELECT grocery_items.id, grocery_items.name, grocery_items.description, grocery_items.image_url, stores.name AS store_name, grocery_item_prices.price
      FROM grocery_items
      JOIN grocery_item_prices ON grocery_items.id = grocery_item_prices.grocery_item_id
      JOIN stores ON grocery_item_prices.store_id = stores.id
      WHERE grocery_items.name LIKE ?
      AND (stores.city = ? OR ? = '')
      AND (stores.name = ? OR ? = '')
      AND (grocery_items.category_name = ? OR ? = '')
      ORDER BY " . $sort;

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$search_query, $selected_city, $selected_city, $selected_store, $selected_store, $selected_category, $selected_category]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="product-container">
          <?php
          // Check if there are any results and display them if there are
          if (count($result) > 0) {
            foreach ($result as $row) {
              // pass grocery_item_id and store_name to url
              echo '<a href="product_details.php?id=' . $row['id'] . '&store=' . $row['store_name'] . '">';
              echo '<div class="product">';
              echo '<img src="' . $row['image_url'] . '" alt="' . $row['name'] . '" style="max-width: 100px; max-height: 100px;">';
              echo '<h3>' . $row['name'] . '</h3>';
              echo '<p>' . $row['description'] . '</p>';
              echo '<p>' . $row['store_name'] . '</p>';
              echo '<p>$' . $row['price'] . '</p>';
              echo '</div>';

              echo '</a>';
            }
          } else {
            echo '<p>No results found.</p>';
          }
          ?>

        </div>
      </div>
    </div>
  </main>
</body>

</html>