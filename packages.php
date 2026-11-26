<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Travel Packages';
$activePage = 'packages';
$packages = $pdo->query('SELECT p.id, p.destination_id, p.name, p.duration_days, p.price, p.description, d.name AS destination_name, d.country, d.image_url FROM packages p JOIN destinations d ON d.id = p.destination_id ORDER BY p.featured DESC, p.id')->fetchAll();
$packageDestinations = $pdo->query('SELECT DISTINCT d.id, d.name FROM packages p JOIN destinations d ON d.id = p.destination_id ORDER BY d.name')->fetchAll();
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero page-hero-packages">
    <div class="wrap page-hero-content"><span class="eyebrow eyebrow-light">THOUGHTFULLY PUT TOGETHER</span><h1>Good journeys.<br><em>Better stories.</em></h1><p>Considered itineraries with space to wander, made for memories that stay with you.</p></div>
</section>
<section class="section-pad wrap">
    <div class="section-heading"><div><span class="eyebrow">A PLACE TO BEGIN</span><h2>Choose your <em>own pace.</em></h2></div><p class="heading-note">Every journey can be tailored. These are just the beginnings.</p></div>
    <?php if ($packages): ?>
        <div class="package-filter-bar">
            <label class="search-box"><span aria-hidden="true">⌕</span><input id="package-search" type="search" placeholder="Search journeys or places" aria-label="Search travel packages"></label>
            <label class="package-destination-filter"><span class="eyebrow">FILTER BY PLACE</span><select id="package-destination"><option value="">All destinations</option><?php foreach ($packageDestinations as $destination): ?><option value="<?= (int) $destination['id'] ?>"><?= e($destination['name']) ?></option><?php endforeach; ?></select></label>
            <p class="package-result-count" id="package-result-count" aria-live="polite"><?= count($packages) ?> <?= count($packages) === 1 ? 'journey' : 'journeys' ?></p>
            <button class="filter-reset" id="package-filter-reset" type="button" hidden>Clear filters</button>
        </div>
    <?php endif; ?>
    <div class="package-grid">
        <?php if (!$packages): ?>
            <div class="content-empty"><div><strong>New experiences are on the way.</strong><p>There are no packages available right now. Our travel team can still help you plan a trip.</p></div><a class="text-link" href="contact.php">Contact a planner <span aria-hidden="true">↗</span></a></div>
        <?php else: ?>
            <?php foreach ($packages as $package): ?>
                <article class="package-card" data-destination="<?= (int) $package['destination_id'] ?>" data-search="<?= e(mb_strtolower($package['name'] . ' ' . $package['destination_name'] . ' ' . $package['country'] . ' ' . $package['description'], 'UTF-8')) ?>">
                    <a class="package-image" href="booking.php?package=<?= (int) $package['id'] ?>"><img src="<?= e($package['image_url']) ?>" alt="<?= e($package['destination_name']) ?>" loading="lazy"><span><?= (int) $package['duration_days'] ?> days</span></a>
                    <div class="package-body"><span class="eyebrow"><?= e($package['destination_name']) ?> · <?= e($package['country']) ?></span><h3><?= e($package['name']) ?></h3><p><?= e($package['description']) ?></p><div class="package-footer"><span>From <strong>$<?= number_format((float) $package['price'], 0) ?></strong> <small>/ person</small></span><a class="round-link" href="booking.php?package=<?= (int) $package['id'] ?>" aria-label="Book <?= e($package['name']) ?>">↗</a></div></div>
                </article>
            <?php endforeach; ?>
            <p class="empty-state" id="package-no-results" role="status" hidden>No journeys match those filters. Try another search or clear your filters.</p>
        <?php endif; ?>
    </div>
</section>
<section class="quote-band"><span class="eyebrow eyebrow-light">NO TWO JOURNEYS SHOULD FEEL THE SAME</span><p>“The world is a book, and those who do not travel read only one page.”</p><a href="booking.php">Let's turn the page <span>↗</span></a></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
