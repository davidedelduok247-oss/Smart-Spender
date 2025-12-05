<?php
session_start();

// Read name from session if needed later (optional for display)
$fullName = $_SESSION['signup_full_name'] ?? '(no name received)';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Page 2 collects email and password and stores them in the session
    $_SESSION['signup_email']    = $_POST['email'] ?? '';
    $_SESSION['signup_password'] = $_POST['password'] ?? '';

    // Go to page 3 (security questions)
    header("Location: SS_Signup_pg3.php");
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
    <h1>
      Create a password with at least 8 characters, including one uppercase letter,
      one number, and one special character.
    </h1>

    <form class="signup-form" action="" method="post">
      <label for="email">Email</label>
      <input
        id="email"
        name="email"
        type="email"
        placeholder="Enter your email"
        required
      >

      <label for="password">Password</label>
      <input
        id="password"
        name="password"
        type="password"
        placeholder="Enter your password"
        required
      >

      <button class="btn" type="submit">Next</button>
    </form>
  </main>
</body>
</html>
