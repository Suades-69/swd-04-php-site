<?php

$siteName = 'Prime Cleaners Ltd';
$tagline = 'A fresher home, with less effort.';
$contactEmail = 'hello@primecleaners.example';

$passwordFromEnvironment = getenv('MESSAGES_PAGE_PASSWORD');
$localConfigPath = __DIR__ . '/local-config.php';
$localConfig = is_file($localConfigPath) ? require $localConfigPath : [];
$localPassword = is_array($localConfig) ? ($localConfig['messagesPagePassword'] ?? '') : '';
$messagesPagePassword = is_string($passwordFromEnvironment) && $passwordFromEnvironment !== ''
    ? $passwordFromEnvironment
    : (is_string($localPassword) ? $localPassword : '');

$navItems = [
    ['label' => 'Home', 'file' => 'index.php'],
    ['label' => 'About', 'file' => 'about.php'],
    ['label' => 'Services', 'file' => 'services.php'],
    ['label' => 'Contact', 'file' => 'contact.php'],
];
