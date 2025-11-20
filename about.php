<?php
require_once 'init.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>About - Vetriarasiwatersupply</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
  body { 
    background: #f4f7fb; 
    font-family: "Poppins", sans-serif; 
    color: #444;
  }

  /* Banner with subtle shadow & smooth gradient */
  .about-banner {
    background: linear-gradient(135deg, #0A84FF 0%, #074a74 100%);
    color: white; 
    padding: 60px 30px;
    border-radius: 22px; 
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
    transition: box-shadow 0.3s ease;
  }
  .about-banner:hover {
    box-shadow: 0 18px 40px rgba(0,0,0,0.3);
  }

  /* Feature box style with lifted hover effect */
  .feature-box {
    background: white; 
    padding: 30px 25px;
    border-radius: 16px;
    box-shadow: 0 7px 20px rgba(0,0,0,0.12);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .feature-box:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.18);
  }
  .feature-box i { 
    font-size: 2.8rem; 
    color: #0A84FF; 
    margin-bottom: 15px; 
    transition: color 0.3s ease;
  }
  .feature-box:hover i {
    color: #074a74;
  }
  .feature-box h6 {
    font-weight: 700;
    margin-bottom: 8px;
  }
  .feature-box p {
    color: #666;
  }

  /* Highlight card with vibrant shadow and smooth corner */
  .highlight-card {
    background: #0A84FF;
    border-radius: 20px; 
    color:white;
    padding: 35px 25px;
    text-align: center;
    box-shadow: 0 14px 40px rgba(10,132,255,0.35);
    transition: box-shadow 0.3s ease;
  }
  .highlight-card:hover {
    box-shadow: 0 18px 55px rgba(10,132,255,0.45);
  }
  .highlight-card i {
    font-size: 3.4rem;
    margin-bottom: 15px;
    transition: transform 0.3s ease;
  }
  .highlight-card:hover i {
    transform: scale(1.1);
  }

  /* Badge icon bigger & brighter */
  .badge-icon {
    font-size: 2.4rem; 
    color:#0A84FF;
    margin-right: 12px;
  }

  /* Section heading */
  h3.fw-bold {
    position: relative;
    padding-bottom: 12px;
    margin-bottom: 40px;
    font-weight: 800;
    color: #0a4f9d;
  }
  h3.fw-bold::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      width: 65px;
      height: 4px;
      background: #0A84FF;
      border-radius: 4px;
  }

  /* List group items spaced and icon aligned */
  ul.list-group {
    max-width: 540px;
    margin: auto;
  }
  ul.list-group li.list-group-item {
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 1.1rem;
    color: #333;
    border-radius: 12px;
    margin-bottom: 10px;
    padding: 18px 22px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
    transition: background-color 0.3s ease;
    background: white;
  }
  ul.list-group li.list-group-item:hover {
    background-color: #e8f0fd;
  }
  ul.list-group li i.badge-icon {
    margin-right: 18px;
  }

  /* Responsive tweaks */
  @media (max-width: 768px) {
    .about-banner {
      padding: 40px 20px;
    }
    .highlight-card {
      padding: 25px 20px;
    }
  }

</style>

</head>
<body>
<?php include __DIR__ . '/templates/header.php'; ?>

<div class="container py-5">

  <!-- HEADER BANNER -->
  <div class="about-banner mb-5 shadow">
    <h2 class="fw-bold">About Vetriarasiwatersupply</h2>
    <p class="mt-3 mb-0 fs-5">
      Delivering purity, trust, and reliable water solutions since day one.
      <br>Your hydration is our responsibility, and we take it seriously.
    </p>
  </div>

  <!-- OUR STORY -->
  <div class="mb-5">
    <h3 class="fw-bold">Our Story</h3>
    <p class="lead text-secondary" style="max-width:750px;">
      Vetriarasi Water Supply was founded with a vision to make clean and affordable water 
      accessible to every home and business. With a proven track record of timely delivery, 
      strict hygiene standards, and transparent service, we have become one of the most 
      trusted suppliers in the region.
    </p>
  </div>

  <!-- HIGHLIGHT CARDS -->
  <div class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="highlight-card shadow">
        <i class="bi bi-shield-check"></i>
        <h4 class="fw-bold">100% Pure & Tested</h4>
        <p>Every drop is quality-checked and compliant with FSSAI standards.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="highlight-card shadow">
        <i class="bi bi-truck"></i>
        <h4 class="fw-bold">Fast & Reliable Delivery</h4>
        <p>Our fleet ensures timely deliveries — always on schedule.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="highlight-card shadow">
        <i class="bi bi-people-fill"></i>
        <h4 class="fw-bold">200+ Happy Customers</h4>
        <p>We are proud to serve households, shops, offices & industries.</p>
      </div>
    </div>
  </div>

  <!-- WHY CHOOSE US -->
  <h3 class="fw-bold">Why Choose Us?</h3>
  <div class="row g-4 mb-5">
    <div class="col-md-3 col-sm-6">
      <div class="feature-box text-center shadow">
        <i class="bi bi-trophy-fill"></i>
        <h6 class="fw-bold mt-3">Trusted Brand</h6>
        <p class="text-muted small px-2">Known for honest pricing & clean supply.</p>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="feature-box text-center shadow">
        <i class="bi bi-droplet"></i>
        <h6 class="fw-bold mt-3">Hygienic & Safe</h6>
        <p class="text-muted small px-2">Handled with strict hygiene procedures.</p>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="feature-box text-center shadow">
        <i class="bi bi-clock-history"></i>
        <h6 class="fw-bold mt-3">On-Time Delivery</h6>
        <p class="text-muted small px-2">We value your time — guaranteed punctuality.</p>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="feature-box text-center shadow">
        <i class="bi bi-cash-coin"></i>
        <h6 class="fw-bold mt-3">Affordable Pricing</h6>
        <p class="text-muted small px-2">Quality water at the best market price.</p>
      </div>
    </div>
  </div>

  <!-- SERVICES -->
  <h3 class="fw-bold mb-3">Our Services</h3>
  <ul class="list-group mb-5 mx-auto">
   <li class="list-group-item"><i class="bi bi-water badge-icon"></i> Bottled Drinking Water Supply</li>
    <li class="list-group-item"><i class="bi bi-truck-front badge-icon"></i> Commercial Tanker Delivery</li>
    <li class="list-group-item"><i class="bi bi-calendar-check badge-icon"></i> Weekly / Monthly Subscriptions</li>
    <li class="list-group-item"><i class="bi bi-geo-alt badge-icon"></i> Real-time Order Tracking</li>
  </ul>

  <!-- MISSION & VISION -->
  <div class="row g-4 mb-5">
    <div class="col-md-6">
      <div class="feature-box shadow">
        <h5 class="fw-bold"><i class="bi bi-bullseye"></i> Our Mission</h5>
        <p class="text-muted px-2">
          To provide safe, clean, and affordable water to everyone — ensuring good health 
          and sustainable living in every household and business we serve.
        </p>
      </div>
    </div>

    <div class="col-md-6">
      <div class="feature-box shadow">
        <h5 class="fw-bold"><i class="bi bi-eye-fill"></i> Our Vision</h5>
        <p class="text-muted px-2">
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
