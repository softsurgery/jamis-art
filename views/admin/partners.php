<?php
session_start();
require_once __DIR__ . '/../../lib/authHelper.php';
require_once __DIR__ . '/../../controllers/PartnerController.php';
require_once __DIR__ . '/../../controllers/UploadController.php';
require_once __DIR__ . '/../../constants/upload-group-map.php';

requireAdmin();

$controller = new PartnerController();
$partnerGroupId = getGroupIdByName("partners");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            $logoId = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $mime = mime_content_type($_FILES['logo']['tmp_name']);
                if (strpos($mime, 'image/') === 0) {
                    $upload = UploadController::uploadFile($_FILES['logo'], false, $partnerGroupId);
                    $logoId = $upload->getId();
                }
            }
            $partner = new Partner(null, $_POST['label'], $logoId);
            $controller->save($partner);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } elseif ($action === 'update') {
            $logoId = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $mime = mime_content_type($_FILES['logo']['tmp_name']);
                if (strpos($mime, 'image/') === 0) {
                    $upload = UploadController::uploadFile($_FILES['logo'], false, $partnerGroupId);
                    $logoId = $upload->getId();
                }
            } else {
                $existing = $controller->getById($_POST['id']);
                if ($existing)
                    $logoId = $existing->getLogoId();
            }
            $partner = new Partner($_POST['id'], $_POST['label'], $logoId);
            $controller->update($_POST['id'], $partner);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } elseif ($action === 'delete') {
            $controller->delete($_POST['id']);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

$partners = $controller->getAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partners Management</title>
    <script src="../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden">
    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/nav.php";
                echo navLayout("partners", "../../");
                ?>
            </nav>
        </div>
        <main class="flex flex-col flex-1 overflow-hidden w-full relative group/main">
            <header class="p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/header.php";
                echo headerLayout();
                ?>
            </header>
            <div class="overflow-auto container mx-auto p-8 bg-gray-50 flex-1 relative">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Partners Management</h1>
                        <p class="mt-1 text-sm text-gray-500">Manage all registered partners and their details.</p>
                    </div>
                    <button onclick="openSheet('create')"
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                        + Add New Partner
                    </button>
                </div>

                <!-- Table -->
                <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-900">
                            <tr>
                                <th class="px-6 py-4 font-medium">ID</th>
                                <th class="px-6 py-4 font-medium">Logo</th>
                                <th class="px-6 py-4 font-medium">Label</th>
                                <th class="px-6 py-4 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($partners as $partner): ?>
                                <?php $logoUrl = $partner->getLogoPath() ? '../../' . $partner->getLogoPath() : ''; ?>
                                <tr class="hover:bg-gray-50 transition relative">
                                    <td class="px-6 py-4"><?= htmlspecialchars($partner->getId()) ?></td>
                                    <td class="px-6 py-4">
                                        <?php if ($logoUrl): ?>
                                            <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Partner Logo"
                                                class="w-10 h-10 rounded-lg object-contain border border-gray-200 shadow-sm bg-gray-100">
                                        <?php else: ?>
                                            <div
                                                class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200 shadow-sm">
                                                <i data-lucide="image" class="w-4 h-4 text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($partner->getLabel()) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-block relative">
                                            <button onclick="toggleDropdown(<?= $partner->getId() ?>)"
                                                class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                            </button>
                                            <!-- dropdown -->
                                            <div id="dropdown-<?= $partner->getId() ?>"
                                                class="hidden absolute right-0 top-full mt-1 z-20 w-36 bg-white rounded-lg shadow-lg border border-gray-100 py-1 text-left overflow-hidden">
                                                <button
                                                    onclick="openSheet('update', <?= $partner->getId() ?>, '<?= htmlspecialchars(addslashes($partner->getLabel())) ?>', '<?= htmlspecialchars(addslashes($logoUrl)) ?>')"
                                                    class="w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 text-left flex items-center gap-2 transition">
                                                    <i data-lucide="edit" class="w-4 h-4 text-gray-400"></i> Edit
                                                </button>
                                                <button onclick="openDialog(<?= $partner->getId() ?>)"
                                                    class="w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 text-left flex items-center gap-2 transition">
                                                    <i data-lucide="trash-2" class="w-4 h-4 text-red-400"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($partners)): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        No partners found. Create one to get started.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </main>
    </section>

    <!-- Side Sheet for Create/Update -->
    <div id="side-sheet"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 hidden justify-end transition-opacity">
        <div class="bg-white w-[400px] h-full shadow-2xl p-6 flex flex-col translate-x-full transition-transform duration-300 ease-out"
            id="sheet-content">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                <h2 id="sheet-title" class="text-xl font-semibold text-gray-900">Add Partner</h2>
                <button onclick="closeSheet()"
                    class="p-2 text-gray-400 hover:text-gray-800 hover:bg-gray-100 rounded-full transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form method="POST" id="sheet-form" enctype="multipart/form-data" class="flex flex-col gap-5 flex-1">
                <input type="hidden" name="action" id="form-action" value="create">
                <input type="hidden" name="id" id="form-id" value="">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Label / Name</label>
                    <input type="text" name="label" id="form-label" required placeholder="e.g. Acme Corp"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Logo</label>
                    <img id="form-logo-preview" src="" alt="Preview"
                        class="hidden mb-3 w-16 h-16 rounded-lg object-contain border border-gray-200 shadow-sm bg-gray-100">
                    <input type="file" name="logo" id="form-logo" accept="image/*"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                </div>

                <div class="mt-auto pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white rounded-lg py-2.5 font-medium hover:bg-indigo-700 transition focus:ring-4 focus:ring-indigo-600/20">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <div id="delete-dialog"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm scale-95 opacity-0 transition-all duration-200"
            id="dialog-content">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Delete Partner?</h2>
            <p class="text-gray-500 text-sm mb-6 leading-relaxed">Are you sure you want to delete this partner? This
                action cannot be undone.</p>

            <form method="POST" class="flex items-center justify-end gap-3">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete-id" value="">
                <button type="button" onclick="closeDialog()"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus:ring-4 focus:ring-gray-200">Cancel</button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition focus:ring-4 focus:ring-red-600/20">Delete</button>
            </form>
        </div>
    </div>

    <script src="../../assets/js/lucide.js"></script>
    <script>
        lucide.createIcons();

        // Dropdown Logic
        function toggleDropdown(id) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el.id !== 'dropdown-' + id) el.classList.add('hidden');
            });
            document.getElementById('dropdown-' + id).classList.toggle('hidden');
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.inline-block.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
            }
        });

        // Sheet Logic
        const sheet = document.getElementById('side-sheet');
        const sheetContent = document.getElementById('sheet-content');

        function openSheet(action, id = '', label = '', logoUrl = '') {
            document.getElementById('form-action').value = action;
            document.getElementById('form-id').value = id;
            document.getElementById('form-label').value = label;
            document.getElementById('form-logo').value = '';

            const logoPreview = document.getElementById('form-logo-preview');
            if (logoUrl) {
                logoPreview.src = logoUrl;
                logoPreview.classList.remove('hidden');
            } else {
                logoPreview.removeAttribute('src');
                logoPreview.classList.add('hidden');
            }

            document.getElementById('sheet-title').innerText = action === 'create' ? 'Add Partner' : 'Edit Partner';

            sheet.classList.remove('hidden');
            sheet.classList.add('flex');

            // Allow display:flex to apply before translating
            requestAnimationFrame(() => {
                sheetContent.classList.remove('translate-x-full');
            });

            // Close dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
        }

        function closeSheet() {
            sheetContent.classList.add('translate-x-full');
            setTimeout(() => {
                sheet.classList.add('hidden');
                sheet.classList.remove('flex');
            }, 300);
        }

        // Click outside sheet to close
        sheet.addEventListener('click', (e) => {
            if (e.target === sheet) closeSheet();
        });

        // Dialog Logic
        const dialog = document.getElementById('delete-dialog');
        const dialogContent = document.getElementById('dialog-content');

        function openDialog(id) {
            document.getElementById('delete-id').value = id;
            dialog.classList.remove('hidden');
            dialog.classList.add('flex');

            requestAnimationFrame(() => {
                dialogContent.classList.remove('scale-95', 'opacity-0');
                dialogContent.classList.add('scale-100', 'opacity-100');
            });

            // Close dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
        }

        function closeDialog() {
            dialogContent.classList.remove('scale-100', 'opacity-100');
            dialogContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                dialog.classList.add('hidden');
                dialog.classList.remove('flex');
            }, 200);
        }

        // Click outside dialog to close
        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) closeDialog();
        });
    </script>
</body>

</html>
