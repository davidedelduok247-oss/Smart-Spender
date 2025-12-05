<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Page 1 collects the full name and stores it in the session
    $_SESSION['signup_full_name'] = $_POST['first-name'] ?? '';

    // Go to page 2 (email + password)
    header("Location: SS_Signup_pg2.php");
    exit();
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
      <span class="name">
        <strong>Smart</strong> <br>
        <strong>Spender</strong>
      </span>
    </div>
  </header>

  <!-- Main content -->
  <main class="signup-main container">
    <h1>Your smartSpender story starts here</h1>

    <form class="signup-form" action="" method="post">
      <label for="first-name">Name</label>
      <input
        id="first-name"
        name="first-name"
        type="text"
        placeholder="Enter your name"
        required
      >
      
      <button class="btn" type="submit">Next</button>
    </form>
  </main>
</body>
</html>
