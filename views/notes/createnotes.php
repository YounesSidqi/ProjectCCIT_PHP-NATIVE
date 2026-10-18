<?php
    $currentPage = 'notes';
    $navLayoutPath = __DIR__ . '/../layouts/nav_layout.php';
    if (!file_exists($navLayoutPath)) {
        $navLayoutPath = __DIR__ . '/../layouts/nav_layout.php';
    }

    if (!isset($plans)) {
        require_once __DIR__ . '/../../models/models.php';
        $plans = getAllPlans(defined('CURRENT_USER_ID') ? CURRENT_USER_ID : 1);
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

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #E0405C;
            box-shadow: 0 0 0 3px rgba(224, 64, 92, 0.12);
        }

        .upload-zone {
            transition: all 0.2s ease;
            border: 2px dashed #e5e7eb;
        }
        .upload-zone:hover, .upload-zone.drag-over {
            border-color: #E0405C;
            background: rgba(224, 64, 92, 0.03);
        }

        .save-btn {
            transition: all 0.2s cubic-bezier(.4,0,.2,1);
        }
        .save-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 30px rgba(224, 64, 92, 0.35);
        }
        .save-btn:active { transform: translateY(0); }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #E0405C; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    <!-- ===== SIDEBAR ===== -->
    <?php require $navLayoutPath; ?>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <a href="index.php?page=allnotes"
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
                <a href="index.php?page=allnotes"
                   class="text-sm font-medium text-gray-400 hover:text-red-500 px-4 py-2 rounded-xl hover:bg-red-50 transition-all duration-200">
                    Discard
                </a>
                <button type="button" onclick="document.getElementById('noteForm').requestSubmit()"
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

                <form id="noteForm" action="index.php?page=store_note" method="POST" class="space-y-6">

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
                            required
                        >
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
                            required
                        ></textarea>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-end gap-3">
                            <a href="index.php?page=allnotes"
                                class="px-6 py-3 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors">
                                Cancel
                            </a>
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

</body>
</html>
