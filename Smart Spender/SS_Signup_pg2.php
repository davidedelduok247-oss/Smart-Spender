<?php
session_start();
if (empty($_SESSION['signup_full_name'])) {

  header('Location: SS_Signup_pg1.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $confirm  = $_POST['password_confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $_SESSION['signup_email']    = $email;    // overwrite if changed
        $_SESSION['signup_password'] = $password; // plain for now, we hash later
        header('Location: SS_Signup_pg3.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Smart Spender</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <!-- Logo top-left -->
  <header class="signup-header">
    <div class="brand">
      <span class="logo">&#128178;</span>
      <span class="name"><strong>Smart</strong> <br> 
        <strong>Spender</strong> </span>
    </div>
  </header>

  <!-- Main content -->
  <main class="signup-main container">
    <h1>Create a password with atleast 8 characters, including one uppercase letter, one number, and one special character.</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>


    <form class="signup-form" action="page3.html" method="get">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="Enter your email" required>

        <label for="Password">Password</label>
        <input id="Password" name="Password" type="text" placeholder="Enter your name" required>

        <!--will need to add on this with php later on-->
      <button class="btn" type="submit">Next</button>
      <a href="SS_Signup_pg3.php" aria-disabled="true"></a> <!--Double check the aria-disabled to ensure you cant go to the next page without putting in proper credentials-->

    </form>
  </main>
</body>
</html>
