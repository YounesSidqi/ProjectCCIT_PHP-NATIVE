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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $note ? htmlspecialchars($note['title']) : 'Note Detail' ?> — NoteSpace</title>
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

        .overlay { background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); }
        .modal-panel { animation: slideUp 0.25s ease; }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-surface flex h-screen overflow-hidden">

    <?php require $navLayoutPath; ?>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="bg-white border-b border-gray-100/80 px-8 py-4 flex items-center gap-4 flex-shrink-0">
            <a href="index.php?page=allnotes" class="text-gray-400 hover:text-primary transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </a>
            <div>
                <p class="text-xs text-gray-400 font-medium">Your Notes</p>
                <h1 class="text-base font-bold text-gray-900"><?= $note ? htmlspecialchars($note['title']) : 'Note not found' ?></h1>
            </div>
        </header>

        <?php if ($note): ?>
        <main class="flex-1 overflow-y-auto px-8 py-6">
            <div class="max-w-3xl mx-auto">

                <div class="bg-white rounded-2xl border border-gray-100/80 p-8 shadow-sm">

                    <h2 class="text-xl font-extrabold text-gray-900 mb-4 leading-tight"><?= htmlspecialchars($note['title']) ?></h2>

                    <?php if (!empty($note['plan_title'])): ?>
                        <span class="inline-block text-[10px] bg-red-50 text-primary font-semibold px-3 py-1 rounded-full mb-4">📌 Linked Plan: <?= htmlspecialchars($note['plan_title']) ?></span>
                    <?php endif; ?>

                    <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line mb-6">
                        <?= nl2br(htmlspecialchars($note['content'])) ?>
                    </div>

                    <div class="flex items-center gap-3 pt-5 border-t border-gray-50">
                        <a href="index.php?page=editnotes&id=<?= $note['notes_id'] ?>"
                           class="flex items-center gap-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all shadow-md shadow-primary/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                            </svg>
                            Edit Note
                        </a>
                        <button onclick="openModal('deleteNoteModal')"
                                class="flex items-center gap-2 bg-white border border-red-200 text-primary hover:bg-red-50 text-sm font-semibold px-5 py-2.5 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                            </svg>
                            Delete Note
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Delete Confirmation Modal -->
        <div id="deleteNoteModal" class="hidden fixed inset-0 z-50 overlay flex items-center justify-center">
            <div class="modal-panel bg-white rounded-2xl w-full max-w-sm mx-4 shadow-2xl text-center p-8">
                <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1">Delete this Note?</h3>
                <p class="text-xs text-gray-400 mb-6">This note will be permanently deleted and cannot be recovered.</p>
                <form action="index.php?page=delete_note" method="POST" class="flex items-center justify-center gap-3">
                    <input type="hidden" name="notes_id" value="<?= $note['notes_id'] ?>">
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all">Yes, Delete</button>
                    <button type="button" onclick="closeModal('deleteNoteModal')" class="text-primary border border-primary/30 hover:bg-red-50 text-sm font-semibold px-6 py-2.5 rounded-xl transition-all">Cancel</button>
                </form>
            </div>
        </div>

        <?php else: ?>
        <main class="flex-1 flex items-center justify-center">
            <p class="text-gray-400 text-sm">Note not found.</p>
        </main>
        <?php endif; ?>
    </div>

    <script>
        function openModal(id)  { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        document.querySelectorAll('.overlay').forEach(el => {
            el.addEventListener('click', function(e) { if (e.target === this) closeModal(this.id); });
        });
    </script>
</body>
</html>
