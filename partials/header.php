<?php
$pageTitle = $pageTitle ?? 'Travel & Tourism';
$activePage = $activePage ?? '';
$basePath = $basePath ?? '';
$flashMessage = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#163b32">
    <meta name="description" content="Thoughtfully designed journeys, extraordinary places, and memories that last a lifetime.">
    <title><?= e($pageTitle) ?> | TRAVEL AND TOURISM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e($basePath) ?>style.css">
    <script src="<?= e($basePath) ?>script.js" defer></script>
</head>
<body>
<header class="site-header">
    <a class="brand" href="<?= e($basePath) ?>index.php" aria-label="Travel and Tourism home">
        <span class="brand-mark" aria-hidden="true">T<span>✳</span></span>
        <span class="brand-copy"><strong>TRAVEL</strong><small>AND TOURISM</small></span>
    </a>
    <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="primary-nav">
        <span></span><span></span><span></span>
    </button>
    <nav class="primary-nav" id="primary-nav" aria-label="Main navigation">
        <?php
        $navItems = [
            'home' => ['Home', 'index.php'],
            'destinations' => ['Destinations', 'destinations.php'],
            'packages' => ['Packages', 'packages.php'],
            'gallery' => ['Gallery', 'gallery.php'],
            'booking' => ['Booking', 'booking.php'],
            'contact' => ['Contact', 'contact.php'],
        ];
        foreach ($navItems as $key => [$label, $href]):
        ?>
            <a class="<?= $activePage === $key ? 'is-active' : '' ?>" href="<?= e($basePath . $href) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
        <?php if (is_logged_in()): ?>
            <?php if (is_admin()): ?>            <a class="<?= $activePage === 'admin' ? 'is-active' : '' ?>" href="<?= e($basePath) ?>admin/index.php">Admin</a><?php endif; ?>
            <a class="<?= $activePage === 'my-bookings' ? 'is-active' : '' ?>" href="<?= e($basePath) ?>my_bookings.php">My trips</a>
            <a class="nav-account" href="<?= e($basePath) ?>logout.php">Log out</a>
        <?php else: ?>
            <a class="nav-account <?= $activePage === 'login' ? 'is-active' : '' ?>" href="<?= e($basePath) ?>login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main>
<?php if ($flashMessage): ?>
    <div class="flash-message flash-<?= e($flashMessage['type']) ?>" role="status"><?= e($flashMessage['message']) ?></div>
<?php endif; ?>
