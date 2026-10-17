<?php
    $currentPage = 'plans';
    $navLayoutPath = __DIR__ . '/../layouts/navlayout.php';

    if (!isset($user))   $user   = ['name' => 'User', 'email' => ''];
    if (!isset($plans))  $plans  = [];
    if (!isset($urgencyCategories))    $urgencyCategories    = [];
    if (!isset($importanceCategories)) $importanceCategories = [];

    $selectedPlan = isset($plan) ? $plan : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Plans — Ritma!</title>
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

        .plan-card { transition: all 0.18s ease; border-left: 3px solid transparent; }
        .plan-card:hover { border-left-color: #C0294A; background: #fdf2f4; }
        .plan-card.selected { border-left-color: #C0294A; background: #fdf2f4; }

        .urgency-santuy     { background: #e8f5e9; color: #2e7d32; }
        .urgency-aman       { background: #e3f2fd; color: #1565c0; }
        .urgency-gercep     { background: #fff3e0; color: #e65100; }
        .urgency-mepet      { background: #fce4ec; color: #b71c1c; }
        .importance-baja    { background: #f3e5f5; color: #6a1b9a; }
        .importance-mid     { background: #e0f2f1; color: #00695c; }
        .importance-highkey { background: #fff8e1; color: #f57f17; }
        .importance-harga   { background: #fce4ec; color: #c62828; }

        .overlay { 
            background: rgba(0,0,0,0.4); 
            backdrop-filter: blur(4px); 
            transition: opacity 0.2s ease;
        }
        .modal-panel {
            animation: slideUp 0.25s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-surface flex h-screen overflow-hidden">

    <?php require $navLayoutPath; ?>

    <div class="flex-1 flex overflow-hidden">

        <!-- LEFT: Plan List -->
        <div class="w-[380px] bg-white border-r border-gray-100 flex flex-col flex-shrink-0">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Your Plans</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Start your plan</p>
                </div>
                <button onclick="openModal('addPlanModal')"
                        class="flex items-center gap-1.5 bg-primary hover:bg-primary-dark text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all shadow-lg shadow-primary/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Plan
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <?php if (empty($plans)): ?>
                    <div class="flex flex-col items-center justify-center h-full text-center px-6">
                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-400">No plans yet</p>
                        <p class="text-xs text-gray-300 mt-1">Create your first plan!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($plans as $p):
                        $isSelected = ($selectedPlan && $selectedPlan['plan_id'] == $p['plan_id']);
                        $urgClass = 'urgency-santuy';
                        $uName = $p['urgency_name'] ?? '-';
                        if (stripos($uName, 'Mepet') !== false) $urgClass = 'urgency-mepet';
                        elseif (stripos($uName, 'Gercep') !== false) $urgClass = 'urgency-gercep';
                        elseif (stripos($uName, 'Aman') !== false) $urgClass = 'urgency-aman';
                    ?>
                    <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=plan_detail&id=<?= $p['plan_id'] ?>"
                       class="plan-card <?= $isSelected ? 'selected' : '' ?> block px-6 py-4 border-b border-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0 pr-3">
                                <p class="text-sm font-bold text-gray-800 truncate"><?= htmlspecialchars($p['title']) ?></p>
                                <p class="text-xs text-gray-400 mt-1 line-clamp-2"><?= htmlspecialchars($p['description']) ?></p>
                            </div>
                            <span class="<?= $urgClass ?> text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0"><?= htmlspecialchars($uName) ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Plan Detail -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <?php if ($selectedPlan): ?>
                <?php
                    $urgClass2 = 'urgency-santuy';
                    $uName2 = $selectedPlan['urgency_name'] ?? '-';
                    if (stripos($uName2, 'Mepet') !== false) $urgClass2 = 'urgency-mepet';
                    elseif (stripos($uName2, 'Gercep') !== false) $urgClass2 = 'urgency-gercep';
                    elseif (stripos($uName2, 'Aman') !== false) $urgClass2 = 'urgency-aman';

                    $impClass2 = 'importance-baja';
                    $iName2 = $selectedPlan['importance_name'] ?? '-';
                    if (stripos($iName2, 'Harga') !== false) $impClass2 = 'importance-harga';
                    elseif (stripos($iName2, 'Highkey') !== false) $impClass2 = 'importance-highkey';
                    elseif (stripos($iName2, 'Mid') !== false) $impClass2 = 'importance-mid';
                ?>
                <div class="px-8 py-6">
                    <p class="text-xs text-gray-400 font-medium mb-1">Your Plans</p>
                    <h2 class="text-xl font-extrabold text-gray-900 mb-6">Your Plan Details</h2>

                    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
                        <div>
                            <label class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Title</label>
                            <p class="text-base font-bold text-gray-800 mt-1"><?= htmlspecialchars($selectedPlan['title']) ?></p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Description</label>
                            <p class="text-sm text-gray-600 mt-1 leading-relaxed"><?= nl2br(htmlspecialchars($selectedPlan['description'])) ?></p>
                        </div>
                        <div class="flex gap-3">
                            <div>
                                <label class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Urgency</label>
                                <p class="mt-1"><span class="<?= $urgClass2 ?> text-xs font-semibold px-3 py-1 rounded-full"><?= htmlspecialchars($uName2) ?></span></p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Importance</label>
                                <p class="mt-1"><span class="<?= $impClass2 ?> text-xs font-semibold px-3 py-1 rounded-full"><?= htmlspecialchars($iName2) ?></span></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-3 border-t border-gray-50">
                            <button onclick="openModal('editPlanModal')"
                                    class="flex items-center gap-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                </svg>
                                Edit Plan
                            </button>
                            <button onclick="openModal('deletePlanModal')"
                                    class="flex items-center gap-2 bg-white border border-red-200 text-primary hover:bg-red-50 text-sm font-semibold px-5 py-2.5 rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                                Delete Plan
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex-1 flex flex-col items-center justify-center text-center px-8">
                    <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mb-5">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zM12 2.25V4.5m5.834.166l-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243l-1.59-1.59"/>
                        </svg>
                    </div>
                    <p class="text-base font-bold text-gray-300">Select a plan to view details</p>
                    <p class="text-sm text-gray-300 mt-1">or create a new one</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══ ADD PLAN MODAL ═══ -->
    <div id="addPlanModal" class="hidden fixed inset-0 z-50 overlay flex items-center justify-center">
        <div class="modal-panel bg-white rounded-2xl w-full max-w-lg mx-4 shadow-2xl">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Add Your Plan</h3>
                <p class="text-xs text-gray-400 mt-0.5">Fill in the details below</p>
            </div>
            <form action="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=add_plan" method="POST" class="p-6 space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Plan Title</label>
                    <input type="text" name="title" required placeholder="e.g. Attend CSS Meeting"
                           class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</label>
                    <textarea name="description" rows="3" placeholder="Plan description..."
                              class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Urgency Status</label>
                        <select name="urgency_status" required class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all bg-white">
                            <option value="">Select...</option>
                            <?php foreach ($urgencyCategories as $uc): ?>
                                <option value="<?= $uc['urgency_id'] ?>"><?= htmlspecialchars($uc['urgency_name']) ?> — <?= htmlspecialchars($uc['urgency_desk']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Importance</label>
                        <select name="important_status" required class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all bg-white">
                            <option value="">Select...</option>
                            <?php foreach ($importanceCategories as $ic): ?>
                                <option value="<?= $ic['importance_id'] ?>"><?= htmlspecialchars($ic['importance_name']) ?> — <?= htmlspecialchars($ic['importance_description']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('addPlanModal')" class="px-5 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors">Cancel</button>
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all shadow-lg shadow-primary/20">Save to Plan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══ EDIT PLAN MODAL ═══ -->
    <?php if ($selectedPlan): ?>
    <div id="editPlanModal" class="hidden fixed inset-0 z-50 overlay flex items-center justify-center">
        <div class="modal-panel bg-white rounded-2xl w-full max-w-lg mx-4 shadow-2xl">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Edit Your Plan</h3>
            </div>
            <form action="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=edit_plan" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="plan_id" value="<?= $selectedPlan['plan_id'] ?>">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($selectedPlan['title']) ?>"
                           class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</label>
                    <textarea name="description" rows="3"
                              class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all resize-none"><?= htmlspecialchars($selectedPlan['description']) ?></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Urgency</label>
                        <select name="urgency_status" required class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all bg-white">
                            <?php foreach ($urgencyCategories as $uc): ?>
                                <option value="<?= $uc['urgency_id'] ?>" <?= ($selectedPlan['urgency_status'] == $uc['urgency_id']) ? 'selected' : '' ?>><?= htmlspecialchars($uc['urgency_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Importance</label>
                        <select name="important_status" required class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all bg-white">
                            <?php foreach ($importanceCategories as $ic): ?>
                                <option value="<?= $ic['importance_id'] ?>" <?= ($selectedPlan['important_status'] == $ic['importance_id']) ? 'selected' : '' ?>><?= htmlspecialchars($ic['importance_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('editPlanModal')" class="px-5 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors">Cancel</button>
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all shadow-lg shadow-primary/20">Save the edit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══ DELETE PLAN MODAL ═══ -->
    <div id="deletePlanModal" class="hidden fixed inset-0 z-50 overlay flex items-center justify-center">
        <div class="modal-panel bg-white rounded-2xl w-full max-w-sm mx-4 shadow-2xl text-center p-8">
            <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Delete this Plan?</h3>
            <p class="text-xs text-gray-400 mb-6">This plan will be permanently deleted and can't be recovered.</p>
            <form action="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=delete_plan" method="POST" class="flex items-center justify-center gap-3">
                <input type="hidden" name="plan_id" value="<?= $selectedPlan['plan_id'] ?>">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all">Yes, Delete</button>
                <button type="button" onclick="closeModal('deletePlanModal')" class="text-primary border border-primary/30 hover:bg-red-50 text-sm font-semibold px-6 py-2.5 rounded-xl transition-all">Cancel</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        function openModal(id)  { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        document.querySelectorAll('.overlay').forEach(el => {
            el.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });
    </script>

</body>
</html>
