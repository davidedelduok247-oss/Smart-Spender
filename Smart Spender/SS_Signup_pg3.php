<?php
session_start();
if (empty($_SESSION['signup_full_name']) || empty($_SESSION['signup_password'])) {
    header('Location: signup1.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['signup_Q1'] = $_POST['Q1'];
    $_SESSION['signup_Q2'] = $_POST['Q2'];
    $_SESSION['signup_Q3'] = $_POST['Q3'];
    $_SESSION['signup_Q4'] = $_POST['Q4'];

    header('Location: SS_Signup_pg4.php');
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
      <span class="name"><strong>Smart</strong> <br> 
        <strong>Spender</strong> </span>
    </div>
  </header>

  <!-- Main content -->
  <main class="signup-main container">
    <h1>Please select security questions</h1>

    <form class="signup-form" action="page3.html" method="get">
        <label for="Q1">What is the middle name of your oldest sibling?</label>
        <input id="Q1" name="Q1" type="text" placeholder="" required>

        <label for="Q2">In what city or town did your parents meet?</label>
      <input id="Q2" name="Q2" type="text" placeholder="" required>
      
        <label for="Q3">What was your dream job as a child?</label>
      <input id="Q3" name="Q3" type="text" placeholder="" required>

        <label for="Q4">What was your first job title?</label>
      <input id="Q4" name="Q4" type="text" placeholder="" required>

        <label for="Q4">Who was your celebrity crush?</label>
      <input id="Q4" name="Q4" type="text" placeholder="" required>

            <!--will need to add on this with php later on-->
      <button class="btn" type="submit">Next</button>

    </form>
  </main>
</body>
</html>
