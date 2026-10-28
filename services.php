<?php

declare(strict_types=1);

$pageTitle = 'Services';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-heading">
    <p class="eyebrow">How we can help</p>
    <h1>Cleaning to suit <span>your space.</span></h1>
    <p>Choose a regular visit or book a one-off refresh. We’ll help you find the right fit.</p>
</section>

<section class="service-grid">
    <?php foreach ($services as $service): ?>
        <?= renderServiceCard($service) ?>
    <?php endforeach; ?>
</section>

<section class="callout">
    <div>
        <p class="eyebrow">Ready for a fresh start?</p>
        <h2>Let’s find the right clean for you.</h2>
    </div>
    <a class="button button-light" href="contact.php">Get in touch</a>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
