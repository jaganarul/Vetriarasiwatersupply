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
          Jolarpet – 638052,<br>
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
          <li><a href="<?php echo $base_url; ?>/index.php">Shop</a></li>
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
      <p>© <?php echo date('Y'); ?> Vetriarasi Water Supply — All Rights Reserved</p>
      <span class="footer-tagline">Clean. Reliable. Delivered.</span>
    </div>

  </div>
</footer>


<style>
  .footer-modern {
    background: #0a0d14;
    padding: 60px 0 35px;
    margin-top: 40px;
    border-top: 1px solid rgba(255,255,255,0.05);
    font-family: 'Inter', sans-serif;
  }

  .footer-container {
    width: 90%;
    margin: auto;
    max-width: 1300px;
  }

  .footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 40px;
    margin-bottom: 40px;
  }

  /* Brand */
  .footer-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.15rem;
    font-weight: 600;
    color: #22c55e;
    letter-spacing: 0.4px;
  }

  .footer-logo {
    width: 34px;
    height: 34px;
    filter: drop-shadow(0px 0px 6px rgba(34, 197, 94, 0.4));
  }

  /* Headings */
  .footer-heading {
    font-size: 1rem;
    font-weight: 600;
    color: #22c55e;
    margin-bottom: 15px;
  }

  /* Text */
  .footer-text {
    color: #cbd5e1;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 12px;
  }

  /* Links */
  .footer-links {
    list-style: none;
    padding: 0;
  }

  .footer-links li {
    margin-bottom: 10px;
  }

  .footer-links a,
  .footer-link {
    text-decoration: none;
    color: #cbd5e1;
    font-size: 0.9rem;
    transition: all 0.25s ease;
  }

  .footer-links a:hover,
  .footer-link:hover {
    color: #22c55e;
    padding-left: 5px;
  }

  /* Bottom Bar */
  .footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.08);
    padding-top: 25px;
    text-align: center;
    color: #94a3b8;
    font-size: 0.85rem;
  }

  .footer-tagline {
    font-size: 0.82rem;
    display: block;
    margin-top: 6px;
    opacity: 0.85;
  }
</style>
