<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/data.php';

$requestedSlug = $_GET['slug'] ?? '';
$service = null;

if (is_string($requestedSlug)) {
    foreach ($services as $availableService) {
        if ($availableService['slug'] === $requestedSlug) {
            $service = $availableService;
            break;
        }
    }
}

if ($service === null) {
    http_response_code(404);
}

$pageTitle = $service['title'] ?? 'Service not found';
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($service !== null): ?>
    <section class="page-heading">
        <p class="eyebrow">Our services</p>
        <h1><?= e($service['title']) ?></h1>
        <p><?= e($service['description']) ?></p>
    </section>

    <article class="service-detail">
        <span class="service-icon" aria-hidden="true"><?= e($service['icon']) ?></span>
        <p class="eyebrow">Service details</p>
        <h2><?= e($service['title']) ?></h2>
        <p><?= e($service['description']) ?></p>
        <p class="service-price">From <?= e(formatPrice($service['price'])) ?> per visit</p>
        <a class="button" href="contact.php">Ask us about this service</a>
    </article>
<?php else: ?>
    <section class="not-found">
        <p class="eyebrow">Service not found</p>
        <h1>We couldn’t find that service.</h1>
        <p>It may have moved or the link may be incorrect. Browse our services to see what’s available.</p>
        <a class="button" href="services.php">Browse services</a>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
