<?php
    $currentPage = 'notes';
    $navLayoutPath = __DIR__ . '/../layouts/nav_layout.php';
    if (!file_exists($navLayoutPath)) {
        $navLayoutPath = __DIR__ . '/../layouts/nav_layout.php';
    }

    if (!isset($note) && !empty($_GET['id'])) {
        require_once __DIR__ . '/../../models/models.php';
        $note = getNoteById((int) $_GET['id']);
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
    <title>Edit Note — NoteSpace</title>
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
                        surface: '#F7F7FA',
                    },
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }

        .dropzone {
            border: 2px dashed #e0e0e0;
            transition: all 0.2s ease;
        }
        .dropzone:hover, .dropzone.dragover {
            border-color: #E0405C;
            background: #FDE8ED/30;
        }
    </style>
</head>
<body class="bg-surface flex h-screen overflow-hidden">

    <?php require $navLayoutPath; ?>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="bg-white border-b border-gray-100/80 px-8 py-4 flex items-center gap-4 flex-shrink-0">
            <a href="index.php?page=detailnotes&id=<?= $note ? $note['notes_id'] : '' ?>" class="text-gray-400 hover:text-primary transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </a>
            <div>
                <p class="text-xs text-gray-400 font-medium">Note : <?= $note ? htmlspecialchars(substr($note['title'], 0, 30)) . '...' : '' ?></p>
                <h1 class="text-base font-bold text-gray-900">Edit Your Note</h1>
            </div>
        </header>

        <?php if ($note): ?>
        <main class="flex-1 overflow-y-auto px-8 py-6">
            <div class="max-w-2xl mx-auto">
                <form action="index.php?page=update_note" method="POST"
                      class="bg-white rounded-2xl border border-gray-100/80 p-8 space-y-5 shadow-sm">

                    <input type="hidden" name="notes_id" value="<?= $note['notes_id'] ?>">

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Note Title <span class="text-primary">*</span></label>
                        <input type="text" name="title" required value="<?= htmlspecialchars($note['title']) ?>"
                               class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Content <span class="text-primary">*</span></label>
                        <textarea name="content" rows="8" required
                                  class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm leading-relaxed focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all resize-none"><?= htmlspecialchars($note['content']) ?></textarea>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Linked Plan (optional)</label>
                        <select name="plan_id" class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all bg-white">
                            <option value="">No linked plan</option>
                            <?php foreach ($plans as $p): ?>
                                <option value="<?= $p['plan_id'] ?>" <?= ($note['plan_id'] == $p['plan_id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['title']) ?> (<?= htmlspecialchars($p['plan_date'] ?? '-') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="pt-3 flex gap-3">
                        <a href="index.php?page=detailnotes&id=<?= $note['notes_id'] ?>"
                           class="w-1/3 text-center border border-gray-200 text-gray-600 font-semibold py-3 rounded-xl hover:bg-gray-50 transition-all text-sm">
                            Cancel
                        </a>
                        <button type="submit" class="w-2/3 bg-primary hover:bg-primary-dark text-white text-sm font-semibold py-3 rounded-xl transition-all shadow-lg shadow-primary/20">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
        <?php else: ?>
        <main class="flex-1 flex items-center justify-center">
            <p class="text-gray-400 text-sm">Note not found.</p>
        </main>
        <?php endif; ?>
    </div>
</body>
</html>
