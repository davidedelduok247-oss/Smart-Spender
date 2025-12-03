<?php
session_start();

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Smart Spender</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <!-- Top bar -->
  <header class="topbar">
    <button class="menu-btn" aria-label="menu">≡</button>
    <div class="search">
      <input type="text" placeholder="Search" />
    </div>
    <div class="icons">
      <span class="icon">&#128100</span>
      <span class="icon">&#9881</span>
    </div>
  </header>

  <!-- Main content -->
  <main class="hero container">
    <section class="left">
      <div class="brand">
        <span class="logo">&#128178</span>
        <span class="name"><strong>Smart</strong> <br> 
        <strong>Spender</strong> </span>
      </div>

      <p class="bold" style="text-indent: 5em;">Master Your Money, One Smart Choice at a Time.</p>

      <h1 class="welcome">Welcome to <span class="highlight">Smart Spender!</span></h1>

      <p class="lead">
        <strong>Budgeting made simple. Just enter your income and monthly
        expenses—we'll do the rest. <br> Get a personalized plan that helps you save,
        <em>spend wisely</em>, and stay on track.</strong>
      </p>

      <div class="dots" aria-hidden="true">
        <span></span><span></span><span></span><span></span>
      </div>
    </section>

    <!-- Phone mockup with sign-in -->
    <aside class="phone">
      <div class="notch"></div>
      <form class="screen" action="#" method="post">
        <h2>Sign in</h2>

    <!-- Login Form -->

            <?php if (!empty($_SESSION['login_error'])): ?>
            <p style="color: red;"></p><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></p>
        <?php endif; ?>


        <label>Email</label>
        <input type="email" placeholder="john@example.com" required />

        <label>Password</label>
        <input type="password" placeholder="******" required />

        <button class="btn" onclick="location.href='SS_dashboard.php'">Sign In</button>

        <p class="muted">
          Don’t have an account?
          <a href="SS_Signup_pg1.php" aria-disabled="true">Sign up</a>
        </p>
      </form>
    </aside>
  </main>
</body>
</html>
 
