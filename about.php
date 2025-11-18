<?php
require_once 'init.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>About - Vetriarasi Water Supply</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
  body { background: #f4f7fb; font-family: "Poppins", sans-serif; }

  .about-banner {
    background: linear-gradient(135deg, #0A84FF 0%, #074a74 100%);
    color: white; padding: 50px;
    border-radius: 18px; 
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  }

  .feature-box {
    background: white; padding: 25px;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    transition: 0.3s ease;
  }
  .feature-box:hover { transform: translateY(-5px); }

  .feature-box i { font-size: 2.4rem; color: #0A84FF; margin-bottom: 10px; }

  .highlight-card {
    background: #0A84FF;
    border-radius: 16px; color:white;
    padding: 30px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  }

  .highlight-card i {
    font-size: 3rem;
    margin-bottom: 10px;
  }

  .badge-icon {
    font-size: 2rem; color:#0A84FF;
  }
</style>
</head>

<body>
<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container py-5">

  <!-- HEADER BANNER -->
  <div class="about-banner mb-5">
    <h2 class="fw-bold">About Vetriarasi Water Supply</h2>
    <p class="mt-3 mb-0">
      Delivering purity, trust, and reliable water solutions since day one.
      Your hydration is our responsibility, and we take it seriously.
    </p>
  </div>

  <!-- OUR STORY -->
  <div class="mb-5">
    <h3 class="fw-bold mb-3">Our Story</h3>
    <p class="lead text-muted">
      Vetriarasi Water Supply was founded with a vision to make clean and affordable water 
      accessible to every home and business. With a proven track record of timely delivery, 
      strict hygiene standards, and transparent service, we have become one of the most 
      trusted suppliers in the region.
    </p>
  </div>

  <!-- HIGHLIGHT CARDS -->
  <div class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="highlight-card">
        <i class="bi bi-shield-check"></i>
        <h4 class="fw-bold">100% Pure & Tested</h4>
        <p>Every drop is quality-checked and compliant with FSSAI standards.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="highlight-card">
        <i class="bi bi-truck"></i>
        <h4 class="fw-bold">Fast & Reliable Delivery</h4>
        <p>Our fleet ensures timely deliveries — always on schedule.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="highlight-card">
        <i class="bi bi-people-fill"></i>
        <h4 class="fw-bold">200+ Happy Customers</h4>
        <p>We are proud to serve households, shops, offices & industries.</p>
      </div>
    </div>
  </div>

  <!-- WHY CHOOSE US -->
  <h3 class="fw-bold mb-4">Why Choose Us?</h3>
  <div class="row g-4 mb-5">

    <div class="col-md-3 col-sm-6">
      <div class="feature-box text-center">
        <i class="bi bi-trophy-fill"></i>
        <h6 class="fw-bold">Trusted Brand</h6>
        <p class="text-muted small">Known for honest pricing & clean supply.</p>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="feature-box text-center">
        <i class="bi bi-droplet"></i>
        <h6 class="fw-bold">Hygienic & Safe</h6>
        <p class="text-muted small">Handled with strict hygiene procedures.</p>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="feature-box text-center">
        <i class="bi bi-clock-history"></i>
        <h6 class="fw-bold">On-Time Delivery</h6>
        <p class="text-muted small">We value your time — guaranteed punctuality.</p>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="feature-box text-center">
        <i class="bi bi-cash-coin"></i>
        <h6 class="fw-bold">Affordable Pricing</h6>
        <p class="text-muted small">Quality water at the best market price.</p>
      </div>
    </div>

  </div>

  <!-- SERVICES -->
  <h3 class="fw-bold mb-3">Our Services</h3>
  <ul class="list-group mb-5">
    <li class="list-group-item">
      <i class="bi bi-bottle-water badge-icon"></i> Bottled Drinking Water Supply
    </li>
    <li class="list-group-item">
      <i class="bi bi-truck-front badge-icon"></i> Commercial Tanker Delivery
    </li>
    <li class="list-group-item">
      <i class="bi bi-calendar-check badge-icon"></i> Weekly / Monthly Subscriptions
    </li>
    <li class="list-group-item">
      <i class="bi bi-geo-alt badge-icon"></i> Real-time Order Tracking
    </li>
  </ul>

  <!-- MISSION & VISION -->
  <div class="row g-4 mb-5">
    <div class="col-md-6">
      <div class="feature-box">
        <h5 class="fw-bold"><i class="bi bi-bullseye"></i> Our Mission</h5>
        <p class="text-muted">
          To provide safe, clean, and affordable water to everyone — ensuring good health 
          and sustainable living in every household and business we serve.
        </p>
      </div>
    </div>

    <div class="col-md-6">
      <div class="feature-box">
        <h5 class="fw-bold"><i class="bi bi-eye-fill"></i> Our Vision</h5>
        <p class="text-muted">
          To become the most reliable and customer-loved water supplier, delivering 
          excellence through purity, service, and commitment.
        </p>
      </div>
    </div>
  </div>

</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>
