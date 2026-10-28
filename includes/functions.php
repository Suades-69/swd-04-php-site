<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatPrice(float $amount): string
{
    return '₦' . number_format($amount, 0);
}

function renderServiceCard(array $service): string
{
    $slug = rawurlencode($service['slug']);

    return '<article class="service-card">'
        . '<span class="service-icon" aria-hidden="true">' . e($service['icon']) . '</span>'
        . '<h2>' . e($service['title']) . '</h2>'
        . '<p>' . e($service['description']) . '</p>'
        . '<p class="service-price">From ' . e(formatPrice($service['price'])) . ' per visit</p>'
        . '<a class="text-link" href="service.php?slug=' . e($slug) . '">View service details <span aria-hidden="true">→</span></a>'
        . '</article>';
}
