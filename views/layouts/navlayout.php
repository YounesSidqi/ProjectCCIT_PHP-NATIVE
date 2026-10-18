<?php
if (!isset($user)) {
    $user = ['name' => 'Ones', 'email' => 'onesmelon@gmail.com'];
}

if (!isset($currentPage) || empty($currentPage)) {
    $paramPage = $_GET['page'] ?? $_GET['action'] ?? '';
    if (!empty($paramPage)) {
        if (in_array($paramPage, ['dashboard'])) {
            $currentPage = 'dashboard';
        } elseif (in_array($paramPage, ['plans', 'allplans', 'plan_detail'])) {
            $currentPage = 'plans';
        } elseif (in_array($paramPage, ['notes', 'allnotes', 'createnotes', 'detailnotes', 'editnotes'])) {
            $currentPage = 'notes';
        }
    } else {
        $path = $_SERVER['REQUEST_URI'];
        if (strpos($path, '/dashboard') !== false) {
            $currentPage = 'dashboard';
        } elseif (strpos($path, '/diaries') !== false || strpos($path, '/plans') !== false) {
            $currentPage = 'plans';
        } elseif (strpos($path, '/notes') !== false) {
            $currentPage = 'notes';
        } else {
            $currentPage = 'dashboard';
        }
    }
}

if (!function_exists('navClass')) {
    function navClass(string $page, string $current): string {
        $base = 'sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group relative';
        if ($page === $current) {
            return $base . ' active bg-[#E0405C]/15 text-[#f09bae] font-semibold shadow-sm shadow-[#E0405C]/5';
        }
        return $base . ' text-white/60 hover:text-white hover:bg-white/[0.06]';
    }
}

$userInitials = 'O';
if (!empty($user['name'])) {
    $words = explode(' ', trim($user['name']));
    $userInitials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
}
?>

<!-- Immediate script to prevent layout flash on page navigation -->
<script>
    (function() {
        try {
            if (localStorage.getItem('ritma_sidebar_collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch (e) {}
    })();
</script>

<style>
    /* ─── Sidebar Transitions & Sizing ─── */
    .sidebar-container {
        width: 16rem; /* 256px */
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: width;
    }

    html.sidebar-collapsed .sidebar-container {
        width: 4.75rem; /* 76px */
    }

    /* Text hiding & animation */
    .sidebar-text-node {
        transition: opacity 0.2s ease, transform 0.2s ease;
        white-space: nowrap;
    }

    html.sidebar-collapsed .sidebar-text-node {
        opacity: 0;
        pointer-events: none;
        display: none !important;
    }

    html.sidebar-collapsed .sidebar-brand-collapsed {
        display: flex !important;
    }

    html.sidebar-collapsed .sidebar-brand-expanded {
        display: none !important;
    }

    html.sidebar-collapsed .sidebar-nav-item {
        justify-content: center;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    html.sidebar-collapsed .sidebar-section-header {
        display: none !important;
    }

    html.sidebar-collapsed .sidebar-toggle-icon {
        transform: rotate(180deg);
    }

    .sidebar-nav-item.active svg {
        color: #E0405C;
    }

    .sidebar-nav-item:hover svg {
        color: #fff;
    }

    /* Custom Floating Tooltip for Collapsed State */
    html.sidebar-collapsed .sidebar-nav-item:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 100%;
        margin-left: 0.75rem;
        padding: 0.35rem 0.75rem;
        background: #1B1B26;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.5rem;
        white-space: nowrap;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 50;
        pointer-events: none;
        animation: fadeIn 0.15s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(-4px); }
        to { opacity: 1; transform: translateX(0); }
    }
</style>

<aside id="appSidebar" class="sidebar-container bg-[#161822] flex-shrink-0 flex flex-col h-full overflow-hidden border-r border-white/5 select-none">

    <!-- ── Brand & Collapse Toggle ── -->
    <div class="px-5 py-5 border-b border-white/[0.08] flex items-center justify-between min-h-[72px]">
        
        <!-- Expanded Brand -->
        <div class="sidebar-brand-expanded flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#E0405C] to-[#ff6b87] flex items-center justify-center text-white font-black text-sm shadow-md shadow-[#E0405C]/25">
                R
            </div>
            <div>
                <p class="text-white font-bold text-lg leading-tight tracking-tight">Ritma<span class="text-[#E0405C]">!</span></p>
                <p class="text-white/40 text-[10px] uppercase font-medium tracking-wider">Plan · Notes</p>
            </div>
        </div>

        <!-- Collapsed Icon-Only Brand (Hidden by default, shown when collapsed) -->
        <div class="sidebar-brand-collapsed hidden w-full justify-center">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#E0405C] to-[#ff6b87] flex items-center justify-center text-white font-black text-base shadow-md shadow-[#E0405C]/25 cursor-pointer" onclick="toggleSidebar()">
                R<span class="text-white text-xs">!</span>
            </div>
        </div>

        <!-- Sidebar Toggle Button (Inside header) -->
        <button type="button" 
                id="sidebarToggleBtn" 
                onclick="toggleSidebar()"
                class="sidebar-text-node p-1.5 rounded-lg text-white/40 hover:text-white hover:bg-white/[0.08] transition-colors focus:outline-none"
                title="Toggle Sidebar (Collapse / Expand)">
            <svg class="w-5 h-5 sidebar-toggle-icon transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/>
                <path stroke-linecap="round" d="M9 4v16"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="m14 10-2 2 2 2"/>
            </svg>
        </button>
    </div>

    <!-- ── Navigation ── -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto overflow-x-hidden">

        <!-- Section Header -->
        <p class="sidebar-section-header text-white/30 text-[11px] font-semibold uppercase tracking-wider px-3 mb-2 pt-1">Main Menu</p>

        <!-- Dashboard -->
        <a href="/ProjectCCIT_PHP_NATIVE/index.php?page=dashboard"
           class="<?= navClass('dashboard', $currentPage) ?>"
           data-tooltip="Dashboard">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z
                         M3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25z
                         M13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6z
                         M13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
            </svg>
            <span class="sidebar-text-node flex-1">Dashboard</span>
            <?php if ($currentPage === 'dashboard'): ?>
                <span class="sidebar-text-node ml-auto w-1.5 h-1.5 rounded-full bg-[#E0405C]"></span>
            <?php endif; ?>
        </a>

        <!-- My Notes -->
        <a href="/ProjectCCIT_PHP_NATIVE/index.php?page=allnotes"
           class="<?= navClass('notes', $currentPage) ?>"
           data-tooltip="My Notes">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5
                         A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25
                         m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75
                         c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <span class="sidebar-text-node flex-1">My Notes</span>
            <?php if ($currentPage === 'notes'): ?>
                <span class="sidebar-text-node ml-auto w-1.5 h-1.5 rounded-full bg-[#E0405C]"></span>
            <?php endif; ?>
        </a>

        <!-- Plans -->
        <a href="/ProjectCCIT_PHP_NATIVE/index.php?page=plans"
           class="<?= navClass('plans', $currentPage) ?>"
           data-tooltip="Plans">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5
                         A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5
                         A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5
                         A2.25 2.25 0 0121 11.25v7.5
                         m-9-6h.008v.008H12v-.008z
                         M12 15h.008v.008H12V15z
                         M12 18h.008v.008H12V18z
                         M9.75 15h.008v.008H9.75V15z
                         M9.75 18h.008v.008H9.75V18z
                         M14.25 15h.008v.008h-.008V15z
                         M14.25 18h.008v.008h-.008V18z"/>
            </svg>
            <span class="sidebar-text-node flex-1">Plans</span>
            <?php if ($currentPage === 'plans'): ?>
                <span class="sidebar-text-node ml-auto w-1.5 h-1.5 rounded-full bg-[#E0405C]"></span>
            <?php endif; ?>
        </a>

        <!-- Account Section Header -->
        <p class="sidebar-section-header text-white/30 text-[11px] font-semibold uppercase tracking-wider px-3 pt-5 pb-1">Account</p>

        <!-- Settings -->
        <a href="#" class="sidebar-nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/[0.06] text-sm font-medium transition-colors" data-tooltip="Settings">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894
                         c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12
                         l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204
                         .165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094
                         c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78
                         -.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45
                         l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107
                         -.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094
                         c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93
                         -.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12
                         l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204
                         -.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094
                         c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78
                         .165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45
                         l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107
                         .397-.165.71-.505.78-.929l.15-.894z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="sidebar-text-node flex-1">Settings</span>
        </a>

    </nav>

</aside>

<script>
    function toggleSidebar() {
        const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
        try {
            localStorage.setItem('ritma_sidebar_collapsed', isCollapsed ? 'true' : 'false');
        } catch (e) {}
    }
</script>
