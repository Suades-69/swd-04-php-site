<?php

declare(strict_types=1);

$pageTitle = 'About';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-heading">
    <p class="eyebrow">A little about us</p>
    <h1>Good people. <span>Great clean.</span></h1>
    <p>We believe a clean, comfortable space can make everyday life feel brighter.</p>
</section>

<section class="content-panel">
    <div>
        <p class="eyebrow">Our story</p>
        <h2>Care is at the heart of everything we do.</h2>
    </div>
    <div class="prose">
        <p>Prime Cleaners Ltd is a fictional local cleaning company built around a simple idea: dependable service should feel personal. We listen to what matters to you, pay attention to the details, and leave every space feeling refreshed.</p>
        <p>Whether we’re helping a family reclaim a little free time or making a workplace more welcoming, we bring the same friendly approach and pride in our work.</p>
        <a class="text-link" href="contact.php">Say hello <span aria-hidden="true">→</span></a>
    </div>
</section>

<section class="team-section">
    <div class="section-heading">
        <p class="eyebrow">Meet our team</p>
        <h2>Friendly faces, thoughtful care.</h2>
    </div>
    <div class="team-grid">
        <?php foreach ($team as $member): ?>
            <article class="team-card">
                <h3><?= e($member['name']) ?></h3>
                <p class="team-role"><?= e($member['role']) ?></p>
                <p><?= e($member['bio']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
