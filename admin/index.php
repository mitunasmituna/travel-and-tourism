<?php
require_once __DIR__ . '/../db.php';
$pageTitle = 'Admin Dashboard';
$activePage = 'admin';
$basePath = '../';

if (!is_logged_in()) {
    flash('error', 'Please sign in to continue.');
    redirect('../login.php');
}

$roleStatement = $pdo->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
$roleStatement->execute([(int) $_SESSION['user_id']]);
$currentRole = $roleStatement->fetchColumn();
if ($currentRole !== 'admin') {
    $_SESSION['user_role'] = is_string($currentRole) ? $currentRole : 'user';
    http_response_code(403);
    exit('You do not have permission to access the admin dashboard.');
}
$_SESSION['user_role'] = 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postString = static function (string $key): string {
        return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
    };
    $action = $postString('action');

    if (!csrf_is_valid()) {
        flash('error', 'Your session expired. Please refresh the page and try again.');
        redirect('index.php');
    }

    try {
        if ($action === 'save_destination') {
            $id = $postString('id') === '' ? null : filter_var($postString('id'), FILTER_VALIDATE_INT);
            $name = $postString('name');
            $country = $postString('country');
            $region = $postString('region');
            $description = $postString('description');
            $imageUrl = $postString('image_url');
            $errors = [];

            if ($id === false || $name === '' || mb_strlen($name) > 120) $errors[] = 'Enter a destination name up to 120 characters.';
            if ($country === '' || mb_strlen($country) > 120) $errors[] = 'Enter a country up to 120 characters.';
            if (mb_strlen($region) > 120) $errors[] = 'Region must be 120 characters or fewer.';
            if ($description === '' || mb_strlen($description) > 5000) $errors[] = 'Enter a description up to 5,000 characters.';
            if (!filter_var($imageUrl, FILTER_VALIDATE_URL) || parse_url($imageUrl, PHP_URL_SCHEME) !== 'https' || mb_strlen($imageUrl) > 500) $errors[] = 'Use a valid HTTPS image URL up to 500 characters.';

            if ($errors) {
                flash('error', implode(' ', $errors));
            } else {
                if ($id === null) {
                    $statement = $pdo->prepare('INSERT INTO destinations (name, country, region, description, image_url) VALUES (?, ?, ?, ?, ?)');
                    $statement->execute([$name, $country, $region !== '' ? $region : null, $description, $imageUrl]);
                    flash('success', 'Destination added.');
                } else {
                    $statement = $pdo->prepare('UPDATE destinations SET name = ?, country = ?, region = ?, description = ?, image_url = ? WHERE id = ?');
                    $statement->execute([$name, $country, $region !== '' ? $region : null, $description, $imageUrl, $id]);
                    flash('success', 'Destination updated.');
                }
            }
        } elseif ($action === 'delete_destination') {
            $id = filter_var($postString('id'), FILTER_VALIDATE_INT);
            if (!$id) {
                flash('error', 'Choose a valid destination.');
            } else {
                $statement = $pdo->prepare('DELETE FROM destinations WHERE id = ?');
                $statement->execute([$id]);
                flash($statement->rowCount() ? 'success' : 'error', $statement->rowCount() ? 'Destination deleted.' : 'Destination not found.');
            }
        } elseif ($action === 'save_package') {
            $id = $postString('id') === '' ? null : filter_var($postString('id'), FILTER_VALIDATE_INT);
            $destinationId = filter_var($postString('destination_id'), FILTER_VALIDATE_INT);
            $name = $postString('name');
            $duration = filter_var($postString('duration_days'), FILTER_VALIDATE_INT);
            $price = $postString('price');
            $description = $postString('description');
            $featured = $postString('featured') === '1' ? 1 : 0;
            $errors = [];

            if ($id === false || !$destinationId) $errors[] = 'Choose a valid destination.';
            if ($name === '' || mb_strlen($name) > 150) $errors[] = 'Enter a package name up to 150 characters.';
            if (!$duration || $duration > 255) $errors[] = 'Duration must be between 1 and 255 days.';
            if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', $price)) $errors[] = 'Enter a price with up to 8 whole digits and 2 decimal places.';
            if ($description === '' || mb_strlen($description) > 5000) $errors[] = 'Enter a description up to 5,000 characters.';

            if ($errors) {
                flash('error', implode(' ', $errors));
            } else {
                if ($id === null) {
                    $statement = $pdo->prepare('INSERT INTO packages (destination_id, name, duration_days, price, description, featured) VALUES (?, ?, ?, ?, ?, ?)');
                    $statement->execute([$destinationId, $name, $duration, $price, $description, $featured]);
                    flash('success', 'Travel package added.');
                } else {
                    $statement = $pdo->prepare('UPDATE packages SET destination_id = ?, name = ?, duration_days = ?, price = ?, description = ?, featured = ? WHERE id = ?');
                    $statement->execute([$destinationId, $name, $duration, $price, $description, $featured, $id]);
                    flash('success', 'Travel package updated.');
                }
            }
        } elseif ($action === 'delete_package') {
            $id = filter_var($postString('id'), FILTER_VALIDATE_INT);
            if (!$id) {
                flash('error', 'Choose a valid travel package.');
            } else {
                $statement = $pdo->prepare('DELETE FROM packages WHERE id = ?');
                $statement->execute([$id]);
                flash($statement->rowCount() ? 'success' : 'error', $statement->rowCount() ? 'Travel package deleted.' : 'Travel package not found.');
            }
        } elseif ($action === 'update_booking_status') {
            $id = filter_var($postString('id'), FILTER_VALIDATE_INT);
            $status = $postString('status');
            if (!$id || !in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
                flash('error', 'Choose a valid booking and status.');
            } else {
                $statement = $pdo->prepare('UPDATE bookings SET status = ? WHERE id = ?');
                $statement->execute([$status, $id]);
                flash('success', 'Booking status updated.');
            }
        } else {
            flash('error', 'That admin action is not available.');
        }
    } catch (PDOException $exception) {
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);
        if ($driverCode === 1062) {
            flash('error', 'A destination or package with those details already exists.');
        } elseif ($driverCode === 1451) {
            flash('error', 'This item is linked to travel records and cannot be deleted.');
        } else {
            error_log('Travel admin action failed: ' . $exception->getMessage());
            flash('error', 'We could not save that change. Please try again.');
        }
    }
    redirect('index.php');
}

$stats = [
    'users' => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'destinations' => (int) $pdo->query('SELECT COUNT(*) FROM destinations')->fetchColumn(),
    'packages' => (int) $pdo->query('SELECT COUNT(*) FROM packages')->fetchColumn(),
    'bookings' => (int) $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
];
$destinations = $pdo->query('SELECT id, name, country, region, description, image_url FROM destinations ORDER BY name')->fetchAll();
$packages = $pdo->query('SELECT p.id, p.destination_id, p.name, p.duration_days, p.price, p.description, p.featured, d.name AS destination_name FROM packages p JOIN destinations d ON d.id = p.destination_id ORDER BY p.name')->fetchAll();
$bookings = $pdo->query('SELECT id, full_name, email, travel_date, travelers, unit_price, total_amount, status FROM bookings ORDER BY created_at DESC LIMIT 100')->fetchAll();
$messages = $pdo->query('SELECT name, email, subject, message, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 100')->fetchAll();
require __DIR__ . '/../partials/header.php';
?>
<section class="page-hero page-hero-booking">
    <div class="wrap page-hero-content"><span class="eyebrow eyebrow-light">TRAVEL AND TOURISM · ADMIN</span><h1>Keep the details<br><em>in good hands.</em></h1><p>Manage the places and journeys, and keep an eye on guest requests.</p></div>
</section>
<section class="section-pad admin-dashboard wrap">
    <div class="section-heading"><div><span class="eyebrow">OVERVIEW</span><h2>Admin <em>dashboard.</em></h2></div></div>
    <div class="admin-stats">
        <div><strong><?= $stats['users'] ?></strong><span>Registered users</span></div>
        <div><strong><?= $stats['destinations'] ?></strong><span>Destinations</span></div>
        <div><strong><?= $stats['packages'] ?></strong><span>Travel packages</span></div>
        <div><strong><?= $stats['bookings'] ?></strong><span>Booking requests</span></div>
    </div>

    <section class="admin-section" id="destinations">
        <div class="admin-section-heading"><div><span class="eyebrow">CURATE THE MAP</span><h2>Destinations</h2></div></div>
        <form method="post" class="admin-edit-card admin-form" data-validate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save_destination">
            <h3>Add a destination</h3>
            <div class="admin-form-grid">
                <label class="field">Name<input name="name" maxlength="120" required></label>
                <label class="field">Country<input name="country" maxlength="120" required></label>
                <label class="field">Region<input name="region" maxlength="120"></label>
                <label class="field field-full">Image URL (HTTPS)<input name="image_url" type="url" maxlength="500" required></label>
                <label class="field field-full">Description<textarea name="description" rows="3" maxlength="5000" required></textarea></label>
            </div>
            <button class="button button-dark" type="submit">Add destination <span>↗</span></button>
        </form>
        <div class="admin-record-list">
            <?php foreach ($destinations as $destination): ?>
                <article class="admin-edit-card">
                    <form method="post" class="admin-form" data-validate>
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save_destination"><input type="hidden" name="id" value="<?= (int) $destination['id'] ?>">
                        <h3><?= e($destination['name']) ?> <small><?= e($destination['country']) ?></small></h3>
                        <div class="admin-form-grid">
                            <label class="field">Name<input name="name" maxlength="120" required value="<?= e($destination['name']) ?>"></label>
                            <label class="field">Country<input name="country" maxlength="120" required value="<?= e($destination['country']) ?>"></label>
                            <label class="field">Region<input name="region" maxlength="120" value="<?= e($destination['region']) ?>"></label>
                            <label class="field field-full">Image URL<input name="image_url" type="url" maxlength="500" required value="<?= e($destination['image_url']) ?>"></label>
                            <label class="field field-full">Description<textarea name="description" rows="3" maxlength="5000" required><?= e($destination['description']) ?></textarea></label>
                        </div>
                        <button class="button button-dark" type="submit">Save destination</button>
                    </form>
                    <form method="post" class="admin-delete-form" data-confirm="Delete this destination? Linked packages or bookings will prevent deletion.">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete_destination"><input type="hidden" name="id" value="<?= (int) $destination['id'] ?>">
                        <button class="button button-danger" type="submit">Delete destination</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="admin-section" id="packages">
        <div class="admin-section-heading"><div><span class="eyebrow">BUILD THE JOURNEY</span><h2>Travel packages</h2></div></div>
        <form method="post" class="admin-edit-card admin-form" data-validate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save_package">
            <h3>Add a package</h3>
            <div class="admin-form-grid">
                <label class="field">Destination<select name="destination_id" required><option value="">Choose a destination</option><?php foreach ($destinations as $destination): ?><option value="<?= (int) $destination['id'] ?>"><?= e($destination['name'] . ', ' . $destination['country']) ?></option><?php endforeach; ?></select></label>
                <label class="field">Package name<input name="name" maxlength="150" required></label>
                <label class="field">Duration (days)<input name="duration_days" type="number" min="1" max="255" required></label>
                <label class="field">Price per traveler ($)<input name="price" type="number" min="0" max="99999999.99" step="0.01" required></label>
                <label class="field field-full">Description<textarea name="description" rows="3" maxlength="5000" required></textarea></label>
                <label class="admin-checkbox"><input type="checkbox" name="featured" value="1"> Feature on the homepage</label>
            </div>
            <button class="button button-dark" type="submit">Add package <span>↗</span></button>
        </form>
        <div class="admin-record-list">
            <?php foreach ($packages as $package): ?>
                <article class="admin-edit-card">
                    <form method="post" class="admin-form" data-validate>
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save_package"><input type="hidden" name="id" value="<?= (int) $package['id'] ?>">
                        <h3><?= e($package['name']) ?> <small><?= e($package['destination_name']) ?></small></h3>
                        <div class="admin-form-grid">
                            <label class="field">Destination<select name="destination_id" required><?php foreach ($destinations as $destination): ?><option value="<?= (int) $destination['id'] ?>" <?= (int) $package['destination_id'] === (int) $destination['id'] ? 'selected' : '' ?>><?= e($destination['name'] . ', ' . $destination['country']) ?></option><?php endforeach; ?></select></label>
                            <label class="field">Package name<input name="name" maxlength="150" required value="<?= e($package['name']) ?>"></label>
                            <label class="field">Duration (days)<input name="duration_days" type="number" min="1" max="255" required value="<?= (int) $package['duration_days'] ?>"></label>
                            <label class="field">Price per traveler ($)<input name="price" type="number" min="0" max="99999999.99" step="0.01" required value="<?= e((string) $package['price']) ?>"></label>
                            <label class="field field-full">Description<textarea name="description" rows="3" maxlength="5000" required><?= e($package['description']) ?></textarea></label>
                            <label class="admin-checkbox"><input type="checkbox" name="featured" value="1" <?= (int) $package['featured'] === 1 ? 'checked' : '' ?>> Feature on the homepage</label>
                        </div>
                        <button class="button button-dark" type="submit">Save package</button>
                    </form>
                    <form method="post" class="admin-delete-form" data-confirm="Delete this package? Existing bookings will prevent deletion.">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete_package"><input type="hidden" name="id" value="<?= (int) $package['id'] ?>">
                        <button class="button button-danger" type="submit">Delete package</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="admin-section" id="bookings">
        <div class="admin-section-heading"><div><span class="eyebrow">GUEST REQUESTS</span><h2>Bookings</h2></div></div>
        <div class="admin-table-wrap"><table class="admin-table">
            <thead><tr><th>Traveler</th><th>Email</th><th>Travel date</th><th>People</th><th>Total</th><th>Status</th></tr></thead>
            <tbody><?php foreach ($bookings as $booking): ?><tr>
                <td><?= e($booking['full_name']) ?></td><td><a href="mailto:<?= e($booking['email']) ?>"><?= e($booking['email']) ?></a></td><td><?= e(date('M j, Y', strtotime($booking['travel_date']))) ?></td><td><?= (int) $booking['travelers'] ?></td><td>$<?= e(number_format((float) $booking['total_amount'], 2)) ?></td>
                <td><form method="post" class="admin-status-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="update_booking_status"><input type="hidden" name="id" value="<?= (int) $booking['id'] ?>"><select name="status" aria-label="Status for booking <?= (int) $booking['id'] ?>"><?php foreach (['pending', 'confirmed', 'cancelled'] as $status): ?><option value="<?= e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option><?php endforeach; ?></select><button type="submit">Save</button></form></td>
            </tr><?php endforeach; ?></tbody>
        </table><?php if (!$bookings): ?><p class="admin-empty">No booking requests yet.</p><?php endif; ?></div>
    </section>

    <section class="admin-section" id="messages">
        <div class="admin-section-heading"><div><span class="eyebrow">FROM OUR TRAVELERS</span><h2>Contact messages</h2></div></div>
        <?php if (!$messages): ?><p class="admin-empty">No messages yet.</p><?php else: ?>
            <div class="admin-message-list"><?php foreach ($messages as $message): ?><article class="admin-edit-card admin-message-card"><div><span class="eyebrow"><?= e(date('M j, Y', strtotime($message['created_at']))) ?></span><h3><?= e($message['subject']) ?></h3><span class="admin-message-from"><?= e($message['name']) ?> · <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></span></div><p><?= e($message['message']) ?></p></article><?php endforeach; ?></div>
        <?php endif; ?>
    </section>
</section>
<?php require __DIR__ . '/../partials/footer.php'; ?>
