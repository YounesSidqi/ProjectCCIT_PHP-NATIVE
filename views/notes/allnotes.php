<?php
    $currentPage = 'notes';
    // Relative path from views/notes/ up to views/layouts/
    $navLayoutPath = __DIR__ . '/../layouts/navlayout.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Notes — Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#C0294A',
                        'primary-light': '#D94060',
                        'primary-dark': '#A0223E',
                        sidebar: '#1a1a2e',
                        'sidebar-hover': '#16213e',
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
        .sidebar-link {
            transition: all 0.18s ease;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(192, 41, 74, 0.15);
            color: #e87a92;
        }
        .sidebar-link.active {
            border-left: 3px solid #C0294A;
        }

        /* Cards */
        .note-card {
            transition: transform 0.22s cubic-bezier(.4,0,.2,1), box-shadow 0.22s ease;
        }
        .note-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.10);
        }

        /* Filter tabs */
        .filter-btn.active {
            background-color: #C0294A;
            color: white;
            border-color: #C0294A;
        }

        /* Search focus */
        .search-bar:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(192, 41, 74, 0.18);
            border-color: #C0294A;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #C0294A; }

        /* Stat card gradient */
        .stat-gradient-1 { background: linear-gradient(135deg, #C0294A, #e05a77); }
        .stat-gradient-2 { background: linear-gradient(135deg, #6366f1, #818cf8); }
        .stat-gradient-3 { background: linear-gradient(135deg, #0ea5e9, #38bdf8); }

        /* Tag badge */
        .tag-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 999px;
        }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    <!-- ===== SIDEBAR (shared layout) ===== -->
    <?php require $navLayoutPath; ?>




    <!-- ===== MAIN CONTENT ===== -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">Your Notes</h1>
                <p class="text-xs text-gray-400 mt-0.5">Start write your plan — Tuesday, Oct 13, 2026</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Search -->
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                    </svg>
                    <input type="text" id="searchInput" placeholder="Search for your notes..."
                        class="search-bar w-72 bg-gray-100 border border-transparent rounded-xl py-2.5 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 transition-all duration-200"
                        onkeyup="filterNotes()">
                </div>
                <!-- Notifications -->
                <button class="relative w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full"></span>
                </button>
                <!-- Add Note Button -->
                <a href="/ProjectCCIT_PHP_NATIVE/views/notes/createnotes.php"
                   class="flex items-center gap-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 shadow-lg shadow-primary/25 hover:shadow-primary/40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Note
                </a>
            </div>
        </header>

        <!-- Scrollable content area -->
        <main class="flex-1 overflow-y-auto px-8 py-7">

            <!-- Stats Row -->
            <div class="grid grid-cols-3 gap-5 mb-8">
                <div class="stat-gradient-1 rounded-2xl p-5 text-white shadow-lg shadow-primary/20">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-white/80 text-sm font-medium">Total Notes</p>
                        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold">24</p>
                    <p class="text-white/60 text-xs mt-1">+3 this week</p>
                </div>

                <div class="stat-gradient-2 rounded-2xl p-5 text-white shadow-lg shadow-indigo-500/20">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-white/80 text-sm font-medium">Bookmarked</p>
                        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold">7</p>
                    <p class="text-white/60 text-xs mt-1">Saved for later</p>
                </div>

                <div class="stat-gradient-3 rounded-2xl p-5 text-white shadow-lg shadow-sky-500/20">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-white/80 text-sm font-medium">Categories</p>
                        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold">5</p>
                    <p class="text-white/60 text-xs mt-1">Active tags</p>
                </div>
            </div>

            <!-- Filter + count row -->
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2" id="filterTabs">
                    <button onclick="setFilter('recent', this)"
                        class="filter-btn active text-sm font-semibold px-4 py-1.5 rounded-full border border-primary transition-all duration-200">
                        Recent
                    </button>
                    <button onclick="setFilter('oldest', this)"
                        class="filter-btn text-sm font-medium px-4 py-1.5 rounded-full border border-gray-200 text-gray-500 hover:border-gray-300 transition-all duration-200">
                        Oldest
                    </button>
                    <button onclick="setFilter('newest', this)"
                        class="filter-btn text-sm font-medium px-4 py-1.5 rounded-full border border-gray-200 text-gray-500 hover:border-gray-300 transition-all duration-200">
                        Newest
                    </button>
                </div>
                <p class="text-xs text-gray-400 font-medium">Showing <span class="text-gray-700 font-bold" id="noteCount">4</span> notes</p>
            </div>

            <!-- Notes Grid (4 columns) -->
            <div class="grid grid-cols-4 gap-5" id="notesGrid">

                <!-- Note Card 1 -->
                <a href="#" class="note-card bg-white border border-gray-100 rounded-2xl p-5 flex flex-col gap-2 cursor-pointer shadow-sm" data-title="The Data Relationship In E-learning">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">9/14/2026</span>
                        <span class="tag-badge bg-red-50 text-red-400">Research</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 leading-snug mt-1">The Data Relationship In E-learning</h3>
                    <p class="text-xs text-gray-400 leading-relaxed flex-1">Data relation ship is very important for modern e-learning platforms...</p>
                    <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-300">2 min read</span>
                        <svg class="w-4 h-4 text-gray-300 hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                        </svg>
                    </div>
                </a>

                <!-- Note Card 2 -->
                <a href="#" class="note-card bg-white border border-gray-100 rounded-2xl p-5 flex flex-col gap-2 cursor-pointer shadow-sm" data-title="What We Need For?">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">9/10/2026</span>
                        <span class="tag-badge bg-indigo-50 text-indigo-400">Planning</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 leading-snug mt-1">What We Need For?</h3>
                    <p class="text-xs text-gray-400 leading-relaxed flex-1">Data relation ship is very important for building strong foundations...</p>
                    <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-300">3 min read</span>
                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                        </svg>
                    </div>
                </a>

                <!-- Note Card 3 -->
                <a href="#" class="note-card bg-white border border-gray-100 rounded-2xl p-5 flex flex-col gap-2 cursor-pointer shadow-sm" data-title="How to be useful">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">9/9/2026</span>
                        <span class="tag-badge bg-emerald-50 text-emerald-400">Tips</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 leading-snug mt-1">How to be useful</h3>
                    <p class="text-xs text-gray-400 leading-relaxed flex-1">Here are some tips that we can apply in our daily life to be more productive...</p>
                    <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-300">4 min read</span>
                        <svg class="w-4 h-4 text-gray-300 hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                        </svg>
                    </div>
                </a>

                <!-- Note Card 4 (with image) -->
                <a href="#" class="note-card bg-white border border-gray-100 rounded-2xl p-5 flex flex-col gap-2 cursor-pointer shadow-sm" data-title="Record of Organization Meeting">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">9/14/2026</span>
                        <span class="tag-badge bg-orange-50 text-orange-400">Meeting</span>
                    </div>
                    <!-- Image placeholder -->
                    <div class="w-full h-28 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center mt-1">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 leading-snug">Record of Organization Meeting</h3>
                    <p class="text-xs text-gray-400 leading-relaxed flex-1">Data relation ship is very important in every organization...</p>
                    <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-300">5 min read</span>
                        <svg class="w-4 h-4 text-gray-300 hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                        </svg>
                    </div>
                </a>

                <!-- Note Card 5 -->
                <a href="#" class="note-card bg-white border border-gray-100 rounded-2xl p-5 flex flex-col gap-2 cursor-pointer shadow-sm" data-title="Weekly Goals Review">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">9/8/2026</span>
                        <span class="tag-badge bg-sky-50 text-sky-400">Goals</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 leading-snug mt-1">Weekly Goals Review</h3>
                    <p class="text-xs text-gray-400 leading-relaxed flex-1">Reviewing this week's goals and planning ahead for next week's priorities...</p>
                    <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-300">3 min read</span>
                        <svg class="w-4 h-4 text-gray-300 hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                        </svg>
                    </div>
                </a>

                <!-- Note Card 6 -->
                <a href="#" class="note-card bg-white border border-gray-100 rounded-2xl p-5 flex flex-col gap-2 cursor-pointer shadow-sm" data-title="Design System Notes">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">9/7/2026</span>
                        <span class="tag-badge bg-purple-50 text-purple-400">Design</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 leading-snug mt-1">Design System Notes</h3>
                    <p class="text-xs text-gray-400 leading-relaxed flex-1">Building a consistent design system requires careful thought on tokens...</p>
                    <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-300">6 min read</span>
                        <svg class="w-4 h-4 text-gray-300 hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                        </svg>
                    </div>
                </a>

                <!-- Note Card 7 -->
                <a href="#" class="note-card bg-white border border-gray-100 rounded-2xl p-5 flex flex-col gap-2 cursor-pointer shadow-sm" data-title="Project Kickoff Agenda">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">9/5/2026</span>
                        <span class="tag-badge bg-orange-50 text-orange-400">Meeting</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 leading-snug mt-1">Project Kickoff Agenda</h3>
                    <p class="text-xs text-gray-400 leading-relaxed flex-1">Agenda and notes from the project kickoff meeting with the client...</p>
                    <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-300">4 min read</span>
                        <svg class="w-4 h-4 text-gray-300 hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                        </svg>
                    </div>
                </a>

                <!-- Note Card 8 -->
                <a href="#" class="note-card bg-white border border-gray-100 rounded-2xl p-5 flex flex-col gap-2 cursor-pointer shadow-sm" data-title="Reading List">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">9/3/2026</span>
                        <span class="tag-badge bg-emerald-50 text-emerald-400">Tips</span>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 leading-snug mt-1">Reading List</h3>
                    <p class="text-xs text-gray-400 leading-relaxed flex-1">Books and articles to read this month to improve my knowledge...</p>
                    <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-300">1 min read</span>
                        <svg class="w-4 h-4 text-gray-300 hover:text-primary transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                        </svg>
                    </div>
                </a>

            </div>

            <!-- Empty State -->
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
        function setFilter(filter, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active', 'text-white', 'border-primary');
                b.classList.add('text-gray-500', 'border-gray-200');
            });
            btn.classList.add('active');
            btn.classList.remove('text-gray-500', 'border-gray-200');
        }

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

            document.getElementById('noteCount').textContent = visible;
            document.getElementById('emptyState').classList.toggle('hidden', visible > 0);
            document.getElementById('notesGrid').classList.toggle('hidden', visible === 0);
        }
    </script>

</body>
</html>
