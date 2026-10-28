<?php

$pageTitle = 'Home';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">A cleaner space starts here</p>
        <h1>Come home to <span>clean.</span></h1>
        <p class="hero-intro">Reliable, thoughtful cleaning for busy homes and hard-working businesses. We take care of the details, so you can enjoy the difference.</p>
        <a class="button" href="services.php">Explore our services</a>
    </div>
    <div class="hero-note" aria-label="Our promise">
        <span class="note-icon" aria-hidden="true">✦</span>
        <p>Care in every corner.<br><strong>That’s our promise.</strong></p>
    </div>
</section>

<section class="welcome section-block">
    <p class="eyebrow">Welcome to Prime Cleaners</p>
    <h2>More time for what matters.</h2>
    <p>From a weekly home refresh to a one-off deep clean, our friendly local team makes it easy to keep your space looking its best.</p>
    <a class="text-link" href="about.php">Get to know us <span aria-hidden="true">→</span></a>
</section>

<section class="feature-grid" aria-label="Why choose Prime Cleaners">
    <article class="feature-card">
        <span class="feature-number">01</span>
        <h3>People you can trust</h3>
        <p>Our dependable team treats your home and workplace with care and respect.</p>
    </article>
    <article class="feature-card">
        <span class="feature-number">02</span>
        <h3>Cleaning that fits</h3>
        <p>Choose the service and schedule that works best for your space and routine.</p>
    </article>
    <article class="feature-card">
        <span class="feature-number">03</span>
        <h3>A little extra sparkle</h3>
        <p>We focus on the finishing touches that make a clean space feel wonderful.</p>
    </article>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
