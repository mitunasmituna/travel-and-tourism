<?php
require_once __DIR__ . '/site.php';
$pageTitle = 'My Journeys';
$activePage = 'my-bookings';
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero page-hero-booking">
    <div class="wrap page-hero-content"><span class="eyebrow eyebrow-light">YOUR TRAVEL NOTES</span><h1>Journeys with<br><em>your name on them.</em></h1><p>Keep track of the places you are planning to go.</p></div>
</section>
<section class="section-pad wrap">
    <div class="section-heading"><div><span class="eyebrow">MY ACCOUNT</span><h2>Your <em>journeys.</em></h2></div><a class="button button-dark" href="destinations.php">Plan another trip <span>↗</span></a></div>
    <div class="content-empty"><div><strong>Your travel story starts here.</strong><p>This frontend-only preview does not create accounts or save bookings. Explore a destination and plan a sample trip.</p></div><a class="text-link" href="destinations.php">Explore destinations <span aria-hidden="true">↗</span></a></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
