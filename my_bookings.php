<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'My Journeys';
$activePage = 'my-bookings';

if (!is_logged_in()) {
    flash('error', 'Please sign in to view your journeys.');
    redirect('login.php');
}

$statement = $pdo->prepare(
    'SELECT b.id, b.travel_date, b.travelers, b.status, b.unit_price, b.total_amount, b.created_at,
            d.name AS destination_name, p.name AS package_name, p.duration_days
     FROM bookings b
     JOIN destinations d ON d.id = b.destination_id
     JOIN packages p ON p.id = b.package_id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC'
);
$statement->execute([(int) $_SESSION['user_id']]);
$bookings = $statement->fetchAll();
$statusLabels = [
    'pending' => 'Request received',
    'confirmed' => 'Confirmed',
    'cancelled' => 'Cancelled',
];
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero page-hero-booking">
    <div class="wrap page-hero-content"><span class="eyebrow eyebrow-light">YOUR TRAVEL NOTES</span><h1>Journeys with<br><em>your name on them.</em></h1><p>Keep track of your requests and the places you are planning to go.</p></div>
</section>
<section class="section-pad wrap">
    <div class="section-heading"><div><span class="eyebrow">MY ACCOUNT</span><h2>Your <em>journeys.</em></h2></div><a class="button button-dark" href="destinations.php">Plan another trip <span>↗</span></a></div>
    <?php if (!$bookings): ?>
        <div class="content-empty"><div><strong>Your travel story starts here.</strong><p>You haven’t requested a trip yet. Explore a destination and find a journey that feels like you.</p></div><a class="text-link" href="destinations.php">Explore destinations <span aria-hidden="true">↗</span></a></div>
    <?php else: ?>
        <div class="my-bookings-list">
            <?php foreach ($bookings as $booking): ?>
                <?php $status = $booking['status']; ?>
                <article class="my-booking-card">
                    <div class="my-booking-main"><span class="eyebrow"><?= e($booking['destination_name']) ?> · <?= (int) $booking['duration_days'] ?> DAYS</span><h3><?= e($booking['package_name']) ?></h3><span class="my-booking-date"><?= e(date('F j, Y', strtotime($booking['travel_date']))) ?> · <?= (int) $booking['travelers'] ?> <?= (int) $booking['travelers'] === 1 ? 'traveler' : 'travelers' ?></span><span class="my-booking-amount">$<?= e(number_format((float) $booking['total_amount'], 2)) ?> total · $<?= e(number_format((float) $booking['unit_price'], 2)) ?> per traveler</span></div>
                    <span class="booking-status status-<?= e($status) ?>"><?= e($statusLabels[$status] ?? 'Status unavailable') ?></span>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
