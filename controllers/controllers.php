<?php
// ==============================================================
// CONTROLLER: Simple Request Handlers for ProjectCCIT_PHP_NATIVE
// ==============================================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../models/models.php';

// Single-user enforcement using global constant defined in config.php
$userId = defined('CURRENT_USER_ID') ? CURRENT_USER_ID : 1;
$_SESSION['user_id'] = $userId;

$user = getUserById($userId);
if (!$user) {
    $user = ['id' => 1, 'name' => 'ones', 'email' => 'onesmelon@gmail.com'];
}

// ─── Dashboard Handler ────────────────────────────────────────

function showDashboard() {
    global $userId, $user;
    $todayDate = date('Y-m-d');
    $tasks = getDashboardTasks($userId, $todayDate);
    $stats = getTaskCompletionStats($userId, $todayDate);
    $notes = getAllNotes($userId);
    $recentNotes = array_slice($notes, 0, 4);
    $doneTasks = $stats['completed'];
    $totalTaskCount = $stats['total'];
    $totalPlans = $stats['total'];

    require __DIR__ . '/../views/dashboard/dashboard.php';
}

// ─── Task Status Toggle (Simple GET Link, NO AJAX) ─────────────

function togglePlanStatus() {
    $planId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $status = isset($_GET['status']) ? (int) $_GET['status'] : 0;

    if ($planId > 0) {
        updateTaskStatus($planId, $status);
    }

    header("Location: index.php?page=dashboard");
    exit();
}

// ─── Note Handlers ─────────────────────────────────────────────

function showAllNotes() {
    global $userId, $user;
    $notes = getAllNotes($userId);
    require __DIR__ . '/../views/notes/allnotes.php';
}

function showCreateNote() {
    global $userId, $user;
    $plans = getAllPlans($userId);
    require __DIR__ . '/../views/notes/createnotes.php';
}

function storeNote() {
    global $userId;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $planId = !empty($_POST['plan_id']) ? (int) $_POST['plan_id'] : null;

        saveNote($userId, $planId, $title, $content);
        header("Location: index.php?page=allnotes");
        exit();
    }
}

function showNoteDetail() {
    global $userId, $user;
    $noteId = (int) ($_GET['id'] ?? 0);
    $note = getNoteById($noteId);
    require __DIR__ . '/../views/notes/detailnotes.php';
}

function showEditNoteForm() {
    global $userId, $user;
    $noteId = (int) ($_GET['id'] ?? 0);
    $note = getNoteById($noteId);
    $plans = getAllPlans($userId);
    require __DIR__ . '/../views/notes/editnotes.php';
}

function updateNoteAction() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $noteId = (int) ($_POST['notes_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $planId = !empty($_POST['plan_id']) ? (int) $_POST['plan_id'] : null;

        updateNote($noteId, $planId, $title, $content);
        header("Location: index.php?page=detailnotes&id=" . $noteId);
        exit();
    }
}

function deleteNoteAction() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $noteId = (int) ($_POST['notes_id'] ?? 0);
        deleteNote($noteId);
        header("Location: index.php?page=allnotes");
        exit();
    }
}

// ─── Plan Handlers ─────────────────────────────────────────────

function showAllPlans() {
    global $userId, $user;
    $selectedDate = !empty($_GET['date']) ? $_GET['date'] : null;
    $plans = getAllPlans($userId, $selectedDate);
    $urgencyCategories = getUrgencyCategories();
    $importanceCategories = getImportanceCategories();
    $selectedPlan = null;
    if (!empty($_GET['id'])) {
        $selectedPlan = getPlanById((int) $_GET['id']);
    }
    require __DIR__ . '/../views/plans/allplans.php';
}

function storePlan() {
    global $userId;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $planDate = !empty($_POST['plan_date']) ? $_POST['plan_date'] : date('Y-m-d');
        $urgencyStatus = $_POST['urgency_status'] ?? '1';
        $importantStatus = $_POST['important_status'] ?? '1';

        savePlan($userId, $title, $description, $planDate, $urgencyStatus, $importantStatus);
        header("Location: index.php?page=plans");
        exit();
    }
}

function updatePlanAction() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $planId = (int) ($_POST['plan_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $planDate = !empty($_POST['plan_date']) ? $_POST['plan_date'] : null;
        $urgencyStatus = $_POST['urgency_status'] ?? '1';
        $importantStatus = $_POST['important_status'] ?? '1';

        updatePlan($planId, $title, $description, $urgencyStatus, $importantStatus, $planDate);
        header("Location: index.php?page=plans");
        exit();
    }
}

function deletePlanAction() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $planId = (int) ($_POST['plan_id'] ?? 0);
        deletePlan($planId);
        header("Location: index.php?page=plans");
        exit();
    }
}

// ─── Router Dispatcher ─────────────────────────────────────────

$action = $_GET['page'] ?? $_POST['page'] ?? $_GET['action'] ?? $_POST['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard':
        showDashboard();
        break;

    case 'toggle_plan':
    case 'togglePlanStatus':
        togglePlanStatus();
        break;

    case 'store_note':
    case 'add_note':
        storeNote();
        break;

    case 'allnotes':
    case 'notes':
        showAllNotes();
        break;

    case 'createnotes':
    case 'create_note':
        showCreateNote();
        break;

    case 'detailnotes':
    case 'note_detail':
        showNoteDetail();
        break;

    case 'editnotes':
    case 'edit_note_form':
        showEditNoteForm();
        break;

    case 'update_note':
    case 'edit_note':
        updateNoteAction();
        break;

    case 'delete_note':
        deleteNoteAction();
        break;

    case 'plans':
    case 'allplans':
    case 'plan_detail':
        showAllPlans();
        break;

    case 'store_plan':
    case 'add_plan':
        storePlan();
        break;

    case 'edit_plan':
    case 'update_plan':
        updatePlanAction();
        break;

    case 'delete_plan':
        deletePlanAction();
        break;

    default:
        showDashboard();
        break;
}
?>
