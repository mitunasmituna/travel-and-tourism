<?php
require_once __DIR__ . '/site.php';
$pageTitle = 'Create an Account';
$activePage = 'login';
$name = '';
$email = '';
$demoMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $demoMessage = 'Demo mode: no account was created or authenticated, and your details were not saved.';
}
require __DIR__ . '/partials/header.php';
?>
<section class="auth-page">
    <div class="auth-image auth-image-register"><span class="eyebrow eyebrow-light">A WORLD OF STORIES</span><p>There is a whole world out there with your name on it.</p></div>
    <div class="auth-content"><span class="eyebrow">JOIN THE JOURNEY</span><h1>Somewhere new<br><em>starts here.</em></h1><p class="auth-subtitle">Create a demo account preview. No account details are stored.</p>
        <?php if ($demoMessage): ?><div class="alert alert-success" role="status"><?= e($demoMessage) ?></div><?php endif; ?>
        <form method="post" class="auth-form" data-validate><label class="field">Full name<input type="text" name="name" autocomplete="name" maxlength="120" required value="<?= e($name) ?>" placeholder="Your name"></label><label class="field">Email address<input type="email" name="email" autocomplete="email" maxlength="190" required value="<?= e($email) ?>" placeholder="you@example.com"></label><label class="field">Password<input type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72" required placeholder="At least 8 characters"></label><label class="field">Confirm password<input type="password" name="confirm_password" autocomplete="new-password" minlength="8" maxlength="72" required placeholder="Type it again"></label><button class="button button-dark" type="submit">Preview account <span>↗</span></button></form>
        <p class="auth-switch">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
