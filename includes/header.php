<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($tagline) ?>">
    <title><?= e($pageTitle) ?> | <?= e($siteName) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-content">
            <a class="brand" href="index.php">
                <span class="brand-name"><?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?></span>
                <span class="brand-tagline"><?= htmlspecialchars($tagline, ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <nav aria-label="Main navigation">
                <ul class="nav-list">
                    <?php foreach ($navItems as $item): ?>
                        <?php
                        $currentPage = basename($_SERVER['PHP_SELF']);
                        $isCurrentPage = $currentPage === $item['file']
                            || ($currentPage === 'service.php' && $item['file'] === 'services.php');
                        ?>
                        <li>
                            <a
                                href="<?= e($item['file']) ?>"
                                <?= $isCurrentPage ? 'class="active" aria-current="page"' : '' ?>
                            ><?= e($item['label']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container page-content">
