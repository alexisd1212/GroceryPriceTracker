<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" href="css/reset.css" />
  <link rel="stylesheet" href="css/landing.css" />
  <link rel="stylesheet" href="css/forms.css" />
</head>

<body>
  <header>
    <?php
    require_once 'header_min.php';
    ?>
  </header>
  <div class="wrap">
    <form class="account-form" name="signUpForm" method="post" action="create_account.php">
      <div class="form-header">
        <h2>Create an Account</h2>
      </div>
      <div class="form-group">
        <input type="text" name="username" class="form-input" placeholder="username" required />
      </div>
      <div class="form-group">
        <input type="text" name="email" class="form-input" placeholder="email@example.com" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" oninvalid="this.setCustomValidity('Please enter a valid email address')" />
      </div>
      <div class="form-group">
        <input type="password" name="password" class="form-input" placeholder="password (minimum 8 characters)" required minlength="8" />
      </div>
      <div class="form-group">
        <label for="image">
          <h2>Upload Profile Picture</h2>
        </label>
        <br />
        <p> if you don't upload a picture, a default one will be used </p>
        <br />
        <input type="file" name="image" class="form-input" placeholder="image" />
      </div>
      <div class="form-group">
        <input type="submit" name="submit" class="form-button" value="Create Account" />
      </div>
      <div class="form-footer">
        <a href="login.php">Login Instead</a>
      </div>
    </form>
  </div>
  <script>
    // check if an image was uploaded
    var fileInput = document.querySelector('input[type="file"]');
    fileInput.onchange = function() {
      if (fileInput.files.length > 0) {
        var file = fileInput.files[0];
        if (file.type != "image/jpg" && file.type != "image/jpeg" && file.type != "image/png" && file.type !=
          "image/gif") {
          alert("File is not an image!");
          fileInput.value = "";
        } else if (file.size > 1024 * 1024) {
          alert("File is too big!");
          fileInput.value = "";
        }
      }
    };

    // check that password is at least 8 characters
    var password = document.getElementById("password");

    function validatePassword() {
      if (password.value.length < 8) {
        password.setCustomValidity("Password must be at least 8 characters");
      } else {
        password.setCustomValidity('');
      }
    }
    password.onchange = validatePassword;
    password.onkeyup = validatePassword;
  </script>
  <?php
  try {
    require_once 'connect.php';

    // get form data 
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    // hash the password
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $image = $_POST['image'];

    // check if image was uploaded
    if (isset($_FILES["image"])) {
      $target_dir = "uploads/";
      $target_file = $target_dir . basename($_FILES["image"]["name"]);
      $uploadOk = 1;
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

      // Check file size
      if ($_FILES["image"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
      }

      // Allow certain file formats
      if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
      ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
      }

      if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
      } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
          echo "The file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";
        } else {
          echo "Sorry, there was an error uploading your file.";
        }
      }
    }
    // else use default image
    else {
      $image = "img/user_default.png";
    }

    // create a query to insert the user into the database
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $statement = $pdo->prepare($sql);
    $statement->execute([$username, $email, $password_hashed]);

    // insert image into database user_images table with user_id and image_url
    $userId = $pdo->lastInsertId();
    $sql = "INSERT INTO user_images (user_id, image_url) VALUES (?, ?)";
    $statement = $pdo->prepare($sql);
    $statement->execute([$userId, $image]);

    // return a response to the app
    $response = array();
    $response["success"] = true;

    if ($statement->rowCount() > 0) {
      session_start();
      $_SESSION['user'] = $userId;
      header("Location: landing.php");
    } else {
    }

    echo json_encode($response);
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
  ?>

</body>

</html>