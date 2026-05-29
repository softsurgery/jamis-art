<?php

session_start();

$isLoggedIn = isset($_SESSION['user_id']);

require_once __DIR__ . '/../../controllers/LocationController.php';
require_once __DIR__ . '/../../controllers/ArtTypeController.php';

$locationController = new LocationController();
$artTypeController = new ArtTypeController();

if (isset($_GET['type'])) {
    $artTypeId = $_GET['type'] ?? null;
    $artType = $artTypeId ? $artTypeController->getById($artTypeId) : null;
    $locations = $artTypeId ? $locationController->getAllByArtType($artTypeId) : [];
} else {
    $locations = $locationController->getAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map</title>
    <!-- Include Leaflet CSS -->
    <script src="../../assets/js/tailwind.js"></script>
    <script src="../../assets/styles.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            z-index: 0;
            height: 100vh;
            width: 100%;
        }

        .map-header {
            position: absolute;
            top: 10px;
            left: 50px;
            z-index: 10;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            font-family: sans-serif;
        }
    </style>
</head>

<body>
    <nav class="fixed top-0 left-0 w-full z-50 glass">
        <?php
        require_once __DIR__ . '/../../components/landing/nav.php';
        echo landingNavLayout('map', $isLoggedIn, '../../', false);
        ?>
    </nav>
    <?php if (isset($artTypeId) && !empty($locations)): ?>
        <div class="map-header">
            <h2> <?php echo htmlspecialchars($artType ? $artType->getLabel() : 'Unknown Art Type'); ?> Map</h2>
        </div>
    <?php endif; ?>

    <div id="map"></div>

    <!-- Include Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        var map = L.map('map').setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var locations = <?php echo json_encode($locations); ?>;

        if (locations && locations.length > 0) {
            var bounds = [];
            locations.forEach(function (loc) {
                if (loc.latitude && loc.longitude) {
                    var lat = parseFloat(loc.latitude);
                    var lng = parseFloat(loc.longitude);

                    var marker = L.marker([lat, lng]).addTo(map);

                    marker.bindPopup(`<b class="text-red-500">${loc.label}</b><br>${loc.description}`);

                    marker.bindTooltip(loc.label, {
                        permanent: true,
                        direction: 'top'
                    });
                    bounds.push([lat, lng]);
                }
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds);
            }
        } else {
            // Set some default view if no locations are found
            map.setView([48.8566, 2.3522], 3);
        }
    </script>
</body>

</html>