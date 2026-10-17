<?php
session_start();
require_once __DIR__ . '/../models/models.php';

// Simulated auth — hardcode user_id = 1 for dev. Replace with real login later.
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}
$userId = $_SESSION['user_id'];
$user = getUserById($userId);

// ─── Route by action parameter ─────────────────────────

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : 'dashboard');

switch ($action) {

    // ── DASHBOARD ───────────────────────────────────────
    case 'dashboard':
        $tasks = getDashboardTasks($userId);
        $notes = getAllNotes($userId);
        $recentNotes = array_slice($notes, 0, 4);
        $totalPlans = count($tasks);
        $completedPlans = 0; // placeholder — no completion column in schema
        require __DIR__ . '/../views/dashboard/dashboard.php';
        break;

    // ── PLANS ───────────────────────────────────────────
    case 'plans':
        $plans = getAllPlans($userId);
        $urgencyCategories = getUrgencyCategories();
        $importanceCategories = getImportanceCategories();
        require __DIR__ . '/../views/plans/allplans.php';
        break;

    case 'plan_detail':
        $planId = (int) $_GET['id'];
        $plan = getPlanById($planId);
        $urgencyCategories = getUrgencyCategories();
        $importanceCategories = getImportanceCategories();
        require __DIR__ . '/../views/plans/allplans.php';
        break;

    case 'add_plan':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $urgencyStatus = $_POST['urgency_status'];
            $importantStatus = $_POST['important_status'];
            insertPlan($userId, $title, $description, $urgencyStatus, $importantStatus);
            header("Location: controllers.php?action=plans");
            exit;
        }
        break;

    case 'edit_plan':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $planId = (int) $_POST['plan_id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $urgencyStatus = $_POST['urgency_status'];
            $importantStatus = $_POST['important_status'];
            updatePlan($planId, $title, $description, $urgencyStatus, $importantStatus);
            header("Location: controllers.php?action=plans");
            exit;
        }
        break;

    case 'delete_plan':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $planId = (int) $_POST['plan_id'];
            deletePlan($planId);
            header("Location: controllers.php?action=plans");
            exit;
        }
        break;

    // ── NOTES ───────────────────────────────────────────
    case 'notes':
        $notes = getAllNotes($userId);
        require __DIR__ . '/../views/notes/allnotes.php';
        break;

    case 'note_detail':
        $noteId = (int) $_GET['id'];
        $note = getNoteById($noteId);
        require __DIR__ . '/../views/notes/detailnotes.php';
        break;

    case 'edit_note_form':
        $noteId = (int) $_GET['id'];
        $note = getNoteById($noteId);
        $plans = getAllPlans($userId);
        require __DIR__ . '/../views/notes/editnotess.php';
        break;

    case 'add_note':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $planId = !empty($_POST['plan_id']) ? (int) $_POST['plan_id'] : 0;
            $imagePath = null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'note_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
                $imagePath = 'assets/uploads/' . $filename;
            }

            insertNote($userId, $planId, $title, $content, $imagePath);
            header("Location: controllers.php?action=notes");
            exit;
        }
        break;

    case 'edit_note':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $noteId = (int) $_POST['notes_id'];
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $planId = !empty($_POST['plan_id']) ? (int) $_POST['plan_id'] : 0;
            $imagePath = null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'note_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
                $imagePath = 'assets/uploads/' . $filename;
            }

            updateNote($noteId, $planId, $title, $content, $imagePath);
            header("Location: controllers.php?action=note_detail&id=" . $noteId);
            exit;
        }
        break;

    case 'delete_note':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $noteId = (int) $_POST['notes_id'];
            deleteNote($noteId);
            header("Location: controllers.php?action=notes");
            exit;
        }
        break;

    default:
        header("Location: controllers.php?action=dashboard");
        exit;
}
?>
