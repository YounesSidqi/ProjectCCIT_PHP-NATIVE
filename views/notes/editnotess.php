<?php
    $currentPage = 'notes';
    $navLayoutPath = __DIR__ . '/../layouts/navlayout.php';

    if (!isset($note))  $note  = null;
    if (!isset($plans)) $plans = [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note — Ritma!</title>
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
            border-color: #C0294A;
            background: #fdf2f4;
        }
    </style>
</head>
<body class="bg-surface flex h-screen overflow-hidden">

    <?php require $navLayoutPath; ?>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="bg-white border-b border-gray-100/80 px-8 py-4 flex items-center gap-4 flex-shrink-0">
            <a href="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=note_detail&id=<?= $note ? $note['notes_id'] : '' ?>" class="text-gray-400 hover:text-primary transition-colors">
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
                <form action="/ProjectCCIT_PHP_NATIVE/controllers/controllers.php?action=edit_note" method="POST" enctype="multipart/form-data"
                      class="bg-white rounded-2xl border border-gray-100/80 p-8 space-y-5">

                    <input type="hidden" name="notes_id" value="<?= $note['notes_id'] ?>">

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Note Title</label>
                        <input type="text" name="title" required value="<?= htmlspecialchars($note['title']) ?>"
                               class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Content</label>
                        <textarea name="content" rows="8"
                                  class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm leading-relaxed focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all resize-none"><?= htmlspecialchars($note['content']) ?></textarea>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Linked Plan (optional)</label>
                        <select name="plan_id" class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all bg-white">
                            <option value="0">No linked plan</option>
                            <?php foreach ($plans as $p): ?>
                                <option value="<?= $p['plan_id'] ?>" <?= ($note['plan_id'] == $p['plan_id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Image Upload Dropzone -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Attach Image</label>
                        <div id="dropzone" class="dropzone mt-1.5 rounded-xl p-6 flex flex-col items-center justify-center text-center cursor-pointer"
                             onclick="document.getElementById('imageInput').click()">
                            <?php if (!empty($note['image_path'])): ?>
                                <img id="previewImg" src="/ProjectCCIT_PHP_NATIVE/<?= htmlspecialchars($note['image_path']) ?>" class="w-32 h-32 object-cover rounded-lg mb-3">
                            <?php else: ?>
                                <img id="previewImg" src="" class="hidden w-32 h-32 object-cover rounded-lg mb-3">
                            <?php endif; ?>
                            <svg id="uploadIcon" class="w-8 h-8 text-primary mb-2 <?= !empty($note['image_path']) ? 'hidden' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                            <p class="text-xs text-gray-400"><span class="text-primary font-semibold">Click here</span> to add your image</p>
                            <p class="text-[10px] text-gray-300 mt-1">Supported format: png, jpg, jpeg</p>
                        </div>
                        <input type="file" name="image" id="imageInput" accept="image/png,image/jpeg,image/jpg" class="hidden" onchange="previewFile(this)">
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold py-3 rounded-xl transition-all shadow-lg shadow-primary/20">
                            Save the edit
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

    <script>
        function previewFile(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('previewImg');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    document.getElementById('uploadIcon').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        const dz = document.getElementById('dropzone');
        if (dz) {
            dz.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            dz.addEventListener('dragleave', function() {
                this.classList.remove('dragover');
            });
            dz.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                const input = document.getElementById('imageInput');
                input.files = e.dataTransfer.files;
                previewFile(input);
            });
        }
    </script>
</body>
</html>
