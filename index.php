<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Discover the World';
$activePage = 'home';
$destinations = $pdo->query('SELECT id, name, country, description, image_url FROM destinations WHERE featured = 1 ORDER BY id LIMIT 3')->fetchAll();
$packages = $pdo->query('SELECT p.id, p.name, p.duration_days, p.price, d.name AS destination_name FROM packages p JOIN destinations d ON d.id = p.destination_id WHERE p.featured = 1 ORDER BY p.id LIMIT 3')->fetchAll();
$destinationCount = (int) $pdo->query('SELECT COUNT(*) FROM destinations')->fetchColumn();
$packageCount = (int) $pdo->query('SELECT COUNT(*) FROM packages')->fetchColumn();
$bookingCount = (int) $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
require __DIR__ . '/partials/header.php';
?>
<section class="hero">
    <div class="hero-image"></div>
    <div class="hero-content wrap">
        <span class="eyebrow eyebrow-light">TAKE THE SCENIC ROUTE</span>
        <h1>TRAVEL AND<br><em>TOURISM</em></h1>
        <p>Discover the World,<br class="mobile-break"> Create Memories</p>
        <a class="button button-gold" href="destinations.php">Explore now <span aria-hidden="true">↗</span></a>
    </div>
    <a class="hero-scroll" href="#intro"><span></span>SCROLL TO WANDER</a>
    <div class="hero-caption">01 / YOUR NEXT CHAPTER</div>
</section>

<section class="intro-section section-pad wrap" id="intro">
    <div class="intro-kicker"><span class="eyebrow">TRAVEL, THOUGHTFULLY</span><span class="fine-line"></span><span>EST. FOR THE CURIOUS</span></div>
    <div class="intro-copy">
        <h2>Not just a place<br>on the map. <em>A feeling.</em></h2>
        <div><p>We believe the best journeys leave room for the unexpected. Find a slower pace, local stories and stays that make the whole world feel a little closer.</p><a class="text-link" href="destinations.php">Find your next journey <span aria-hidden="true">↗</span></a></div>
    </div>
</section>

<section class="section-pad section-tint">
    <div class="wrap">
        <div class="section-heading">
            <div><span class="eyebrow">THE PLACES WE LOVE</span><h2>Somewhere <em>calling.</em></h2></div>
            <a class="text-link" href="destinations.php">All destinations <span aria-hidden="true">↗</span></a>
        </div>
        <div class="destination-grid home-destinations">
            <?php if (!$destinations): ?>
                <div class="content-empty"><div><strong>Somewhere new is on its way.</strong><p>Our destination collection is being prepared. Check back soon or get in touch to plan a trip.</p></div><a class="text-link" href="contact.php">Talk to us <span aria-hidden="true">↗</span></a></div>
            <?php else: ?>
                <?php foreach ($destinations as $destination): ?>
                    <a class="destination-card" href="booking.php?destination=<?= (int) $destination['id'] ?>">
                        <img src="<?= e($destination['image_url']) ?>" alt="<?= e($destination['name']) ?>" loading="lazy">
                        <span class="card-shade"></span>
                        <span class="destination-info"><small><?= e($destination['country']) ?></small><strong><?= e($destination['name']) ?></strong><span>Find your way <b>↗</b></span></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section-pad wrap experience-section">
    <div class="section-heading">
        <div><span class="eyebrow">MADE FOR THE MOMENT</span><h2>Journeys with <em>meaning.</em></h2></div>
        <a class="text-link" href="packages.php">View all journeys <span aria-hidden="true">↗</span></a>
    </div>
    <div class="experience-grid">
        <?php if (!$packages): ?>
            <div class="content-empty"><div><strong>New journeys are taking shape.</strong><p>Our travel experiences will appear here as soon as they are available.</p></div><a class="text-link" href="contact.php">Ask a planner <span aria-hidden="true">↗</span></a></div>
        <?php else: ?>
            <?php foreach ($packages as $package): ?>
                <article class="experience-card">
                    <span class="experience-number">0<?= (int) $package['id'] ?></span>
                    <span class="eyebrow"><?= (int) $package['duration_days'] ?> DAYS · <?= e($package['destination_name']) ?></span>
                    <h3><?= e($package['name']) ?></h3>
                    <div class="experience-bottom"><span>From <strong>$<?= number_format((float) $package['price'], 0) ?></strong> / person</span><a href="booking.php?package=<?= (int) $package['id'] ?>" aria-label="Book <?= e($package['name']) ?>">↗</a></div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="why-section">
    <div class="why-photo"></div>
    <div class="why-content">
        <span class="eyebrow eyebrow-light">A BETTER WAY TO GET THERE</span>
        <h2>Travel that feels<br><em>like you.</em></h2>
        <p>Small details, trusted local partners and room to make each journey your own. That is the difference between simply going and truly being there.</p>
        <a class="button button-outline-light" href="contact.php">Let's plan together <span>↗</span></a>
    </div>
    <div class="why-aside">GOOD PLACES. GOOD PEOPLE. GOOD STORIES.</div>
</section>

<section class="stats-section wrap">
    <div><strong><?= $destinationCount ?>+</strong><span>curated destinations</span></div>
    <div><strong><?= $packageCount ?>+</strong><span>thoughtful journeys</span></div>
    <div><strong><?= number_format($bookingCount) ?>+</strong><span>journeys started</span></div>
    <div class="stats-note"><span class="eyebrow">THE BEST SOUVENIR?</span><p>A story you can't wait to tell.</p></div>
</section>

<section class="closing-cta">
    <span class="eyebrow eyebrow-light">YOUR NEXT CHAPTER IS OUT THERE</span>
    <h2>Go where you <em>feel alive.</em></h2>
    <a class="button button-gold" href="destinations.php">Find your somewhere <span>↗</span></a>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
