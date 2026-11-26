<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Create an Account';
$activePage = 'login';
$errors = [];
$name = '';
$email = '';
if (is_logged_in()) {
    redirect('index.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
    if (!csrf_is_valid()) {
        $errors[] = 'Your session expired. Please refresh the page and try again.';
    }
    if ($name === '' || mb_strlen($name) > 120) {
        $errors[] = 'Enter your name (up to 120 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
        $errors[] = 'Enter a valid email address.';
    }
    if (strlen($password) < 8 || strlen($password) > 72) {
        $errors[] = 'Choose a password between 8 and 72 characters.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Your passwords do not match.';
    }
    if (!$errors) {
        $statement = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $statement->execute([$name, $email, $passwordHash]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'user';
            flash('success', 'Your account is ready. Let the daydreaming begin.');
            redirect('index.php');
        } catch (PDOException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                $errors[] = 'An account with that email address already exists.';
            } else {
                error_log('Travel registration failed: ' . $exception->getMessage());
                $errors[] = 'We could not create your account right now. Please try again.';
            }
        }
    }
}
require __DIR__ . '/partials/header.php';
?>
<section class="auth-page">
    <div class="auth-image auth-image-register"><span class="eyebrow eyebrow-light">A WORLD OF STORIES</span><p>There is a whole world out there with your name on it.</p></div>
    <div class="auth-content"><span class="eyebrow">JOIN THE JOURNEY</span><h1>Somewhere new<br><em>starts here.</em></h1><p class="auth-subtitle">Create your account and keep all your travel ideas close.</p>
        <?php if ($errors): ?><div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" class="auth-form" data-validate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label class="field">Full name<input type="text" name="name" autocomplete="name" maxlength="120" required value="<?= e($name) ?>" placeholder="Your name"></label><label class="field">Email address<input type="email" name="email" autocomplete="email" maxlength="190" required value="<?= e($email) ?>" placeholder="you@example.com"></label><label class="field">Password<input type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72" required placeholder="At least 8 characters"></label><label class="field">Confirm password<input type="password" name="confirm_password" autocomplete="new-password" minlength="8" maxlength="72" required placeholder="Type it again"></label><button class="button button-dark" type="submit">Create account <span>↗</span></button></form>
        <p class="auth-switch">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
