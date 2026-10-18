<?php
    $currentPage = 'dashboard';
    $navLayoutPath = __DIR__ . '/../layouts/nav_layout.php';
    if (!file_exists($navLayoutPath)) {
        $navLayoutPath = __DIR__ . '/../layouts/nav_layout.php';
    }

    if (!isset($user))        $user        = ['name' => 'Ones', 'email' => 'onesmelon@gmail.com'];
    if (!isset($tasks))       $tasks       = [];
    if (!isset($recentNotes)) $recentNotes = [];
    if (!isset($doneTasks))   $doneTasks   = 0;
    if (!isset($totalTaskCount)) $totalTaskCount = count($tasks);

    $greeting = '';
    $hour = (int) date('G');
    if ($hour < 12)      $greeting = 'Good morning';
    elseif ($hour < 17)  $greeting = 'Good afternoon';
    else                 $greeting = 'Good evening';

    $todayDate = date('l, F j');
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
                        primary: '#E0405C',
                        'primary-light': '#FDE8ED',
                        'primary-dark': '#C0294A',
                        sidebar: '#1B1B26',
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
        ::-webkit-scrollbar-thumb:hover { background: #E0405C; }

        .task-row { transition: background 0.15s ease; }
        .task-row:hover { background: #FDE8ED/30; }
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
            border-color: #E0405C;
            color: #E0405C;
            background: #FDE8ED;
        }
        .quick-btn:hover svg { color: #E0405C; }

        .mood-btn { transition: transform 0.15s ease; }
        .mood-btn:hover { transform: scale(1.2); }
    </style>
</head>
<body class="bg-surface flex h-screen overflow-hidden">

    <?php require $navLayoutPath; ?>

    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-100/80 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <div class="text-xs text-gray-400 font-medium">
                <?= $todayDate ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-8 py-6">
            <div class="max-w-6xl mx-auto grid grid-cols-12 gap-6">

                <!-- LEFT COLUMN (8 cols) -->
                <div class="col-span-8 space-y-6">

                    <!-- Quick Actions -->
                    <div class="grid grid-cols-2 gap-4">
                        <a href="index.php?page=plans" class="quick-btn bg-white rounded-2xl p-4 flex flex-col items-center gap-2 text-center shadow-sm">
                            <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-gray-700">Add Plan</span>
                        </a>
                        <a href="index.php?page=createnotes" class="quick-btn bg-white rounded-2xl p-4 flex flex-col items-center gap-2 text-center shadow-sm">
                            <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-gray-700">Add Note</span>
                        </a>
                    </div>

                    <!-- Today's Actions -->
                    <div class="bg-white rounded-2xl border border-gray-100/80 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                            <h3 class="text-sm font-bold text-gray-900">Todays Action</h3>
                            <span id="taskCounter" class="text-xs font-bold text-primary"><?= $doneTasks ?>/<?= $totalTaskCount ?></span>
                        </div>
                        <div class="divide-y divide-gray-50" id="tasksList">
                            <?php if (empty($tasks)): ?>
                                <div class="px-6 py-8 text-center">
                                    <p class="text-gray-400 text-sm">Belum ada plan untuk hari ini. Yuk buat plan!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($tasks as $task):
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

                                    $isDone = !empty($task['is_completed']);
                                ?>
                                <div class="task-row flex items-center gap-4 px-6 py-3.5">
                                    <!-- Simple GET link toggle without AJAX -->
                                    <a href="index.php?page=toggle_plan&id=<?= $task['plan_id'] ?>&status=<?= $isDone ? 0 : 1 ?>"
                                       class="w-5 h-5 rounded-md border flex items-center justify-center transition-all flex-shrink-0 <?= $isDone ? 'bg-primary border-primary text-white' : 'border-gray-300 hover:border-primary bg-white' ?>"
                                       title="<?= $isDone ? 'Mark as incomplete' : 'Mark as complete' ?>">
                                        <?php if ($isDone): ?>
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                            </svg>
                                        <?php endif; ?>
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold truncate transition-all duration-150 <?= $isDone ? 'line-through text-gray-400' : 'text-gray-800' ?>">
                                            <?= htmlspecialchars($task['title']) ?>
                                        </p>
                                        <p class="text-xs truncate transition-all duration-150 <?= $isDone ? 'line-through text-gray-300' : 'text-gray-400' ?>">
                                            <?= htmlspecialchars($task['description']) ?>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="<?= $urgClass ?> text-[10px] font-semibold px-2.5 py-0.5 rounded-full"><?= htmlspecialchars($urgName) ?></span>
                                        <span class="<?= $impClass ?> text-[10px] font-semibold px-2.5 py-0.5 rounded-full"><?= htmlspecialchars($impName) ?></span>
                                    </div>
                                    <a href="index.php?page=detail_plan&id=<?= $task['plan_id'] ?>" class="text-xs text-gray-400 hover:text-primary font-medium transition-colors">Detail</a>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Notes & Diary -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900">Your Notes</h3>
                            <a href="index.php?page=allnotes" class="text-xs text-gray-400 hover:text-primary font-semibold transition-colors">See all</a>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <?php if (empty($recentNotes)): ?>
                                <div class="col-span-3 bg-white rounded-2xl border border-gray-100/80 p-8 text-center shadow-sm">
                                    <p class="text-gray-400 text-sm">Belum ada catatan.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach (array_slice($recentNotes, 0, 3) as $note): ?>
                                <a href="index.php?page=detailnotes&id=<?= $note['notes_id'] ?>"
                                   class="card-lift bg-white rounded-2xl border border-gray-100/80 p-5 flex flex-col gap-2 shadow-sm">
                                    <span class="text-[10px] text-gray-400 font-medium">Note</span>
                                    <h4 class="text-sm font-bold text-gray-800 leading-snug line-clamp-1"><?= htmlspecialchars($note['title']) ?></h4>
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
                    <div class="bg-white rounded-2xl border border-gray-100/80 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900">Upcoming Agenda</h3>
                            <a href="index.php?page=plans" class="text-xs text-gray-400 hover:text-primary font-semibold transition-colors">See all</a>
                        </div>
                        <div class="space-y-3">
                            <?php if (empty($tasks)): ?>
                                <p class="text-gray-400 text-xs text-center py-4">Belum ada agenda hari ini.</p>
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
                    <div class="bg-white rounded-2xl border border-gray-100/80 p-5 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900 mb-2">How are you feeling today?</h3>
                        <p class="text-xs text-gray-400 mb-4">Let's check your mood</p>
                        <div class="flex items-center justify-between">
                            <button type="button" class="mood-btn flex flex-col items-center gap-1">
                                <span class="text-2xl">😄</span>
                                <span class="text-[10px] text-gray-400 font-medium">Great</span>
                            </button>
                            <button type="button" class="mood-btn flex flex-col items-center gap-1">
                                <span class="text-2xl">🙂</span>
                                <span class="text-[10px] text-gray-400 font-medium">Good</span>
                            </button>
                            <button type="button" class="mood-btn flex flex-col items-center gap-1">
                                <span class="text-2xl">😐</span>
                                <span class="text-[10px] text-gray-400 font-medium">Okay</span>
                            </button>
                            <button type="button" class="mood-btn flex flex-col items-center gap-1">
                                <span class="text-2xl">😞</span>
                                <span class="text-[10px] text-gray-400 font-medium">Low</span>
                            </button>
                            <button type="button" class="mood-btn flex flex-col items-center gap-1">
                                <span class="text-2xl">😫</span>
                                <span class="text-[10px] text-gray-400 font-medium">Tired</span>
                            </button>
                        </div>
                    </div>

                    <!-- Stats Mini Cards -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-sm hover:border-gray-300 transition-all flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-[10px] font-semibold uppercase tracking-wider">Plans</p>
                                <p class="text-2xl font-extrabold text-gray-900 mt-0.5"><?= $totalTaskCount ?></p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-[#E0405C]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75"/>
                                </svg>
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-sm hover:border-gray-300 transition-all flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-[10px] font-semibold uppercase tracking-wider">Notes</p>
                                <p class="text-2xl font-extrabold text-gray-900 mt-0.5"><?= count($recentNotes) ?></p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-2xl border border-gray-100/80 p-5 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Recent Activity</h3>
                        <div class="space-y-3">
                            <?php foreach (array_slice($tasks, 0, 3) as $act): ?>
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