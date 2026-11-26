<?php
require_once __DIR__ . '/site.php';
$pageTitle = 'Welcome Back';
$activePage = 'login';
$email = '';
$demoMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $demoMessage = 'Demo mode: sign-in is not enabled. No account was authenticated and nothing was saved.';
}
require __DIR__ . '/partials/header.php';
?>
<section class="auth-page">
    <div class="auth-image auth-image-login"><span class="eyebrow eyebrow-light">WELCOME BACK, WANDERER</span><p>“We travel not to escape life, but for life not to escape us.”</p></div>
    <div class="auth-content"><span class="eyebrow">YOUR NEXT CHAPTER</span><h1>Good to see<br><em>you again.</em></h1><p class="auth-subtitle">This frontend-only preview has no account service. You can still explore every page.</p>
        <?php if ($demoMessage): ?><div class="alert alert-success" role="status"><?= e($demoMessage) ?></div><?php endif; ?>
        <form method="post" class="auth-form" data-validate><label class="field">Email address<input type="email" name="email" autocomplete="email" maxlength="190" required value="<?= e($email) ?>" placeholder="you@example.com"></label><label class="field">Password<input type="password" name="password" autocomplete="current-password" required placeholder="Your password"></label><button class="button button-dark" type="submit">Preview sign in <span>↗</span></button></form>
        <p class="auth-switch">New around here? <a href="register.php">Create an account</a></p>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
