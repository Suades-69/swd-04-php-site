<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/data.php';

if (session_status() !== PHP_SESSION_ACTIVE && !session_start()) {
    throw new RuntimeException('Unable to start a session for the contact form.');
}

if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];
$formData = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'service' => '',
    'message' => '',
];
$errors = [];
$isSubmitted = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($isSubmitted) {
    foreach ($formData as $field => $defaultValue) {
        $postedValue = $_POST[$field] ?? '';
        $formData[$field] = is_string($postedValue) ? trim($postedValue) : '';
    }

    $postedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($postedToken) || !hash_equals($csrfToken, $postedToken)) {
        $errors['csrf'] = 'Your form session has expired. Please try again.';
    }

    if ($formData['name'] === '') {
        $errors['name'] = 'Please enter your name.';
    }

    if ($formData['email'] === '') {
        $errors['email'] = 'Please enter your email address.';
    } elseif (filter_var($formData['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    $validServiceSlugs = array_column($services, 'slug');
    if (!in_array($formData['service'], $validServiceSlugs, true)) {
        $errors['service'] = 'Please choose one of our services.';
    }

    $messageLength = mb_strlen($formData['message'], 'UTF-8');
    if ($formData['message'] === '') {
        $errors['message'] = 'Please enter a message.';
    } elseif ($messageLength < 10 || $messageLength > 1000) {
        $errors['message'] = 'Your message must be between 10 and 1000 characters.';
    }

    if ($errors === []) {
        $storageDirectory = __DIR__ . '/storage';
        if (!is_dir($storageDirectory) && !mkdir($storageDirectory, 0750, true) && !is_dir($storageDirectory)) {
            error_log('Contact form could not create the storage directory.');
            $errors['form'] = 'We could not save your message right now. Please try again later.';
        } else {
            $submission = [
                'submitted_at' => date(DATE_ATOM),
                'name' => $formData['name'],
                'email' => $formData['email'],
                'phone' => $formData['phone'],
                'service' => $formData['service'],
                'message' => $formData['message'],
            ];
            $jsonLine = json_encode($submission, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($jsonLine === false) {
                error_log('Contact form submission could not be encoded as JSON: ' . json_last_error_msg());
                $errors['form'] = 'We could not save your message right now. Please try again later.';
            } else {
                $writtenBytes = file_put_contents(
                    $storageDirectory . '/messages.jsonl',
                    $jsonLine . PHP_EOL,
                    FILE_APPEND | LOCK_EX
                );

                if ($writtenBytes === false) {
                    error_log('Contact form submission could not be written to storage/messages.jsonl.');
                    $errors['form'] = 'We could not save your message right now. Please try again later.';
                } else {
                    header('Location: contact.php?sent=1', true, 303);
                    exit;
                }
            }
        }
    }
}

$pageTitle = 'Contact';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-heading">
    <p class="eyebrow">We’d love to hear from you</p>
    <h1>Let’s make your space <span>shine.</span></h1>
    <p>Tell us a little about what you need and we’ll be happy to help you choose a service.</p>
</section>

<?php if (isset($_GET['sent']) && $_GET['sent'] === '1'): ?>
    <p class="form-alert form-alert-success" role="status">Thanks for getting in touch. Your message has been sent.</p>
<?php endif; ?>

<section class="contact-layout">
    <div class="contact-card">
        <p class="eyebrow">Get in touch</p>
        <h2>Send us a message.</h2>
        <p>Complete the form and our friendly team will get back to you.</p>

        <?php if (isset($errors['csrf'])): ?>
            <p class="form-alert form-alert-error" role="alert"><?= e($errors['csrf']) ?></p>
        <?php endif; ?>
        <?php if (isset($errors['form'])): ?>
            <p class="form-alert form-alert-error" role="alert"><?= e($errors['form']) ?></p>
        <?php endif; ?>

        <form class="contact-form" method="post" action="contact.php">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

            <div class="form-field">
                <label for="name">Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    autocomplete="name"
                    value="<?= e($formData['name']) ?>"
                    required
                    <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="name-error"' : '' ?>
                >
                <?php if (isset($errors['name'])): ?>
                    <p class="field-error" id="name-error"><?= e($errors['name']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-field">
                <label for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    value="<?= e($formData['email']) ?>"
                    required
                    <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?>
                >
                <?php if (isset($errors['email'])): ?>
                    <p class="field-error" id="email-error"><?= e($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-field">
                <label for="phone">Phone <span class="optional-label">(optional)</span></label>
                <input
                    id="phone"
                    name="phone"
                    type="tel"
                    autocomplete="tel"
                    value="<?= e($formData['phone']) ?>"
                    <?= isset($errors['phone']) ? 'aria-invalid="true" aria-describedby="phone-error"' : '' ?>
                >
                <?php if (isset($errors['phone'])): ?>
                    <p class="field-error" id="phone-error"><?= e($errors['phone']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-field">
                <label for="service">Service</label>
                <select
                    id="service"
                    name="service"
                    required
                    <?= isset($errors['service']) ? 'aria-invalid="true" aria-describedby="service-error"' : '' ?>
                >
                    <option value="">Choose a service</option>
                    <?php foreach ($services as $service): ?>
                        <option
                            value="<?= e($service['slug']) ?>"
                            <?= $formData['service'] === $service['slug'] ? 'selected' : '' ?>
                        ><?= e($service['title']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['service'])): ?>
                    <p class="field-error" id="service-error"><?= e($errors['service']) ?></p>
                <?php endif; ?>
            </div>

            <div class="form-field">
                <label for="message">Message</label>
                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    required
                    aria-describedby="<?= isset($errors['message']) ? 'message-hint message-error' : 'message-hint' ?>"
                    <?= isset($errors['message']) ? 'aria-invalid="true"' : '' ?>
                ><?= e($formData['message']) ?></textarea>
                <p class="field-hint" id="message-hint">Please use 10 to 1000 characters.</p>
                <?php if (isset($errors['message'])): ?>
                    <p class="field-error" id="message-error"><?= e($errors['message']) ?></p>
                <?php endif; ?>
            </div>

            <button class="button" type="submit">Send message</button>
        </form>

        <p class="small-note">You can also email us at <a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a>.</p>
    </div>
    <div class="contact-aside">
        <span class="note-icon" aria-hidden="true">✦</span>
        <h2>A clean space is a happy place.</h2>
        <p>Home, office, or a little of both — we’ll help you find a service that fits.</p>
        <a class="text-link" href="services.php">See our services <span aria-hidden="true">→</span></a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
