<?php
require_once __DIR__ . '/../site.php';
$pageTitle = 'Admin Preview';
$activePage = 'admin';
$destinations = site_destinations();
$packages = site_packages();
require __DIR__ . '/../partials/header.php';
?>
<section class="page-hero page-hero-booking">
    <div class="wrap page-hero-content"><span class="eyebrow eyebrow-light">TRAVEL AND TOURISM · ADMIN PREVIEW</span><h1>Keep the details<br><em>in good hands.</em></h1><p>A read-only preview of the places and journeys in this frontend-only site.</p></div>
</section>
<section class="section-pad admin-dashboard wrap">
    <div class="section-heading"><div><span class="eyebrow">OVERVIEW</span><h2>Admin <em>dashboard.</em></h2></div></div>
    <div class="alert alert-success" role="status">Preview mode: this website has no database. Changes, accounts, bookings, and messages are not saved.</div>
    <div class="admin-stats">
        <div><strong>—</strong><span>Registered users</span></div>
        <div><strong><?= count($destinations) ?></strong><span>Static destinations</span></div>
        <div><strong><?= count($packages) ?></strong><span>Static travel packages</span></div>
        <div><strong>—</strong><span>Booking requests</span></div>
    </div>

    <section class="admin-section" id="destinations">
        <div class="admin-section-heading"><div><span class="eyebrow">CURATE THE MAP</span><h2>Destinations</h2></div></div>
        <div class="admin-record-list">
            <?php foreach ($destinations as $destination): ?>
                <article class="admin-edit-card"><h3><?= e($destination['name']) ?> <small><?= e($destination['country']) ?></small></h3><p><?= e($destination['description']) ?></p><a class="text-link" href="<?= e($basePath) ?>booking.php?destination=<?= (int) $destination['id'] ?>">Preview a trip <span>↗</span></a></article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="admin-section" id="packages">
        <div class="admin-section-heading"><div><span class="eyebrow">BUILD THE JOURNEY</span><h2>Travel packages</h2></div></div>
        <div class="admin-record-list">
            <?php foreach ($packages as $package): ?>
                <article class="admin-edit-card"><h3><?= e($package['name']) ?> <small><?= e($package['destination_name']) ?></small></h3><p><?= e($package['description']) ?></p><span class="eyebrow"><?= (int) $package['duration_days'] ?> days · $<?= number_format((float) $package['price'], 0) ?> per traveler</span></article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="admin-section" id="bookings">
        <div class="admin-section-heading"><div><span class="eyebrow">GUEST REQUESTS</span><h2>Bookings</h2></div></div>
        <p class="admin-empty">Bookings are preview-only and are not collected or stored on this site.</p>
    </section>

    <section class="admin-section" id="messages">
        <div class="admin-section-heading"><div><span class="eyebrow">FROM OUR TRAVELERS</span><h2>Contact messages</h2></div></div>
        <p class="admin-empty">Contact form messages are not sent or stored in this frontend-only preview.</p>
    </section>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
