<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/landing.css" />
    <link rel="stylesheet" href="css/forms.css" />
  </head>
  <header>
    <?php
      require_once 'header_min.php';
    ?>
  </header>

  <body>
    <div class="wrap">
      <form
        class="account-form"
        name="login"
        action="validate_login.php"
        method="get"
      >
        <div class="form-header">
          <h2>Login</h2>
        </div>
        <div class="form-group">
          <input
            type="text"
            name="email"
            class="form-input"
            placeholder="email@example.com"
            required
          />
        </div>
        <div class="form-group">
          <input
            type="password"
            name="password"
            class="form-input"
            placeholder="password"
            required
          />
        </div>
        <div class="form-group">
          <input type="submit" value="Login" class="form-button" />
        </div>
        <div class="form-footer">
          <a href="create_account.php">Sign-Up Instead</a>
        </div>
      </form>
    </div>
  </body>
</html>
