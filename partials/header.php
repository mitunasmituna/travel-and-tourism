<?php
$pageTitle = $pageTitle ?? 'Travel & Tourism';
$activePage = $activePage ?? '';
$basePath = $basePath ?? '';
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
            'my-bookings' => ['My trips', 'my_bookings.php'],
            'contact' => ['Contact', 'contact.php'],
            'admin' => ['Admin demo', 'admin/index.php'],
        ];
        foreach ($navItems as $key => [$label, $href]):
        ?>
            <a class="<?= $activePage === $key ? 'is-active' : '' ?>" href="<?= e($basePath . $href) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
        <a class="nav-account <?= $activePage === 'login' ? 'is-active' : '' ?>" href="<?= e($basePath) ?>login.php">Login</a>
    </nav>
</header>
<main>
