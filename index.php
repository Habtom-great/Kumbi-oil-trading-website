--- FILE: index.php ---
<?php
session_start();
// Simple CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function sanitize($s){
    return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
}

$success = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid session token. Please reload the page.';
    }

    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $company = sanitize($_POST['company'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (!$name) $errors[] = 'Name is required.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (!$message) $errors[] = 'Message is required.';

    if (empty($errors)) {
        // store lead to CSV (simple lightweight storage)
        $row = [date('c'), $name, $email, $company, $message];
        $f = fopen(__DIR__ . '/data/leads.csv', 'a');
        if ($f) {
            fputcsv($f, $row);
            fclose($f);
            $success = 'Thanks! Your message has been received. We will contact you shortly.';
            // clear POST values to avoid resubmission
            $_POST = [];
        } else {
            $errors[] = 'Failed to save your inquiry. Please try again later.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>FuelTradePro — Global Fuel Trading & Logistics</title>
  <meta name="description" content="FuelTradePro provides safe, reliable fuel trading, storage and logistics across continents.">
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
  <header class="site-header">
    <div class="container">
      <div class="brand">FuelTradePro</div>
      <nav class="nav">
        <a href="#services">Services</a>
        <a href="#fleet">Fleet</a>
        <a href="#clients">Clients</a>
        <a href="#contact">Contact</a>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="container hero-inner">
      <div class="hero-copy">
        <h1>Secure. Compliant. On time.</h1>
        <p>Worldwide fuel sourcing, storage and delivery solutions for retailers, airlines and industry.</p>
        <a class="cta" href="#contact">Request Quote</a>
      </div>
      <div class="hero-image" aria-hidden="true"></div>
    </div>
  </section>

  <main class="container">

    <section id="services" class="cards">
      <h2>Our Services</h2>
      <div class="card-grid">
        <article class="card">
          <h3>Trading & Procurement</h3>
          <p>Flexible sourcing across major markets, competitive pricing and hedging advisory.</p>
        </article>
        <article class="card">
          <h3>Logistics & Delivery</h3>
          <p>Trusted carriers, real‑time tracking and optimized delivery scheduling.</p>
        </article>
        <article class="card">
          <h3>Storage & Terminals</h3>
          <p>Network of bonded terminals, compliant tank farms and inventory management.</p>
        </article>
      </div>
    </section>

    <section id="fleet" class="section-simple">
      <h2>Our Fleet & Partners</h2>
      <p>We work with a vetted network of tankers, pipeline operators and terminal owners to ensure continuity of supply.</p>
      <ul class="stats">
        <li><strong>50+</strong> Global supplier partners</li>
        <li><strong>24/7</strong> Operations desk</li>
        <li><strong>100k+</strong> MT monthly capacity</li>
      </ul>
    </section>

    <section id="clients" class="section-simple">
      <h2>Trusted By</h2>
      <div class="clients-grid">
        <div class="client">Airline A</div>
        <div class="client">Retailer B</div>
        <div class="client">Distributor C</div>
        <div class="client">Refinery D</div>
      </div>
    </section>

    <section id="contact" class="contact">
      <h2>Contact Us</h2>

      <?php if ($success): ?>
        <div class="notice success"><?= $success ?></div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="notice error">
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?= $e ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" class="contact-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label>
          <span>Name</span>
          <input name="name" value="<?= sanitize($_POST['name'] ?? '') ?>" required>
        </label>
        <label>
          <span>Email</span>
          <input name="email" type="email" value="<?= sanitize($_POST['email'] ?? '') ?>" required>
        </label>
        <label>
          <span>Company</span>
          <input name="company" value="<?= sanitize($_POST['company'] ?? '') ?>">
        </label>
        <label>
          <span>Message</span>
          <textarea name="message" required><?= sanitize($_POST['message'] ?? '') ?></textarea>
        </label>
        <button name="contact_submit" class="btn">Send inquiry</button>
      </form>

    </section>

  </main>

  <footer class="site-footer">
    <div class="container">
      <div>© <?= date('Y') ?> FuelTradePro — All rights reserved.</div>
      <div class="small">Registered office • Compliance & Safety • Privacy Policy</div>
    </div>
  </footer>

  <script src="assets/scripts.js"></script>
</body>
</html>

--- FILE: assets/styles.css ---
/* Modern, minimal professional styling */
:root{
  --accent:#0b62d6;
  --muted:#6b7280;
  --bg:#f6f8fb;
  --card:#ffffff;
  --radius:12px;
  --maxw:1080px;
}
*{box-sizing:border-box}
body{font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; margin:0; background:var(--bg); color:#0f1724; line-height:1.5}
.container{max-width:var(--maxw); margin:0 auto; padding:28px}
.site-header{background:linear-gradient(90deg, rgba(11,98,214,0.05), rgba(11,98,214,0));}
.site-header .container{display:flex; align-items:center; justify-content:space-between}
.brand{font-weight:700; font-size:20px; color:var(--accent)}
.nav a{margin-left:18px; text-decoration:none; color:var(--muted)}
.hero{padding:48px 0}
.hero-inner{display:flex; gap:32px; align-items:center}
.hero-copy h1{font-size:40px; margin:0 0 12px}
.hero-copy p{color:var(--muted); margin:0 0 18px}
.cta{display:inline-block; background:var(--accent); color:#fff; padding:12px 20px; border-radius:10px; text-decoration:none}
.hero-image{flex:1; min-height:180px; background:linear-gradient(135deg,#e6f0ff, #f7fbff); border-radius:12px}
.cards h2, .section-simple h2, .contact h2{margin-top:0}
.card-grid{display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:18px}
.card{background:var(--card); padding:20px; border-radius:12px; box-shadow:0 6px 18px rgba(15,23,36,0.06)}
.stats{display:flex; gap:18px; list-style:none; padding:0}
.stats li{background:var(--card); padding:16px; border-radius:10px; flex:1; text-align:center}
.clients-grid{display:grid; grid-template-columns:repeat(auto-fit,minmax(120px,1fr)); gap:12px}
.client{background:#fff; padding:16px; border-radius:8px; text-align:center}
.contact-form{display:grid; gap:12px; max-width:720px}
.contact-form label{display:block}
.contact-form input, .contact-form textarea{width:100%; padding:10px; border-radius:8px; border:1px solid #e6eef8}
.btn{background:var(--accent); color:#fff; border:none; padding:12px 16px; border-radius:10px; cursor:pointer}
.notice{padding:12px; border-radius:10px}
.notice.success{background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.14)}
.notice.error{background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.12)}
.site-footer{padding:24px 0; color:var(--muted)}n
@media(max-width:800px){
  .hero-inner{flex-direction:column}
  .container{padding:18px}
}

--- FILE: assets/scripts.js ---
// Small interactive touches
document.addEventListener('DOMContentLoaded', function(){
  // Simple smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(a=>{
    a.addEventListener('click', e=>{
      const t = document.querySelector(a.getAttribute('href'));
      if (t){
        e.preventDefault();
        t.scrollIntoView({behavior:'smooth', block:'start'});
      }
    });
  });

  // Basic client-side validation hint
  const form = document.querySelector('.contact-form');
  if (form) form.addEventListener('submit', e=>{
    const email = form.querySelector('input[name=email]');
    if (email && !/^\S+@\S+\.\S+$/.test(email.value)){
      e.preventDefault();
      alert('Please enter a valid email address.');
      email.focus();
    }
  });
});

--- FILE: data/.gitignore ---
# prevent accidental upload of leads
leads.csv

--- FILE: README.md ---
# FuelTradePro — Starter PHP Website

Files included:
- `index.php` — main landing page + contact handling
- `assets/styles.css` — site styles
- `assets/scripts.js` — small interactivity
- `data/leads.csv` — generated when a contact is submitted (ensure `data/` is writable)

## Install
1. Copy files to a PHP-enabled webserver (PHP 7.4+ recommended).
2. Ensure `data/` folder exists and is writable by the webserver (e.g. `mkdir data && chmod 755 data`).
3. Open `index.php` in the browser.

## Notes & Next steps
- Replace placeholder client logos & text with your real assets.
- Integrate SMTP or CRM webhook for production contact handling.
- Add HTTPS and an actual domain for production deployment.
- Consider adding i18n, cookie policy, and accessibility improvements.

---
