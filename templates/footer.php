<footer class="footer-modern">
  <div class="footer-container">

    <div class="footer-grid">

      <!-- Brand -->
      <div>
        <div class="footer-brand">
          <img src="<?php echo $base_url; ?>/assets/images/logo.png" alt="Vetriarasi Logo" class="footer-logo">
          <span>Vetriarasiwatersupply</span>
        </div>

        <p class="footer-text">
          No. 14, Babu Nagar,<br>
          Jolarpet – 635851,<br>
          Tirupattur, Tamil Nadu, India
        </p>
      </div>

      <!-- Contact -->
      <div>
        <h4 class="footer-heading">Contact Us</h4>
        <p class="footer-text">
          <strong>Phone:</strong><br>
          +91 9360658623
        </p>
        <p class="footer-text">
          <strong>Email:</strong><br>
          <a href="mailto:vetriarasiwatersupply@gmail.com" class="footer-link">
            vetriarasiwatersupply@gmail.com
          </a>
        </p>
      </div>

      <!-- Quick Links -->
      <div>
        <h4 class="footer-heading">Quick Links</h4>
        <ul class="footer-links">
          <li><a href="<?php echo $base_url; ?>">Shop</a></li>
          <li><a href="<?php echo $base_url; ?>/profile.php">My Profile</a></li>
          <li><a href="<?php echo $base_url; ?>/cart.php">Shopping Cart</a></li>
          <li><a href="<?php echo $base_url; ?>/track.php">Track Order</a></li>
        </ul>
      </div>

      <!-- About -->
      <div>
        <h4 class="footer-heading">About Us</h4>
        <p class="footer-text">
          Delivering premium-quality water solutions with reliable delivery,
          trusted service, and a commitment to customer satisfaction.
        </p>
      </div>

    </div>

    <div class="footer-bottom">
      <p>© <?php echo date('Y'); ?> Vetriarasiwatersupply — All Rights Reserved</p>
      <span class="footer-tagline">Clean. Reliable. Delivered.</span>
    </div>

  </div>
</footer>

<!-- Bootstrap bundle (JS) for dropdowns, collapse etc -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
  .footer-modern {
    background: linear-gradient(135deg, #2e2e32, #1e1e22);
    padding: 70px 0 40px;
    margin-top: 60px;
    border-top: 1px solid rgba(255,255,255,0.05);
    font-family: 'Inter', sans-serif;
  }

  .footer-container {
    width: 90%;
    margin: auto;
    max-width: 1350px;
  }

  .footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 50px;
    margin-bottom: 50px;
  }

  /* Brand */
  .footer-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.25rem;
    font-weight: 700;
    color: #4cc2ee;
    letter-spacing: 0.5px;
    text-shadow: 0 0 8px rgba(76,194,238,0.25);
  }

  .footer-logo {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    filter: drop-shadow(0px 0px 8px rgba(34, 197, 94, 0.4));
    transition: transform 0.3s ease;
  }

  .footer-logo:hover {
    transform: scale(1.08);
  }

  /* Headings */
  .footer-heading {
    font-size: 1.05rem;
    font-weight: 700;
    color: #22c55e;
    margin-bottom: 18px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
  }

  /* Text */
  .footer-text {
    color: #d1d5db;
    font-size: 0.92rem;
    line-height: 1.7;
    margin-bottom: 12px;
  }

  /* Links */
  .footer-links {
    list-style: none;
    padding: 0;
  }

  .footer-links li {
    margin-bottom: 12px;
  }

  .footer-links a,
  .footer-link {
    text-decoration: none;
    color: #d1d5db;
    font-size: 0.92rem;
    transition: all 0.3s ease;
    display: inline-block;
  }

  .footer-links a:hover,
  .footer-link:hover {
    color: #4cc2ee;
    transform: translateX(6px);
  }

  /* Bottom Bar */
  .footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 28px;
    text-align: center;
    color: #a5b4c3;
    font-size: 0.88rem;
    letter-spacing: 0.3px;
  }

  .footer-tagline {
    font-size: 0.85rem;
    display: block;
    margin-top: 6px;
    opacity: 0.9;
    font-weight: 500;
  }

  /* Hover card effect */
  .footer-grid > div {
    transition: transform 0.35s ease, background 0.35s ease;
    padding: 5px;
    border-radius: 10px;
  }

  .footer-grid > div:hover {
    transform: translateY(-6px);
    background: rgba(255,255,255,0.03);
  }
</style>
