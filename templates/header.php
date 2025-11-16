<header class="shadow-sm border-bottom">
<nav class="navbar navbar-expand-lg navbar-light bg-white py-2">
  <div class="container">

    <!-- Logo + Brand -->
    <a href="<?php echo $base_url; ?>/" class="d-flex align-items-center text-decoration-none">
      <img src="<?php echo $base_url; ?>/assets/images/logo.png" 
        alt="Vetriarasiwatersupply"
        class="me-2"
        style="height:34px;width:auto;">
      <span style="
          font-size:1.35rem;
          font-weight:700;
          font-family:'Inter', sans-serif;
          letter-spacing:-0.5px;
          color:#0b74ff;">
        Vetriarasiwatersupply
      </span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse mt-2 mt-lg-0" id="navMain">

      <!-- Search 
      <form class="d-flex mx-lg-4" action="<?php echo $base_url; ?>/index.php" method="get" style="flex:1;max-width:600px;">
        <div class="input-group modern-search">
          <input 
            name="q"
            type="search"
            class="form-control border-end-0"
            placeholder="Search for products..."
            value="<?php echo esc($_GET['q'] ?? ''); ?>">
          <button class="btn btn-primary px-3" type="submit">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </form> -->

      <!-- Links -->
      <ul class="navbar-nav ms-lg-3 mb-2 mb-lg-0 align-items-lg-center">

        <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/product.php">Products</a></li>
        <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/about.php">About</a></li>
        <li class="nav-item"><a class="nav-link modern-link" href="<?php echo $base_url; ?>/contact.php">Contact</a></li>

        <!-- Categories -->
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle modern-link" href="#" id="catDropdown" role="button" data-bs-toggle="dropdown">
    Categories
  </a>
  <ul class="dropdown-menu shadow-sm border-0 rounded-3">
    <?php foreach($catRows as $cr): if(!$cr['category']) continue; ?>
      <li>
        <a class="dropdown-item py-2" href="<?php echo $base_url; ?>/index.php?category=<?php echo urlencode($cr['category']); ?>">
          <?php echo esc($cr['category']); ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</li>

<li class="nav-item">
  <a class="nav-link modern-link" href="<?php echo $base_url; ?>/track.php">Track Order</a>
</li>
</ul>



      <!-- Right Buttons -->
      <div class="ms-auto d-flex align-items-center gap-2 mt-2 mt-lg-0">

        <a class="btn btn-outline-primary btn-sm px-3" href="<?php echo $base_url; ?>/cart.php">
          <i class="bi bi-cart3"></i>
          <span class="ms-1">🛒 Cart</span>
        </a>

        <?php if(is_logged_in()): ?>
          <a class="btn btn-link modern-link fw-semibold" href="<?php echo $base_url; ?>/profile.php">
            <?php echo esc($_SESSION['user_name']); ?>
          </a>
          <a class="btn btn-link modern-link text-danger" href="<?php echo $base_url; ?>/logout.php">Logout</a>
        <?php else: ?>
          <a class="btn btn-primary btn-sm px-3" href="<?php echo $base_url; ?>/login.php">Login</a>
          <a class="btn btn-outline-secondary btn-sm px-3" href="<?php echo $base_url; ?>/register.php">Register</a>
        <?php endif; ?>

        <a class="btn btn-dark btn-sm px-3 rounded-pill" href="<?php echo $base_url; ?>/admin/login.php">
          Admin
        </a>

      </div>
    </div>
  </div>
</nav>
</header>
