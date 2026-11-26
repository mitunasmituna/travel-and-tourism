<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Explore Destinations';
$activePage = 'destinations';
$destinations = $pdo->query('SELECT id, name, country, region, description, image_url FROM destinations ORDER BY featured DESC, name ASC')->fetchAll();
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero page-hero-destinations">
    <div class="wrap page-hero-content"><span class="eyebrow eyebrow-light">A WORLD OF POSSIBILITIES</span><h1>Find your <em>somewhere.</em></h1><p>From misty mountain mornings to salt-air evenings, your next favourite place is waiting.</p></div>
</section>
<section class="section-pad wrap">
    <div class="listing-toolbar"><div><span class="eyebrow">CURATED FOR THE CURIOUS</span><h2>Places to <em>stay awhile.</em></h2></div><label class="search-box"><span aria-hidden="true">⌕</span><input id="destination-search" type="search" placeholder="Search a place or country" aria-label="Search destinations"></label></div>
    <div class="destination-grid listing-grid" id="destination-grid">
        <?php if (!$destinations): ?>
            <div class="content-empty"><div><strong>No destinations to show just yet.</strong><p>Our destination collection is being updated. Please check back soon.</p></div><a class="text-link" href="contact.php">Ask us about a trip <span aria-hidden="true">↗</span></a></div>
        <?php else: ?>
            <?php foreach ($destinations as $destination): ?>
                <article class="destination-card listing-card" data-search="<?= e(strtolower($destination['name'] . ' ' . $destination['country'] . ' ' . $destination['region'])) ?>">
                    <img src="<?= e($destination['image_url']) ?>" alt="<?= e($destination['name']) ?>" loading="lazy"><span class="card-shade"></span>
                    <div class="destination-info"><small><?= e($destination['region'] ? $destination['region'] . ', ' . $destination['country'] : $destination['country']) ?></small><strong><?= e($destination['name']) ?></strong><p><?= e($destination['description']) ?></p><a href="booking.php?destination=<?= (int) $destination['id'] ?>">Explore more <b>↗</b></a></div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
        <p class="empty-state" id="no-destinations" hidden>No places match that search. Try another destination.</p>
    </div>
</section>
<section class="small-cta"><div class="wrap small-cta-inner"><p>Have a place in mind? <em>We can make it happen.</em></p><a class="button button-dark" href="contact.php">Talk to a planner <span>↗</span></a></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
