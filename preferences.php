<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" href="css/reset.css" />
  <link rel="stylesheet" href="css/landing.css" />
  <title>Pantry</title>
</head>

<body>
  <header>
    <?php
    include_once 'header_min.php';
    ?>
  </header>
  <div class="main">
    <div class="breadcrumb">
      <?php
      echo "<p><a href='landing.php'>Home</a> > Preferences</p>";
      ?>
    </div>
    <?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);
    try {
      require_once 'connect.php';

      // get user id from session
      session_start();
      $user_id = $_SESSION['user'];

      // get username, email, and password from database users table (id,username,email,password,role)
      $sql = "SELECT username, email, password FROM users WHERE id = $user_id";
      $statement = $pdo->prepare($sql);
      $statement->execute();

      // check if role is admin
      $sql3 = "SELECT role FROM users WHERE id = $user_id";
      $statement3 = $pdo->prepare($sql3);
      $statement3->execute();
      $role = $statement3->fetchColumn();

      // get image from database user_images table (id, user_id, image_url(varchar))
      $sql2 = "SELECT image_url FROM user_images WHERE user_id = $user_id";
      $statement2 = $pdo->prepare($sql2);
      $statement2->execute();

      // show image and user info
      while ($row = $statement->fetch()) {
        // use table
        echo '<div class="preferences">';
        echo '
        <div class="user-image">
          <h2>User Image</h2>
          <img src="' . $statement2->fetchColumn() . '" alt="user image" width="100">
        </div>
          <div class="user-info">
            <h2>User Info</h2>
            <table>
              <tr>
                <td>Username:</td>
                <td>' . $row['username'] . '</td>
              </tr>
              <tr>
                <td>Email:</td>
                <td>' . $row['email'] . '</td>
              </tr>
            </table>
          </div>
        </div>
        ';
      }
      // option to change password
      echo '
        <div class="change-password">
          <h2>Change Password</h2>
          <form action="change_password.php" method="post">
            <label for="old-password">Old Password:</label>
            <input type="password" id="old-password" name="old-password" placeholder="Old Password" />
            <label for="new-password">New Password:</label>
            <input type="password" id="new-password" name="new-password" placeholder="New Password" />
            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" />
            <input type="submit" value="Submit" />
          </form>
        </div>
        ';

      // check if url has error message
      if (isset($_GET['message'])) {
        // get error message
        $message = $_GET['message'];
        // if error message is 1, show error message
        if ($message == 1) {
          echo '<p class="error">Password changed successfully!</p>';
        } else if ($message == 2) {
          echo '<p class="error">New password does not match confirm password!</p>';
        } else if ($message == 3) {
          echo '<p class="error">Old password does not match password in database!</p>';
        }
      }

      // if role is admin, include options to add price to grocery_item_prices table (id, grocery_item_id, store_id, price, price_date) from list of products from grocery_items table (id, name, brand, category_name, description, image_url, weight)
      if ($role == 'admin') {
        // query for list of products with id and name
        $sql4 = "SELECT id, name FROM grocery_items";
        $statement4 = $pdo->prepare($sql4);
        $statement4->execute();

        // query for list of stores with id and name
        $sql5 = "SELECT id, name FROM stores";
        $statement5 = $pdo->prepare($sql5);
        $statement5->execute();

        echo '
          <div class="admin-options">
            <h2>Admin Options</h2>
            <form action="add_price.php" method="post">
              <label for="product">Product:</label>
              <select name="product" id="product">
                <option value="">Select Product</option>
                ';
        while ($row = $statement4->fetch()) {
          echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
        }
        echo '
              </select>
              <label for="store">Store:</label>
              <select name="store" id="store">
                <option value="">Select Store</option>
                ';
        while ($row = $statement5->fetch()) {
          echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
        }
        echo '
              </select>
              <label for="price">Price:</label>
              <input type="text" id="price" name="price" placeholder="Price" />
              <label for="date">Date:</label>
              <input type="text" id="date" name="date" placeholder="Date (YYYY-MM-DD)" />
              <input type="submit" value="Submit" />
            </form>
          </div>
          ';
      }

      // check get variable for success message
      if (isset($_GET['success'])) {
        echo '<h2>' . $_GET['success adding price'] . '</h2>';
      }
    } catch (PDOException $e) {
      die($e->getMessage());
    }
    ?>
    <!-- log out button ends php session -->
    <a href="logout.php">
      <div class="button" style="width:5rem; margin-left:1rem;">
        <h2>Log Out</h2>
      </div>
    </a>
  </div>
  </div>
</body>

</html>