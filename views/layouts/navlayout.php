<?php

if (!isset($currentPage)) {
    $path = $_SERVER['REQUEST_URI'];
    if (strpos($path, '/dashboard') !== false) {
        $currentPage = 'dashboard';
    } elseif (strpos($path, '/diaries') !== false || strpos($path, '/plans') !== false) {
        $currentPage = 'plans';
    } elseif (strpos($path, '/notes') !== false) {
        $currentPage = 'notes';
    } else {
        $currentPage = '';
    }
}


function navClass(string $page, string $current): string {
    if ($page === $current) {
        return 'sidebar-link active flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/90 text-sm font-semibold';
    }
    return 'sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/50 text-sm font-medium';
}
?>



<style>

    .sidebar-link {
        transition: all 0.18s ease;
    }
    .sidebar-link:hover {
        background: rgba(192, 41, 74, 0.15);
        color: #e87a92;
    }
    .sidebar-link.active {
        background: rgba(192, 41, 74, 0.18);
        color: #f09bae;
        border-left: 3px solid #C0294A;
        padding-left: calc(0.75rem - 3px); 
    }
    .sidebar-link.active svg {
        color: #C0294A;
    }
</style>

<aside class="w-64 bg-[#1a1a2e] flex-shrink-0 flex flex-col h-full overflow-y-auto">

    <!-- ── Logo ── -->
    <div class="px-6 py-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#C0294A] flex items-center justify-center shadow-lg shadow-[#C0294A]/30">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                </svg>
            </div>
            <div>
                <p class="text-white font-bold text-sm leading-tight">Ritma!</p>
                <p class="text-white/40 text-xs">Write. Organize. Grow.</p>
            </div>
        </div>
    </div>

    <!-- ── User Profile ── -->
    <div class="px-6 py-5 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#C0294A] to-pink-400
                        flex items-center justify-center text-white font-bold text-sm shadow">
                YS
            </div>
            <div>
                <p class="text-white font-semibold text-sm">Younes Sidqi</p>
                <p class="text-white/40 text-xs">younes@mail.com</p>
            </div>
        </div>
    </div>

    <!-- ── Navigation ── -->
    <nav class="flex-1 px-4 py-5 space-y-1">

        <!-- Main Menu -->
        <p class="text-white/30 text-xs font-semibold uppercase tracking-widest px-2 mb-3">Main Menu</p>

        <!-- Dashboard -->
        <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=dashboard"
           class="<?= navClass('dashboard', $currentPage) ?>">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z
                         M3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25z
                         M13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6z
                         M13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
            </svg>
            Dashboard
            <?php if ($currentPage === 'dashboard'): ?>
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-[#C0294A]"></span>
            <?php endif; ?>
        </a>

        <!-- My Notes -->
        <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=notes"
           class="<?= navClass('notes', $currentPage) ?>">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5
                         A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25
                         m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75
                         c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            My Notes
            <?php if ($currentPage === 'notes'): ?>
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-[#C0294A]"></span>
            <?php endif; ?>
        </a>

        <!-- Plans / Diaries -->
        <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=plans"
           class="<?= navClass('plans', $currentPage) ?>">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
            Plans
            <?php if ($currentPage === 'plans'): ?>
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-[#C0294A]"></span>
            <?php endif; ?>
        </a>

        <!-- Bookmarks -->
        <a href="#"
           class="<?= navClass('bookmarks', $currentPage) ?>">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507
                         c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
            </svg>
            Bookmarks
        </a>

        <!-- Account Section -->
        <p class="text-white/30 text-xs font-semibold uppercase tracking-widest px-2 pt-5 pb-2">Account</p>

        <!-- Settings -->
        <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/50 text-sm font-medium">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
            Settings
        </a>

        <!-- Logout -->
        <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/50 text-sm font-medium">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5
                         A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
            </svg>
            Logout
        </a>
    </nav>

</aside>
