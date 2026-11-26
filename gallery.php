<?php
require_once __DIR__ . '/site.php';
$pageTitle = 'Travel Gallery';
$activePage = 'gallery';
$galleryImages = array_slice(site_destinations(), 0, 8);
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero page-hero-gallery">
    <div class="wrap page-hero-content"><span class="eyebrow eyebrow-light">POSTCARDS FROM OUT THERE</span><h1>Little windows<br>to <em>everywhere.</em></h1><p>A collection of places that remind us why we go.</p></div>
</section>
<section class="section-pad wrap">
    <div class="section-heading"><div><span class="eyebrow">THE TRAVEL JOURNAL</span><h2>Scenes to <em>remember.</em></h2></div><p class="heading-note">Tap any photograph to take a closer look.</p></div>
    <div class="gallery-grid">
        <?php foreach ($galleryImages as $index => $photo): ?>
            <button class="gallery-item gallery-item-<?= $index % 6 ?>" type="button" data-image="<?= e($photo['image_url']) ?>" data-caption="<?= e($photo['name'] . ', ' . $photo['country']) ?>" aria-label="View <?= e($photo['name']) ?>, <?= e($photo['country']) ?>">
                <img src="<?= e($photo['image_url']) ?>" alt="<?= e($photo['name']) ?>, <?= e($photo['country']) ?>" loading="lazy"><span class="gallery-caption"><span><?= e($photo['name']) ?></span><small><?= e($photo['country']) ?></small></span>
            </button>
        <?php endforeach; ?>
    </div>
</section>
<dialog class="lightbox" id="gallery-lightbox" aria-label="Photo viewer"><button class="lightbox-close" type="button" aria-label="Close photo">×</button><img src="" alt=""><p></p></dialog>
<?php require __DIR__ . '/partials/footer.php'; ?>
