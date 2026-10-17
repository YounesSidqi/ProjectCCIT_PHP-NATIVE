<?php
    $currentPage = 'dashboard';
    $navLayoutPath = __DIR__ . '/../layouts/navlayout.php';

    if (!isset($user))   $user   = ['name' => 'Dimas Arkana', 'email' => 'dimas@mail.com'];
    if (!isset($tasks))  $tasks  = [];
    if (!isset($recentNotes)) $recentNotes = [];
    if (!isset($totalPlans))  $totalPlans  = 0;

    $greeting = '';
    $hour = (int) date('G');
    if ($hour < 12)      $greeting = 'Good morning';
    elseif ($hour < 17)  $greeting = 'Good afternoon';
    else                 $greeting = 'Good evening';

    $todayDate = date('l, F j');
    $doneTasks = 0;
    $totalTaskCount = count($tasks);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Ritma!</title>
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
                        surface: '#F7F7FA',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
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
        ::-webkit-scrollbar-thumb:hover { background: #C0294A; }

        .task-row { transition: background 0.15s ease; }
        .task-row:hover { background: #fdf2f4; }
        .card-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-lift:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(0,0,0,0.08); }

        .urgency-santuy     { background: #e8f5e9; color: #2e7d32; }
        .urgency-aman       { background: #e3f2fd; color: #1565c0; }
        .urgency-gercep     { background: #fff3e0; color: #e65100; }
        .urgency-mepet      { background: #fce4ec; color: #b71c1c; }

        .importance-baja    { background: #f3e5f5; color: #6a1b9a; }
        .importance-mid     { background: #e0f2f1; color: #00695c; }
        .importance-highkey { background: #fff8e1; color: #f57f17; }
        .importance-harga   { background: #fce4ec; color: #c62828; }

        .quick-btn {
            transition: all 0.2s ease;
            border: 1.5px solid #f0f0f0;
        }
        .quick-btn:hover {
            border-color: #C0294A;
            color: #C0294A;
            background: #fdf2f4;
        }
        .quick-btn:hover svg { color: #C0294A; }

        .mood-btn { transition: transform 0.15s ease; }
        .mood-btn:hover { transform: scale(1.2); }
    </style>
</head>
<body class="bg-surface flex h-screen overflow-hidden">

    <?php require $navLayoutPath; ?>

    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-100/80 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                <p class="text-gray-400 text-xs font-medium"><?= $greeting ?>,</p>
                <h1 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($user['name']) ?></h1>
            </div>
            <div class="flex items-center gap-3">
                <button class="relative w-10 h-10 bg-gray-50 hover:bg-gray-100 rounded-xl flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full"></span>
                </button>
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-pink-400 flex items-center justify-center text-white font-bold text-xs shadow">
                    <?= strtoupper(substr($user['name'], 0, 1) . substr(strstr($user['name'], ' '), 1, 1)) ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-8 py-6">
            <div class="max-w-6xl mx-auto grid grid-cols-12 gap-6">

                <!-- LEFT COLUMN (8 cols) -->
                <div class="col-span-8 space-y-6">

                    <!-- Summary Banner -->
                    <div class="bg-gradient-to-r from-primary via-[#d1365c] to-[#e05a77] rounded-2xl p-6 text-white relative overflow-hidden">
                        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/5 rounded-full"></div>
                        <div class="absolute -right-4 bottom-0 w-24 h-24 bg-white/5 rounded-full"></div>
                        <span class="inline-block bg-white/20 text-xs font-semibold px-3 py-1 rounded-full mb-3"><?= $todayDate ?></span>
                        <h2 class="text-2xl font-extrabold mb-1"><?= $doneTasks ?>/<?= $totalTaskCount ?> Task Done</h2>
                        <p class="text-white/70 text-sm font-medium">"Small steps still move you forward."</p>
                        <div class="mt-3 space-y-1 text-white/60 text-xs">
                            <p>• 5 hours 35 minutes of sleep</p>
                            <p>• You haven't exercise this day</p>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="grid grid-cols-2 gap-4">
                        <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=plans" class="quick-btn bg-white rounded-2xl p-4 flex flex-col items-center gap-2 text-center">
                            <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-gray-700">Add Plan</span>
                        </a>
                        <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=notes" class="quick-btn bg-white rounded-2xl p-4 flex flex-col items-center gap-2 text-center">
                            <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-gray-700">Add Note</span>
                        </a>
                    </div>

                    <!-- Today's Actions -->
                    <div class="bg-white rounded-2xl border border-gray-100/80">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                            <h3 class="text-sm font-bold text-gray-900">Todays Action</h3>
                            <span class="text-xs font-bold text-primary"><?= $doneTasks ?>/<?= $totalTaskCount ?></span>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <?php if (empty($tasks)): ?>
                                <div class="px-6 py-8 text-center">
                                    <p class="text-gray-400 text-sm">Belum ada plan. Yuk buat plan pertama!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach (array_slice($tasks, 0, 5) as $task):
                                    $urgClass = 'urgency-santuy';
                                    $urgName = $task['urgency_name'] ?? '-';
                                    if (stripos($urgName, 'Mepet') !== false)      $urgClass = 'urgency-mepet';
                                    elseif (stripos($urgName, 'Gercep') !== false)  $urgClass = 'urgency-gercep';
                                    elseif (stripos($urgName, 'Aman') !== false)    $urgClass = 'urgency-aman';

                                    $impClass = 'importance-baja';
                                    $impName = $task['importance_name'] ?? '-';
                                    if (stripos($impName, 'Harga') !== false)       $impClass = 'importance-harga';
                                    elseif (stripos($impName, 'Highkey') !== false)  $impClass = 'importance-highkey';
                                    elseif (stripos($impName, 'Mid') !== false)     $impClass = 'importance-mid';
                                ?>
                                <div class="task-row flex items-center gap-4 px-6 py-3.5">
                                    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30 accent-[#C0294A] cursor-pointer">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate"><?= htmlspecialchars($task['title']) ?></p>
                                        <p class="text-xs text-gray-400 truncate"><?= htmlspecialchars($task['description']) ?></p>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="<?= $urgClass ?> text-[10px] font-semibold px-2.5 py-0.5 rounded-full"><?= htmlspecialchars($urgName) ?></span>
                                        <span class="<?= $impClass ?> text-[10px] font-semibold px-2.5 py-0.5 rounded-full"><?= htmlspecialchars($impName) ?></span>
                                    </div>
                                    <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=plan_detail&id=<?= $task['plan_id'] ?>" class="text-xs text-gray-400 hover:text-primary font-medium transition-colors">Detail</a>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Notes & Diary -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900">Note & Diary</h3>
                            <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=notes" class="text-xs text-gray-400 hover:text-primary font-semibold transition-colors">See all</a>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <?php if (empty($recentNotes)): ?>
                                <div class="col-span-3 bg-white rounded-2xl border border-gray-100/80 p-8 text-center">
                                    <p class="text-gray-400 text-sm">Belum ada catatan.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach (array_slice($recentNotes, 0, 3) as $note): ?>
                                <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=note_detail&id=<?= $note['notes_id'] ?>"
                                   class="card-lift bg-white rounded-2xl border border-gray-100/80 p-5 flex flex-col gap-2">
                                    <span class="text-[10px] text-gray-400 font-medium">Note</span>
                                    <h4 class="text-sm font-bold text-gray-800 leading-snug"><?= htmlspecialchars($note['title']) ?></h4>
                                    <p class="text-xs text-gray-400 leading-relaxed line-clamp-2"><?= htmlspecialchars(substr($note['content'], 0, 80)) ?>...</p>
                                    <?php if (!empty($note['plan_title'])): ?>
                                        <span class="self-start text-[10px] bg-red-50 text-primary font-semibold px-2.5 py-0.5 rounded-full mt-1">📌 <?= htmlspecialchars($note['plan_title']) ?></span>
                                    <?php endif; ?>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN (4 cols) -->
                <div class="col-span-4 space-y-6">

                    <!-- Upcoming Agenda -->
                    <div class="bg-white rounded-2xl border border-gray-100/80 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900">Upcoming Agenda</h3>
                            <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=plans" class="text-xs text-gray-400 hover:text-primary font-semibold transition-colors">See all</a>
                        </div>
                        <div class="space-y-3">
                            <?php if (empty($tasks)): ?>
                                <p class="text-gray-400 text-xs text-center py-4">Belum ada agenda.</p>
                            <?php else: ?>
                                <?php foreach (array_slice($tasks, 0, 4) as $agenda): ?>
                                <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50/60 hover:bg-red-50/40 transition-colors">
                                    <div class="w-1 h-full min-h-[40px] rounded-full bg-primary flex-shrink-0 mt-0.5"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-800"><?= htmlspecialchars($agenda['title']) ?></p>
                                        <p class="text-[10px] text-gray-400 mt-0.5"><?= htmlspecialchars(substr($agenda['description'], 0, 40)) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- How's your feeling? -->
                    <div class="bg-white rounded-2xl border border-gray-100/80 p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-2">How are you feeling today?</h3>
                        <p class="text-xs text-gray-400 mb-4">Let's check your mood</p>
                        <div class="flex items-center justify-between">
                            <button class="mood-btn flex flex-col items-center gap-1">
                                <span class="text-2xl">😄</span>
                                <span class="text-[10px] text-gray-400 font-medium">Great</span>
                            </button>
                            <button class="mood-btn flex flex-col items-center gap-1">
                                <span class="text-2xl">🙂</span>
                                <span class="text-[10px] text-gray-400 font-medium">Good</span>
                            </button>
                            <button class="mood-btn flex flex-col items-center gap-1">
                                <span class="text-2xl">😐</span>
                                <span class="text-[10px] text-gray-400 font-medium">Okay</span>
                            </button>
                            <button class="mood-btn flex flex-col items-center gap-1">
                                <span class="text-2xl">😞</span>
                                <span class="text-[10px] text-gray-400 font-medium">Low</span>
                            </button>
                            <button class="mood-btn flex flex-col items-center gap-1">
                                <span class="text-2xl">😫</span>
                                <span class="text-[10px] text-gray-400 font-medium">Tired</span>
                            </button>
                        </div>
                    </div>

                    <!-- Stats Mini Cards -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gradient-to-br from-primary to-[#e05a77] rounded-2xl p-4 text-white">
                            <p class="text-white/70 text-[10px] font-semibold uppercase tracking-wider">Plans</p>
                            <p class="text-2xl font-extrabold mt-1"><?= $totalTaskCount ?></p>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-400 rounded-2xl p-4 text-white">
                            <p class="text-white/70 text-[10px] font-semibold uppercase tracking-wider">Notes</p>
                            <p class="text-2xl font-extrabold mt-1"><?= isset($recentNotes) ? count($recentNotes) : 0 ?></p>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-2xl border border-gray-100/80 p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Recent Activity</h3>
                        <div class="space-y-3">
                            <?php foreach (array_slice($tasks, 0, 3) as $idx => $act): ?>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                                        <circle cx="12" cy="12" r="9" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-700 truncate"><?= htmlspecialchars($act['title']) ?></p>
                                    <p class="text-[10px] text-gray-400">Plan added</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($tasks)): ?>
                                <p class="text-xs text-gray-400 text-center py-2">No activity yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

</body>
</html>