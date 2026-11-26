<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Get in Touch';
$activePage = 'contact';
$errors = [];
$success = false;
$name = '';
$email = '';
$subject = '';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    if (!csrf_is_valid()) {
        $errors[] = 'Your session expired. Please refresh the page and try again.';
    }
    if ($name === '' || mb_strlen($name) > 120) {
        $errors[] = 'Enter your name (up to 120 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
        $errors[] = 'Enter a valid email address.';
    }
    if ($subject === '' || mb_strlen($subject) > 180) {
        $errors[] = 'Enter a subject (up to 180 characters).';
    }
    if ($message === '' || mb_strlen($message) > 5000) {
        $errors[] = 'Enter a message (up to 5,000 characters).';
    }
    if (!$errors) {
        $statement = $pdo->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
        try {
            $statement->execute([$name, $email, $subject, $message]);
            $success = true;
            $name = $email = $subject = $message = '';
        } catch (PDOException $exception) {
            error_log('Travel contact message save failed: ' . $exception->getMessage());
            $errors[] = 'We could not send your message right now. Please try again.';
        }
    }
}
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero page-hero-contact">
    <div class="wrap page-hero-content"><span class="eyebrow eyebrow-light">A HUMAN ON THE OTHER SIDE</span><h1>Tell us what<br>you’re <em>dreaming of.</em></h1><p>Big plans, little questions, half-formed ideas. We’d love to hear them all.</p></div>
</section>
<section class="section-pad wrap contact-layout">
    <div class="contact-copy"><span class="eyebrow">LET'S TALK</span><h2>Every great trip<br>starts with a <em>hello.</em></h2><p>Share your thoughts with us and a real person from our travel team will get back to you soon.</p><div class="contact-detail"><span class="contact-icon">✳</span><div><strong>Thoughtful advice, always</strong><small>Tell us what matters most to you.</small></div></div><div class="contact-detail"><span class="contact-icon">↗</span><div><strong>Made for your kind of travel</strong><small>We’ll help turn your idea into a plan.</small></div></div></div>
    <div class="form-panel">
        <span class="eyebrow">SEND A NOTE</span><h2>We’re listening.</h2>
        <?php if ($success): ?><div class="alert alert-success" role="status">Thanks for reaching out. Your message is with our travel team now.</div><?php endif; ?>
        <?php if ($errors): ?><div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" class="form-grid" data-validate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label class="field field-full">Your name<input type="text" name="name" autocomplete="name" maxlength="120" required value="<?= e($name) ?>" placeholder="Your name"></label><label class="field field-full">Email address<input type="email" name="email" autocomplete="email" maxlength="190" required value="<?= e($email) ?>" placeholder="you@example.com"></label><label class="field field-full">Subject<input type="text" name="subject" maxlength="180" required value="<?= e($subject) ?>" placeholder="What’s on your mind?"></label><label class="field field-full">Your message<textarea name="message" rows="5" maxlength="5000" required placeholder="Tell us a little more..."><?= e($message) ?></textarea></label><button class="button button-dark field-full" type="submit">Send your note <span>↗</span></button></form>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
