<?php
    $currentPage = 'notes';
    $navLayoutPath = __DIR__ . '/../layouts/navlayout.php';
    if (!file_exists($navLayoutPath)) {
        $navLayoutPath = __DIR__ . '/../layouts/nav_layout.php';
    }

    if (!isset($plans)) {
        require_once __DIR__ . '/../../models/models.php';
        $plans = getAllPlans($_SESSION['user_id'] ?? 1);
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Note — NoteSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#E0405C',
                        'primary-light': '#FDE8ED',
                        'primary-dark': '#C0294A',
                        sidebar: '#1B1B26',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Sidebar */
        .sidebar-link { transition: all 0.18s ease; }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(224, 64, 92, 0.15);
            color: #f09bae;
        }
        .sidebar-link.active { border-left: 3px solid #E0405C; }

        /* Input focus ring */
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #E0405C;
            box-shadow: 0 0 0 3px rgba(224, 64, 92, 0.12);
        }

        /* Upload zone */
        .upload-zone {
            transition: all 0.2s ease;
            border: 2px dashed #e5e7eb;
        }
        .upload-zone:hover, .upload-zone.drag-over {
            border-color: #E0405C;
            background: rgba(224, 64, 92, 0.03);
        }

        /* Tag chip */
        .tag-chip {
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .tag-chip:hover { opacity: 0.85; transform: scale(0.97); }
        .tag-chip.selected { ring: 2px; }

        /* Save button */
        .save-btn {
            transition: all 0.2s cubic-bezier(.4,0,.2,1);
        }
        .save-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 30px rgba(224, 64, 92, 0.35);
        }
        .save-btn:active { transform: translateY(0); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #E0405C; }

        /* Image preview */
        #imagePreviewWrap { transition: all 0.3s ease; }

        /* Char counter pulse */
        @keyframes pulse-red {
            0%, 100% { color: #E0405C; }
            50% { color: #e05a77; }
        }
        .char-warn { animation: pulse-red 1s infinite; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    <!-- ===== SIDEBAR (shared layout) ===== -->
    <?php require $navLayoutPath; ?>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <a href="/ProjectCCIT_PHP_NATIVE/index.php?page=allnotes"
                   class="flex items-center gap-2 text-gray-400 hover:text-primary text-sm font-medium transition-colors group">
                    <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Your Notes
                </a>
                <span class="text-gray-200">/</span>
                <span class="text-sm font-semibold text-gray-800">Add New Note</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" id="discardBtn"
                   class="text-sm font-medium text-gray-400 hover:text-red-500 px-4 py-2 rounded-xl hover:bg-red-50 transition-all duration-200">
                    Discard
                </button>
                <button type="button" id="saveBtn"
                   class="save-btn flex items-center gap-2 bg-primary text-white text-sm font-semibold px-6 py-2.5 rounded-xl shadow-lg shadow-primary/25">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                    </svg>
                    Save to Note
                </button>
            </div>
        </header>

        <!-- Scrollable form area -->
        <main class="flex-1 overflow-y-auto px-8 py-8">
            <div class="max-w-4xl mx-auto">

                <!-- Page heading -->
                <div class="mb-8">
                    <h1 class="text-2xl font-extrabold text-gray-900">Add Your Note</h1>
                    <p class="text-sm text-gray-400 mt-1">Write something here — your ideas matter ✍️</p>
                </div>

                <form id="noteForm" action="/ProjectCCIT_PHP_NATIVE/index.php?page=store_note" method="POST" enctype="multipart/form-data" class="space-y-6">

                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Note Title <span class="text-primary">*</span></label>
                        <input
                            type="text"
                            id="noteTitle"
                            name="title"
                            placeholder="Enter a descriptive title..."
                            maxlength="100"
                            class="form-input w-full bg-white border border-gray-200 rounded-2xl px-5 py-3.5 text-sm text-gray-800 placeholder-gray-400 transition-all duration-200 font-medium"
                            oninput="updateTitleCount()"
                            required
                        >
                        <div class="flex justify-end mt-1.5">
                            <span id="titleCount" class="text-xs text-gray-300">0 / 100</span>
                        </div>
                    </div>

                    <!-- Link to Plan (Optional FK) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Link to Plan <span class="text-gray-300 font-normal normal-case">(optional)</span></label>
                        <select name="plan_id" class="form-input w-full bg-white border border-gray-200 rounded-2xl px-5 py-3.5 text-sm text-gray-800 transition-all duration-200 font-medium">
                            <option value="">None / Standalone Note</option>
                            <?php if (!empty($plans)): ?>
                                <?php foreach ($plans as $p): ?>
                                    <option value="<?= $p['plan_id'] ?>"><?= htmlspecialchars($p['title']) ?> (<?= htmlspecialchars($p['plan_date'] ?? '-') ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Note Content -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Note Content <span class="text-primary">*</span></label>
                        <textarea
                            id="noteContent"
                            name="content"
                            placeholder="Write your note here... let your thoughts flow freely."
                            rows="10"
                            maxlength="5000"
                            class="form-textarea w-full bg-white border border-gray-200 rounded-2xl px-5 py-4 text-sm text-gray-800 placeholder-gray-400 resize-none transition-all duration-200 leading-relaxed"
                            oninput="updateContentCount()"
                            required
                        ></textarea>
                        <div class="flex items-center justify-between mt-1.5">
                            <span class="text-xs text-gray-300">Supports plain text</span>
                            <span id="contentCount" class="text-xs text-gray-300">0 / 5000</span>
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Attach Image <span class="text-gray-300 font-normal normal-case">(optional)</span></label>

                        <!-- Upload zone -->
                        <div id="uploadZone"
                             class="upload-zone rounded-2xl p-6 flex items-center gap-5 bg-white cursor-pointer"
                             onclick="document.getElementById('imageInput').click()"
                             ondragover="handleDragOver(event)"
                             ondragleave="handleDragLeave(event)"
                             ondrop="handleDrop(event)">
                            <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">
                                    <span class="text-primary font-semibold cursor-pointer hover:underline">Click here</span>
                                    <span class="font-medium"> to add your image</span>
                                    <span class="text-gray-400"> or drag & drop</span>
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">Supported format: PNG, JPG, JPEG · Max 5 MB</p>
                            </div>
                            <input type="file" id="imageInput" name="image" class="hidden" accept="image/png, image/jpg, image/jpeg" onchange="previewImage(event)">
                        </div>

                        <!-- Image preview (hidden by default) -->
                        <div id="imagePreviewWrap" class="hidden mt-4 relative">
                            <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-sm bg-white p-3 flex items-start gap-4">
                                <img id="imagePreview" src="" alt="Preview" class="w-28 h-20 object-cover rounded-xl flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p id="imageName" class="text-sm font-semibold text-gray-700 truncate"></p>
                                    <p id="imageSize" class="text-xs text-gray-400 mt-0.5"></p>
                                    <button type="button" onclick="removeImage()"
                                        class="mt-2 text-xs text-red-400 hover:text-red-600 font-medium flex items-center gap-1 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                        Remove image
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" onclick="window.location.href='/ProjectCCIT_PHP_NATIVE/index.php?page=allnotes'"
                                class="px-6 py-3 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors">
                                Cancel
                            </button>
                            <!-- Save button (bottom) -->
                            <button type="submit" id="saveBtnBottom"
                               class="save-btn flex items-center gap-2 bg-primary text-white text-sm font-semibold px-8 py-3 rounded-xl shadow-lg shadow-primary/25">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                                </svg>
                                Save to Note
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <script>
        // ── Character counters ──────────────────────────────────────
        function updateTitleCount() {
            const el  = document.getElementById('noteTitle');
            const cnt = document.getElementById('titleCount');
            const len = el.value.length;
            cnt.textContent = `${len} / 100`;
            cnt.classList.toggle('char-warn', len >= 90);
        }

        function updateContentCount() {
            const el  = document.getElementById('noteContent');
            const cnt = document.getElementById('contentCount');
            const len = el.value.length;
            cnt.textContent = `${len} / 5000`;
            cnt.classList.toggle('char-warn', len >= 4800);
        }

        // ── Image upload / preview ──────────────────────────────────
        function previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            showPreview(file);
        }

        function showPreview(file) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imageName').textContent = file.name;
                document.getElementById('imageSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
                document.getElementById('imagePreviewWrap').classList.remove('hidden');
                document.getElementById('uploadZone').classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        function removeImage() {
            document.getElementById('imageInput').value = '';
            document.getElementById('imagePreviewWrap').classList.add('hidden');
            document.getElementById('uploadZone').classList.remove('hidden');
        }

        // ── Drag & drop ─────────────────────────────────────────────
        function handleDragOver(e) {
            e.preventDefault();
            document.getElementById('uploadZone').classList.add('drag-over');
        }

        function handleDragLeave(e) {
            document.getElementById('uploadZone').classList.remove('drag-over');
        }

        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('uploadZone').classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                showPreview(file);
                const dt = new DataTransfer();
                dt.items.add(file);
                document.getElementById('imageInput').files = dt.files;
            }
        }

        // ── Keyboard shortcut: Ctrl+S ───────────────────────────────
        document.addEventListener('keydown', e => {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.getElementById('noteForm').requestSubmit();
            }
        });

        // ── Discard → back to list ──────────────────────────────────
        document.getElementById('discardBtn').addEventListener('click', () => {
            if (confirm('Discard changes and go back?')) {
                window.location.href = '/ProjectCCIT_PHP_NATIVE/index.php?page=allnotes';
            }
        });

        // ── Top Save button submits form ────────────────────────────
        document.getElementById('saveBtn').addEventListener('click', () => {
            document.getElementById('noteForm').requestSubmit();
        });
    </script>

</body>
</html>
