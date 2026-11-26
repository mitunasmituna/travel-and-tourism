<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Plan Your Journey';
$activePage = 'booking';
$errors = [];
$success = false;
$bookingId = 0;
$bookingTotal = null;
$bookingTravelers = 0;
$name = (string) ($_SESSION['user_name'] ?? '');
$email = (string) ($_SESSION['user_email'] ?? '');
$selectedDestination = filter_input(INPUT_GET, 'destination', FILTER_VALIDATE_INT) ?: 0;
$selectedPackage = filter_input(INPUT_GET, 'package', FILTER_VALIDATE_INT) ?: 0;
$travelDate = '';
$travelers = 2;

if (!is_logged_in()) {
    flash('error', 'Please sign in before booking a journey.');
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['full_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $selectedDestination = filter_input(INPUT_POST, 'destination_id', FILTER_VALIDATE_INT) ?: 0;
    $selectedPackage = filter_input(INPUT_POST, 'package_id', FILTER_VALIDATE_INT) ?: 0;
    $travelDate = trim((string) ($_POST['travel_date'] ?? ''));
    $travelers = filter_input(INPUT_POST, 'travelers', FILTER_VALIDATE_INT) ?: 0;

    if (!csrf_is_valid()) {
        $errors[] = 'Your session expired. Please refresh the page and try again.';
    }
    if ($name === '' || mb_strlen($name) > 120) {
        $errors[] = 'Please enter your name (up to 120 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
        $errors[] = 'Please enter a valid email address.';
    }
    $dateObject = DateTime::createFromFormat('Y-m-d', $travelDate);
    if (!$dateObject || $dateObject->format('Y-m-d') !== $travelDate || $travelDate < date('Y-m-d')) {
        $errors[] = 'Choose a valid travel date that is today or later.';
    }
    if ($travelers < 1 || $travelers > 20) {
        $errors[] = 'Choose between 1 and 20 travelers.';
    }

    $packageCheck = $pdo->prepare('SELECT destination_id, price FROM packages WHERE id = ?');
    $packageCheck->execute([$selectedPackage]);
    $packageResult = $packageCheck->fetch();
    if (!$packageResult || (int) $packageResult['destination_id'] !== $selectedDestination) {
        $errors[] = 'Choose a travel package that matches the selected destination.';
    }

    if (!$errors) {
        $priceParts = explode('.', (string) $packageResult['price'], 2);
        $wholePrice = (int) $priceParts[0];
        $fractionalPrice = str_pad(substr($priceParts[1] ?? '', 0, 2), 2, '0');
        $unitPriceCents = ($wholePrice * 100) + (int) $fractionalPrice;
        $unitPrice = $wholePrice . '.' . $fractionalPrice;
        $totalCents = $unitPriceCents * $travelers;
        $bookingTotal = intdiv($totalCents, 100) . '.' . str_pad((string) ($totalCents % 100), 2, '0', STR_PAD_LEFT);
        $statement = $pdo->prepare('INSERT INTO bookings (user_id, package_id, destination_id, full_name, email, travel_date, travelers, unit_price, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        try {
            $statement->execute([(int) $_SESSION['user_id'], $selectedPackage, $selectedDestination, $name, $email, $travelDate, $travelers, $unitPrice, $bookingTotal]);
            $bookingId = (int) $pdo->lastInsertId();
            $bookingTravelers = $travelers;
            $success = true;
            $name = (string) ($_SESSION['user_name'] ?? '');
            $email = (string) ($_SESSION['user_email'] ?? '');
            $travelDate = '';
            $selectedDestination = $selectedPackage = 0;
            $travelers = 2;
        } catch (PDOException $exception) {
            error_log('Travel booking save failed: ' . $exception->getMessage());
            $errors[] = 'We could not save your booking request. Please try again.';
        }
    }
}

$destinations = $pdo->query('SELECT id, name, country FROM destinations ORDER BY name')->fetchAll();
$packageOptions = $pdo->query('SELECT p.id, p.destination_id, p.name, p.duration_days, p.price, d.name AS destination_name FROM packages p JOIN destinations d ON d.id = p.destination_id ORDER BY p.name')->fetchAll();
require __DIR__ . '/partials/header.php';
?>
<section class="page-hero page-hero-booking">
    <div class="wrap page-hero-content"><span class="eyebrow eyebrow-light">THE FIRST STEP TO SOMEWHERE</span><h1>Let's make it<br><em>happen.</em></h1><p>Tell us a little about the journey you have in mind. We’ll take it from here.</p></div>
</section>
<section class="section-pad wrap booking-layout">
    <aside class="booking-aside"><span class="eyebrow">YOUR JOURNEY, YOUR WAY</span><h2>Start with a<br><em>little daydream.</em></h2><p>Share your travel details and our team will be in touch to help shape an experience that feels just right.</p><div class="booking-note"><span>✳</span><p>All prices are sample per-person rates. Your final itinerary can be tailored to you.</p></div></aside>
    <div class="form-panel">
        <span class="eyebrow">PLAN YOUR TRIP</span><h2>Trip details</h2>
        <?php if ($success): ?><div class="alert alert-success" role="status">Booking request #<?= $bookingId ?> has been saved as pending. Total for <?= $bookingTravelers ?> traveler(s): $<?= e($bookingTotal) ?>. We’ll be in touch soon.</div><?php endif; ?>
        <?php if ($errors): ?><div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" class="form-grid" data-validate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label class="field field-full">Full name<input name="full_name" type="text" autocomplete="name" maxlength="120" required value="<?= e($name) ?>" placeholder="Your name"></label>
            <label class="field field-full">Email address<input name="email" type="email" autocomplete="email" maxlength="190" required value="<?= e($email) ?>" placeholder="you@example.com"></label>
            <label class="field">Destination<select name="destination_id" id="booking-destination" required><option value="">Choose a place</option><?php foreach ($destinations as $destination): ?><option value="<?= (int) $destination['id'] ?>" <?= $selectedDestination === (int) $destination['id'] ? 'selected' : '' ?>><?= e($destination['name'] . ', ' . $destination['country']) ?></option><?php endforeach; ?></select></label>
            <label class="field">Travel package<select name="package_id" id="booking-package" required><option value="">Choose an experience</option><?php foreach ($packageOptions as $package): ?><option value="<?= (int) $package['id'] ?>" data-destination="<?= (int) $package['destination_id'] ?>" <?= $selectedPackage === (int) $package['id'] ? 'selected' : '' ?>><?= e($package['name'] . ' · ' . $package['duration_days'] . ' days · $' . number_format((float) $package['price'], 0)) ?></option><?php endforeach; ?></select></label>
            <label class="field">Travel date<input type="date" name="travel_date" required min="<?= e(date('Y-m-d')) ?>" value="<?= e($travelDate) ?>"></label>
            <label class="field">Number of travelers<select name="travelers" required><?php for ($count = 1; $count <= 12; $count++): ?><option value="<?= $count ?>" <?= $travelers === $count ? 'selected' : '' ?>><?= $count ?> <?= $count === 1 ? 'traveler' : 'travelers' ?></option><?php endfor; ?><option value="20" <?= $travelers === 20 ? 'selected' : '' ?>>13–20 travelers</option></select></label>
            <button class="button button-dark field-full" type="submit">Request my journey <span>↗</span></button>
        </form>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
