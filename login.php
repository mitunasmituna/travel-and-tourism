<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Welcome Back';
$activePage = 'login';
$errors = [];
$email = '';
if (is_logged_in()) {
    redirect('index.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if (!csrf_is_valid()) {
        $errors[] = 'Your session expired. Please refresh the page and try again.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address and password.';
    } elseif (!$errors) {
        $statement = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
        $statement->execute([$email]);
        $user = $statement->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            flash('success', 'Welcome back, ' . $user['name'] . '. Your next journey awaits.');
            redirect('index.php');
        }
        $errors[] = 'The email or password you entered is incorrect.';
    }
}
require __DIR__ . '/partials/header.php';
?>
<section class="auth-page">
    <div class="auth-image auth-image-login"><span class="eyebrow eyebrow-light">WELCOME BACK, WANDERER</span><p>“We travel not to escape life, but for life not to escape us.”</p></div>
    <div class="auth-content"><span class="eyebrow">YOUR NEXT CHAPTER</span><h1>Good to see<br><em>you again.</em></h1><p class="auth-subtitle">Sign in to pick up where your daydreams left off.</p>
        <?php if ($errors): ?><div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" class="auth-form" data-validate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label class="field">Email address<input type="email" name="email" autocomplete="email" maxlength="190" required value="<?= e($email) ?>" placeholder="you@example.com"></label><label class="field">Password<input type="password" name="password" autocomplete="current-password" required placeholder="Your password"></label><button class="button button-dark" type="submit">Sign in <span>↗</span></button></form>
        <p class="auth-switch">New around here? <a href="register.php">Create an account</a></p>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
