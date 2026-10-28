<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/data.php';

if (session_status() !== PHP_SESSION_ACTIVE && !session_start()) {
    throw new RuntimeException('Unable to start a session for the messages page.');
}

if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];
$isAuthenticated = ($_SESSION['messages_authenticated'] ?? false) === true;
$passwordConfigured = $messagesPagePassword !== '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($postedToken) || !hash_equals($csrfToken, $postedToken)) {
        $errors[] = 'Your form session has expired. Please reload the page and try again.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($isAuthenticated && $action === 'logout') {
            unset($_SESSION['messages_authenticated']);
            header('Location: messages.php', true, 303);
            exit;
        }

        $postedPassword = $_POST['password'] ?? '';
        if (!$passwordConfigured) {
            $errors[] = 'The messages-page password is not configured. Follow the local setup instructions in README.md.';
        } elseif (!$isAuthenticated && $action === 'login' && is_string($postedPassword)) {
            if (hash_equals($messagesPagePassword, $postedPassword)) {
                if (!session_regenerate_id(true)) {
                    error_log('Messages page could not regenerate the session ID after login.');
                    $errors[] = 'Login could not be completed. Please try again.';
                } else {
                    $_SESSION['messages_authenticated'] = true;
                    header('Location: messages.php', true, 303);
                    exit;
                }
            } else {
                $errors[] = 'That password was not correct. Please try again.';
            }
        } elseif (!$isAuthenticated && $action !== 'login') {
            $errors[] = 'Please use the login form to access submissions.';
        }
    }

    $isAuthenticated = ($_SESSION['messages_authenticated'] ?? false) === true;
}

$messages = [];
$storageError = '';

if ($isAuthenticated) {
    $messagesFile = __DIR__ . '/storage/messages.jsonl';
    if (is_file($messagesFile)) {
        $contents = file_get_contents($messagesFile);
        if ($contents === false) {
            error_log('Messages page could not read storage/messages.jsonl.');
            $storageError = 'Submissions could not be loaded. Check the storage file permissions.';
        } else {
            $serviceTitles = [];
            foreach ($services as $service) {
                $serviceTitles[$service['slug']] = $service['title'];
            }

            foreach (explode("\n", $contents) as $lineNumber => $line) {
                if (trim($line) === '') {
                    continue;
                }

                $record = json_decode($line, true);
                $requiredStringFields = ['submitted_at', 'name', 'email', 'phone', 'service', 'message'];
                $validRecord = is_array($record);
                foreach ($requiredStringFields as $field) {
                    if (!is_array($record) || !isset($record[$field]) || !is_string($record[$field])) {
                        $validRecord = false;
                        break;
                    }
                }

                if (!$validRecord) {
                    error_log('Messages page skipped an invalid JSON record on line ' . ($lineNumber + 1) . '.');
                    continue;
                }

                $timestamp = strtotime($record['submitted_at']);
                if ($timestamp === false) {
                    error_log('Messages page skipped a record with an invalid timestamp on line ' . ($lineNumber + 1) . '.');
                    continue;
                }

                $record['timestamp'] = $timestamp;
                $record['service_title'] = $serviceTitles[$record['service']] ?? $record['service'];
                $messages[] = $record;
            }

            usort(
                $messages,
                static fn (array $first, array $second): int => $second['timestamp'] <=> $first['timestamp']
            );
        }
    }
}

$pageTitle = 'Messages';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-heading">
    <p class="eyebrow">Local admin page</p>
    <h1>Contact <span>messages.</span></h1>
    <p>Review submissions sent through the contact form.</p>
</section>

<?php foreach ($errors as $error): ?>
    <p class="form-alert form-alert-error" role="alert"><?= e($error) ?></p>
<?php endforeach; ?>

<?php if (!$isAuthenticated && $passwordConfigured): ?>
    <section class="messages-login">
        <h2>Enter the local page password</h2>
        <form class="contact-form" method="post" action="messages.php">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <input type="hidden" name="action" value="login">
            <div class="form-field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button class="button" type="submit">View messages</button>
        </form>
    </section>
<?php elseif (!$isAuthenticated): ?>
    <p class="form-alert form-alert-error" role="alert">The messages-page password is not configured. Follow the local setup instructions in README.md.</p>
<?php else: ?>
    <section class="messages-section">
        <div class="messages-heading">
            <p class="messages-count"><?= count($messages) ?> <?= count($messages) === 1 ? 'message' : 'messages' ?></p>
            <form method="post" action="messages.php">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="action" value="logout">
                <button class="button button-secondary" type="submit">Log out</button>
            </form>
        </div>

        <?php if ($storageError !== ''): ?>
            <p class="form-alert form-alert-error" role="alert"><?= e($storageError) ?></p>
        <?php elseif ($messages === []): ?>
            <p class="empty-state">There are no contact submissions yet.</p>
        <?php else: ?>
            <div class="table-scroll">
                <table class="messages-table">
                    <thead>
                        <tr>
                            <th scope="col">Received</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Service</th>
                            <th scope="col">Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <tr>
                                <td><?= e($message['submitted_at']) ?></td>
                                <td><?= e($message['name']) ?></td>
                                <td><?= e($message['email']) ?></td>
                                <td><?= e($message['phone'] !== '' ? $message['phone'] : '—') ?></td>
                                <td><?= e($message['service_title']) ?></td>
                                <td class="message-cell"><?= e($message['message']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
