<?php
require_once __DIR__ . '/site.php';
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
    if ($name === '' || strlen($name) > 120) $errors[] = 'Enter your name (up to 120 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) $errors[] = 'Enter a valid email address.';
    if ($subject === '' || strlen($subject) > 180) $errors[] = 'Enter a subject (up to 180 characters).';
    if ($message === '' || strlen($message) > 5000) $errors[] = 'Enter a message (up to 5,000 characters).';
    if (!$errors) $success = true;
}
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero page-hero-contact">
    <div class="wrap page-hero-content"><span class="eyebrow eyebrow-light">A HUMAN ON THE OTHER SIDE</span><h1>Tell us what<br>you’re <em>dreaming of.</em></h1><p>Big plans, little questions, half-formed ideas. We’d love to hear them all.</p></div>
</section>
<section class="section-pad wrap contact-layout">
    <div class="contact-copy"><span class="eyebrow">LET'S TALK</span><h2>Every great trip<br>starts with a <em>hello.</em></h2><p>Share your thoughts and explore the site’s frontend-only contact demo. Your message will not be sent or stored.</p><div class="contact-detail"><span class="contact-icon">✳</span><div><strong>Thoughtful advice, always</strong><small>Tell us what matters most to you.</small></div></div><div class="contact-detail"><span class="contact-icon">↗</span><div><strong>Made for your kind of travel</strong><small>Turn your idea into a plan.</small></div></div></div>
    <div class="form-panel">
        <span class="eyebrow">SEND A NOTE</span><h2>We’re listening.</h2>
        <?php if ($success): ?><div class="alert alert-success" role="status">Thanks, <?= e($name) ?>. Your message looks ready, but this frontend-only demo does not send or save it.</div><?php endif; ?>
        <?php if ($errors): ?><div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" class="form-grid" data-validate><label class="field field-full">Your name<input type="text" name="name" autocomplete="name" maxlength="120" required value="<?= e($name) ?>" placeholder="Your name"></label><label class="field field-full">Email address<input type="email" name="email" autocomplete="email" maxlength="190" required value="<?= e($email) ?>" placeholder="you@example.com"></label><label class="field field-full">Subject<input type="text" name="subject" maxlength="180" required value="<?= e($subject) ?>" placeholder="What’s on your mind?"></label><label class="field field-full">Your message<textarea name="message" rows="5" maxlength="5000" required placeholder="Tell us a little more..."><?= e($message) ?></textarea></label><button class="button button-dark field-full" type="submit">Preview your note <span>↗</span></button></form>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
