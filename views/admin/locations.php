<?php
session_start();
require_once __DIR__ . '/../../lib/authHelper.php';
require_once __DIR__ . '/../../controllers/LocationController.php';
require_once __DIR__ . '/../../controllers/ArtTypeController.php';

requireAdmin();

$locationController = new LocationController();
$artTypeController = new ArtTypeController();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            $location = new Location(null, $_POST['latitude'], $_POST['longitude'], $_POST['label'], $_POST['description'], $_POST['artTypeId']);
            $locationController->save($location);
            header("Location: " . $_SERVER['PHP_SELF'] . "?type=" . $_POST['artTypeId']);
            exit;
        } elseif ($action === 'delete') {
            $locationController->delete($_POST['id']);
            header("Location: " . $_SERVER['PHP_SELF'] . (isset($_POST['filterArtTypeId']) ? "?type=" . $_POST['filterArtTypeId'] : ""));
            exit;
        }
    }
}

$artTypes = $artTypeController->getAll();
$selectedArtTypeId = isset($_GET['type']) ? $_GET['type'] : null;

if ($selectedArtTypeId) {
    $locations = $locationController->getAllByArtType($selectedArtTypeId);
} else {
    $locations = $locationController->getAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Management</title>
    <script src="../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: 100%;
            width: 100%;
            border-radius: 0.5rem;
            z-index: 10;
        }
    </style>
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden">
    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/nav.php";
                echo navLayout("locations", "../../");
                ?>
            </nav>
        </div>
        <main class="flex flex-col flex-1 overflow-hidden w-full">
            <header class="p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/header.php";
                echo headerLayout();
                ?>
            </header>
            <div class="overflow-auto container mx-auto p-8 bg-gray-50 flex-1 flex flex-col relative">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Map & Locations</h1>
                        <p class="mt-1 text-sm text-gray-500">Pin locations for a selected art type.</p>
                    </div>
                    <form method="GET" action="" class="flex items-center gap-4">
                        <label for="type" class="text-sm font-medium text-gray-700">Filter by Art Type:</label>
                        <select name="type" id="type" onchange="this.form.submit()"
                            class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 bg-white border">
                            <option value="">-- Select Art Type --</option>
                            <?php foreach ($artTypes as $artType): ?>
                                <option value="<?= htmlspecialchars($artType['id']) ?>"
                                    <?= $selectedArtTypeId == $artType['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($artType['label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <div
                    class="mt-4 flex-1 bg-white border border-gray-200 rounded-lg shadow-sm relative p-2 min-h-[500px]">
                    <?php if ($selectedArtTypeId): ?>
                        <div id="map"></div>
                    <?php else: ?>
                        <div class="flex h-full items-center justify-center text-gray-500">
                            Please select an Art Type to start pinning locations.
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </section>

    <!-- Modal for adding location -->
    <div id="locationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
        <div class="bg-white rounded-lg p-6 w-[400px] shadow-xl">
            <h2 class="text-lg font-bold mb-4">Add New Location</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="artTypeId" value="<?= htmlspecialchars($selectedArtTypeId ?? '') ?>">
                <input type="hidden" name="latitude" id="latInput">
                <input type="hidden" name="longitude" id="lngInput">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Label</label>
                    <input type="text" name="label" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Save
                        Pin</button>
                </div>
            </form>
        </div>
    </div>
    <script src="../../assets/js/lucide.js"></script>
    <script>
        lucide.createIcons();
    </script>


    <?php if ($selectedArtTypeId): ?>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="">
            </script>
        <script>
            var map = L.map('map').setView([0, 0], 2);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var locations = <?= json_encode($locations) ?>;
            var bounds = [];

            // Add existing locations to map
            if (locations && locations.length > 0) {
                locations.forEach(function (loc) {
                    if (loc.latitude && loc.longitude) {
                        var lat = parseFloat(loc.latitude);
                        var lng = parseFloat(loc.longitude);

                        var marker = L.marker([lat, lng]).addTo(map);

                        // Create popup with delete button
                        var popupContent = `
                        <div class="font-sans text-sm">
                            <b class="text-indigo-600">${loc.label}</b><br>
                            <p class="mt-1 mb-2 text-gray-600">${loc.description || ''}</p>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="${loc.id}">
                                <input type="hidden" name="filterArtTypeId" value="<?= htmlspecialchars($selectedArtTypeId) ?>">
                                <button type="submit" class="text-xs bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 transition">Delete Pin</button>
                            </form>
                        </div>
                    `;

                        marker.bindPopup(popupContent);
                        marker.bindTooltip(loc.label, {
                            permanent: false,
                            direction: 'top'
                        });
                        bounds.push([lat, lng]);
                    }
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds);
                }
            } else {
                // Default view if no locations
                map.setView([36.78585, 10.17068], 14); // Example: Morocco/General view
            }

            var newMarker = null;

            // Open modal on map click
            map.on('click', function (e) {
                // Remove previous unsaved marker if any
                if (newMarker) {
                    map.removeLayer(newMarker);
                }

                // Create temporary marker
                newMarker = L.marker(e.latlng, { opacity: 0.6 }).addTo(map);

                // Populate hidden inputs
                document.getElementById('latInput').value = e.latlng.lat;
                document.getElementById('lngInput').value = e.latlng.lng;

                // Show modal
                document.getElementById('locationModal').classList.remove('hidden');
            });

            function closeModal() {
                document.getElementById('locationModal').classList.add('hidden');
                if (newMarker) {
                    map.removeLayer(newMarker);
                    newMarker = null;
                }
            }
        </script>
    <?php endif; ?>
</body>

</html>