<?php
    $currentPage = 'notes';
    $navLayoutPath = __DIR__ . '/../layouts/nav_layout.php';
    if (!file_exists($navLayoutPath)) {
        $navLayoutPath = __DIR__ . '/../layouts/nav_layout.php';
    }

    if (!isset($notes)) {
        require_once __DIR__ . '/../../models/models.php';
        $notes = getAllNotes(defined('CURRENT_USER_ID') ? CURRENT_USER_ID : 1);
    }
    $totalNotes = count($notes);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Notes — NoteSpace</title>
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
                        'sidebar-hover': '#252538',
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

        .note-card {
            transition: transform 0.22s cubic-bezier(.4,0,.2,1), box-shadow 0.22s ease;
        }
        .note-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.10);
        }

        .filter-btn.active {
            background-color: #E0405C;
            color: white;
            border-color: #E0405C;
        }

        .search-bar:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(224, 64, 92, 0.18);
            border-color: #E0405C;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #E0405C; }

        .tag-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 999px;
        }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    <?php require $navLayoutPath; ?>

    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">Your Notes</h1>
                <p class="text-xs text-gray-400 mt-0.5">Start write your plan — <?= date('l, M j, Y') ?></p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search for your notes..."
                        class="search-bar w-72 bg-gray-100 border border-transparent rounded-xl py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition-all duration-200"
                        onkeyup="filterNotes()">
                </div>
                <a href="index.php?page=createnotes"
                   class="flex items-center gap-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 shadow-lg shadow-primary/25 hover:shadow-primary/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Note
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-8 py-7">

            <!-- Stats Row -->
            <div class="grid grid-cols-2 gap-5 mb-8">
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:border-gray-300 transition-all flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Notes</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1"><?= $totalNotes ?></p>
                        <p class="text-[11px] text-gray-400 mt-1">All notes in storage</p>
                    </div>
                    <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-[#E0405C] flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:border-gray-300 transition-all flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">With Plans</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1"><?= count(array_filter($notes, function($n) { return !empty($n['plan_id']) && $n['plan_id'] > 0; })) ?></p>
                        <p class="text-[11px] text-gray-400 mt-1">Linked to plans</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-500 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.86-3.06a4.5 4.5 0 00-6.364-6.364L4.5 8.257"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Notes Grid -->
            <div class="grid grid-cols-4 gap-5" id="notesGrid">
                <?php if (empty($notes)): ?>
                    <div class="col-span-4 flex flex-col items-center justify-center py-24 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mb-5 shadow-inner">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        </div>
                        <p class="text-base font-bold text-gray-500">No notes yet</p>
                        <p class="text-sm text-gray-400 mt-1">Create your first note to get started!</p>
                        <a href="index.php?page=createnotes" class="mt-4 inline-flex items-center gap-2 bg-primary text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-md">
                            + Add Note
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($notes as $note): ?>
                    <a href="index.php?page=detailnotes&id=<?= $note['notes_id'] ?>"
                       class="note-card bg-white border border-gray-100 rounded-2xl p-5 flex flex-col gap-2 cursor-pointer shadow-sm"
                       data-title="<?= htmlspecialchars($note['title']) ?>">
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 font-medium">Note #<?= $note['notes_id'] ?></span>
                            <?php if (!empty($note['plan_title'])): ?>
                                <span class="tag-badge bg-red-50 text-primary truncate max-w-[120px]">📌 <?= htmlspecialchars($note['plan_title']) ?></span>
                            <?php else: ?>
                                <span class="tag-badge bg-gray-50 text-gray-400">General</span>
                            <?php endif; ?>
                        </div>

                        <h3 class="text-sm font-bold text-gray-900 leading-snug mt-1 line-clamp-1"><?= htmlspecialchars($note['title']) ?></h3>
                        <p class="text-xs text-gray-400 leading-relaxed flex-1 line-clamp-2"><?= htmlspecialchars(substr($note['content'], 0, 80)) ?>...</p>
                        <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-50">
                            <svg class="w-4 h-4 text-gray-300 hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Empty State (search) -->
            <div id="emptyState" class="hidden flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mb-5 shadow-inner">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                </div>
                <p class="text-base font-bold text-gray-400">No notes found</p>
                <p class="text-sm text-gray-300 mt-1">Try a different search keyword</p>
            </div>

        </main>
    </div>

    <script>
        function filterNotes() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('#notesGrid a');
            let visible = 0;

            cards.forEach(card => {
                const title = (card.querySelector('h3')?.textContent || '').toLowerCase();
                const desc  = (card.querySelector('p')?.textContent  || '').toLowerCase();
                const tag   = (card.querySelector('.tag-badge')?.textContent || '').toLowerCase();
                if (title.includes(query) || desc.includes(query) || tag.includes(query)) {
                    card.classList.remove('hidden');
                    visible++;
                } else {
                    card.classList.add('hidden');
                }
            });

            document.getElementById('emptyState').classList.toggle('hidden', visible > 0);
            document.getElementById('notesGrid').classList.toggle('hidden', visible === 0);
        }
    </script>

</body>
</html>
