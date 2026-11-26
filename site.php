<?php
declare(strict_types=1);

$scriptPath = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = str_contains($scriptPath, '/admin/') ? '../' : '';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function site_destinations(): array
{
    return [
        ['id' => 1, 'name' => 'Ooty', 'country' => 'India', 'region' => 'Tamil Nadu', 'description' => 'Winding tea gardens, cool mountain air and toy-train views make this hill escape a timeless favourite.', 'image_url' => 'https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=1200&q=85', 'featured' => 1],
        ['id' => 2, 'name' => 'Kerala', 'country' => 'India', 'region' => 'South India', 'description' => 'Drift through palm-fringed backwaters, spice-scented hills and a coastline made for slowing down.', 'image_url' => 'https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?auto=format&fit=crop&w=1200&q=85', 'featured' => 1],
        ['id' => 3, 'name' => 'Paris', 'country' => 'France', 'region' => 'Île-de-France', 'description' => 'A city of café mornings, art-filled afternoons and golden-hour walks along the Seine.', 'image_url' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=1200&q=85', 'featured' => 1],
        ['id' => 4, 'name' => 'Swiss Alps', 'country' => 'Switzerland', 'region' => 'Central Europe', 'description' => 'Ride alpine railways past emerald valleys, mirror lakes and peaks that reach into the clouds.', 'image_url' => 'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?auto=format&fit=crop&w=1200&q=85', 'featured' => 1],
        ['id' => 5, 'name' => 'Bali', 'country' => 'Indonesia', 'region' => 'Southeast Asia', 'description' => 'Find your rhythm among rice terraces, temple courtyards, surf breaks and quiet island sunsets.', 'image_url' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=85', 'featured' => 1],
        ['id' => 6, 'name' => 'Dubai', 'country' => 'United Arab Emirates', 'region' => 'Middle East', 'description' => 'Pair desert horizons and Arabian hospitality with design-forward hotels and city-night energy.', 'image_url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1200&q=85', 'featured' => 0],
        ['id' => 7, 'name' => 'Kyoto', 'country' => 'Japan', 'region' => 'East Asia', 'description' => 'A thoughtful journey through lantern-lit lanes, garden sanctuaries and beautifully preserved traditions.', 'image_url' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=1200&q=85', 'featured' => 1],
        ['id' => 8, 'name' => 'Maldives', 'country' => 'Maldives', 'region' => 'Indian Ocean', 'description' => 'Overwater mornings, luminous reefs and slow island days in the heart of the Indian Ocean.', 'image_url' => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=1200&q=85', 'featured' => 0],
    ];
}

function site_packages(): array
{
    $destinations = [];
    foreach (site_destinations() as $destination) {
        $destinations[$destination['id']] = $destination;
    }

    $packages = [
        ['id' => 1, 'destination_id' => 1, 'name' => 'Tea Trails & Mountain Air', 'duration_days' => 4, 'price' => 389.00, 'description' => 'A restful hill-country escape with a tea estate stay, scenic railway ride and guided garden walk.', 'featured' => 1],
        ['id' => 2, 'destination_id' => 2, 'name' => 'Kerala, Slowly', 'duration_days' => 6, 'price' => 649.00, 'description' => 'A private backwater cruise, a spice garden visit and two nights among the Western Ghats.', 'featured' => 1],
        ['id' => 3, 'destination_id' => 3, 'name' => 'Paris in Good Company', 'duration_days' => 5, 'price' => 879.00, 'description' => 'Neighbourhood-led city days, a Seine evening cruise and time to wander at your own pace.', 'featured' => 1],
        ['id' => 4, 'destination_id' => 4, 'name' => 'Alpine Grand Journey', 'duration_days' => 7, 'price' => 1499.00, 'description' => 'Panoramic rail journeys, a lakeside stay and unhurried time in the Swiss mountain villages.', 'featured' => 1],
        ['id' => 5, 'destination_id' => 5, 'name' => 'Island Rituals of Bali', 'duration_days' => 6, 'price' => 999.00, 'description' => 'Rice terrace sunrise, a private villa, temple visits and a coast-to-coast island day.', 'featured' => 0],
        ['id' => 6, 'destination_id' => 7, 'name' => 'The Kyoto Edit', 'duration_days' => 5, 'price' => 1129.00, 'description' => 'A considered introduction to Kyoto with garden visits, a tea experience and boutique stays.', 'featured' => 0],
    ];

    foreach ($packages as &$package) {
        $destination = $destinations[$package['destination_id']];
        $package['destination_name'] = $destination['name'];
        $package['country'] = $destination['country'];
        $package['image_url'] = $destination['image_url'];
    }
    unset($package);

    return $packages;
}
