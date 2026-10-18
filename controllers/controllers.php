<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../models/models.php';

// Simulated auth — hardcode user_id = 1 (ones) for dev.
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}
$userId = $_SESSION['user_id'];
$user = getUserById($userId);
if (!$user) {
    $user = ['id' => 1, 'name' => 'ones', 'email' => 'onesmelon@gmail.com'];
}

// ─── Controller Functions ──────────────────────────────

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

function ajaxUpdateTaskStatus() {
    global $userId;
    
    // Read input from JSON body or $_POST
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    if (!is_array($data) || empty($data)) {
        $data = $_POST;
    }

    $planId = isset($data['plan_id']) ? (int) $data['plan_id'] : 0;
    $isCompleted = isset($data['is_completed']) ? (bool) $data['is_completed'] : false;
    $date = !empty($data['date']) ? $data['date'] : date('Y-m-d');

    if ($planId > 0) {
        updateTaskStatus($planId, $isCompleted);
    }

    $stats = getTaskCompletionStats($userId, $date);

    header('Content-Type: application/json');
    echo json_encode([
        'success'   => true,
        'completed' => (int) $stats['completed'],
        'total'     => (int) $stats['total']
    ]);
    exit();
}

function storeNote() {
    global $userId;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $planId = !empty($_POST['plan_id']) ? (int) $_POST['plan_id'] : null;
        $imagePath = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $filename = 'note_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $imagePath = 'assets/uploads/' . $filename;
            }
        }

        saveNote($userId, $planId, $title, $content, $imagePath);
        header("Location: index.php?page=allnotes");
        exit();
    }
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
    global $userId;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $noteId = (int) ($_POST['notes_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $planId = !empty($_POST['plan_id']) ? (int) $_POST['plan_id'] : null;
        $imagePath = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $filename = 'note_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $imagePath = 'assets/uploads/' . $filename;
            }
        }

        updateNote($noteId, $planId, $title, $content, $imagePath);
        header("Location: index.php?page=note_detail&id=" . $noteId);
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

// ─── Dispatcher ────────────────────────────────────────

$action = $_GET['page'] ?? $_POST['page'] ?? $_GET['action'] ?? $_POST['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard':
        showDashboard();
        break;

    case 'ajaxUpdateTaskStatus':
    case 'ajax_update_task_status':
        ajaxUpdateTaskStatus();
        break;

    case 'store_note':
    case 'add_note':
        storeNote();
        break;

    case 'store_plan':
    case 'add_plan':
        storePlan();
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
        showAllPlans();
        break;

    case 'plan_detail':
        showAllPlans();
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
