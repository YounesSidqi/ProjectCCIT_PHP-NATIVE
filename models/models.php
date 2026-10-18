<?php
require_once __DIR__ . '/../config/config.php';

// ─── USER ───────────────────────────────────────────────

function getUserById($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT user_id, user_id AS id, name, email FROM Users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetch() ?: null;
}

// ─── DASHBOARD & TASK COMPLETION ────────────────────────

function getDashboardTasks($userId, $date = null) {
    global $pdo;
    if ($date === null || $date === '') {
        $date = date('Y-m-d');
    }

    $sql = "SELECT p.plan_id, p.user_id, p.title, p.description, p.plan_date, p.urgency_status, p.important_status, p.is_completed,
                   u.urgency_name, u.urgency_desk,
                   i.importance_name, i.importance_description
            FROM plans_table p
            LEFT JOIN urgency_level_cagetory u ON p.urgency_status = u.urgency_id
            LEFT JOIN importance_level_category i ON p.important_status = i.importance_id
            WHERE p.user_id = :user_id AND p.plan_date = :plan_date
            ORDER BY p.plan_id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id'   => $userId,
        ':plan_date' => $date
    ]);
    return $stmt->fetchAll();
}

function getTaskCompletionStats($userId, $date = null) {
    global $pdo;
    if ($date === null || $date === '') {
        $date = date('Y-m-d');
    }

    $sql = "SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) AS completed
            FROM plans_table
            WHERE user_id = :user_id AND plan_date = :plan_date";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id'   => $userId,
        ':plan_date' => $date
    ]);
    $row = $stmt->fetch();

    return [
        'completed' => (int) ($row['completed'] ?? 0),
        'total'     => (int) ($row['total'] ?? 0)
    ];
}

function updateTaskStatus($planId, $isCompleted) {
    global $pdo;
    $status = $isCompleted ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE plans_table SET is_completed = :status WHERE plan_id = :id");
    return $stmt->execute([
        ':status' => $status,
        ':id'     => $planId
    ]);
}

// ─── PLANS ──────────────────────────────────────────────

function savePlan($userId, $title, $description, $planDate, $urgencyStatus, $importantStatus) {
    global $pdo;
    if (empty($planDate)) {
        $planDate = date('Y-m-d');
    }

    $sql = "INSERT INTO plans_table (user_id, title, description, plan_date, urgency_status, important_status, is_completed)
            VALUES (:user_id, :title, :description, :plan_date, :urgency_status, :important_status, 0)";
    
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        ':user_id'          => $userId,
        ':title'            => $title,
        ':description'      => $description,
        ':plan_date'        => $planDate,
        ':urgency_status'   => $urgencyStatus,
        ':important_status' => $importantStatus
    ]);

    return $success ? $pdo->lastInsertId() : false;
}

// Alias for backwards compatibility
function insertPlan($userId, $title, $description, $urgencyStatus, $importantStatus, $planDate = null) {
    return savePlan($userId, $title, $description, $planDate, $urgencyStatus, $importantStatus);
}

function getAllPlans($userId, $date = null) {
    global $pdo;
    $params = [':user_id' => $userId];
    $dateCondition = '';
    
    if (!empty($date)) {
        $dateCondition = ' AND p.plan_date = :plan_date';
        $params[':plan_date'] = $date;
    }

    $sql = "SELECT p.plan_id, p.user_id, p.title, p.description, p.plan_date, p.urgency_status, p.important_status, p.is_completed,
                   u.urgency_name, u.urgency_desk,
                   i.importance_name, i.importance_description
            FROM plans_table p
            LEFT JOIN urgency_level_cagetory u ON p.urgency_status = u.urgency_id
            LEFT JOIN importance_level_category i ON p.important_status = i.importance_id
            WHERE p.user_id = :user_id {$dateCondition}
            ORDER BY p.plan_id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getPlanById($planId) {
    global $pdo;
    $sql = "SELECT p.plan_id, p.user_id, p.title, p.description, p.plan_date, p.urgency_status, p.important_status, p.is_completed,
                   u.urgency_name, u.urgency_desk,
                   i.importance_name, i.importance_description
            FROM plans_table p
            LEFT JOIN urgency_level_cagetory u ON p.urgency_status = u.urgency_id
            LEFT JOIN importance_level_category i ON p.important_status = i.importance_id
            WHERE p.plan_id = :plan_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':plan_id' => $planId]);
    return $stmt->fetch() ?: null;
}

function updatePlan($planId, $title, $description, $urgencyStatus, $importantStatus, $planDate = null) {
    global $pdo;
    if (!empty($planDate)) {
        $stmt = $pdo->prepare("UPDATE plans_table SET title = :title, description = :description, plan_date = :plan_date, urgency_status = :urgency_status, important_status = :important_status WHERE plan_id = :plan_id");
        return $stmt->execute([
            ':title'            => $title,
            ':description'      => $description,
            ':plan_date'        => $planDate,
            ':urgency_status'   => $urgencyStatus,
            ':important_status' => $importantStatus,
            ':plan_id'          => $planId
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE plans_table SET title = :title, description = :description, urgency_status = :urgency_status, important_status = :important_status WHERE plan_id = :plan_id");
        return $stmt->execute([
            ':title'            => $title,
            ':description'      => $description,
            ':urgency_status'   => $urgencyStatus,
            ':important_status' => $importantStatus,
            ':plan_id'          => $planId
        ]);
    }
}

function deletePlan($planId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM plans_table WHERE plan_id = :plan_id");
    return $stmt->execute([':plan_id' => $planId]);
}

// ─── NOTES ──────────────────────────────────────────────

function saveNote($userId, $planId, $title, $content, $imagePath) {
    global $pdo;
    $cleanPlanId = (!empty($planId) && is_numeric($planId) && (int)$planId > 0) ? (int)$planId : null;

    $sql = "INSERT INTO notes_table (user_id, plan_id, title, content, image_path) 
            VALUES (:user_id, :plan_id, :title, :content, :image_path)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    if ($cleanPlanId === null) {
        $stmt->bindValue(':plan_id', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':plan_id', $cleanPlanId, PDO::PARAM_INT);
    }
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);
    $stmt->bindValue(':image_path', $imagePath, $imagePath === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

    $success = $stmt->execute();
    return $success ? $pdo->lastInsertId() : false;
}

// Alias for backwards compatibility
function insertNote($userId, $planId, $title, $content, $imagePath) {
    return saveNote($userId, $planId, $title, $content, $imagePath);
}

function getAllNotes($userId) {
    global $pdo;
    $sql = "SELECT n.notes_id, n.user_id, n.plan_id, n.title, n.content, n.image_path,
                   p.title AS plan_title
            FROM notes_table n
            LEFT JOIN plans_table p ON n.plan_id = p.plan_id
            WHERE n.user_id = :user_id
            ORDER BY n.notes_id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll();
}

function getNoteById($noteId) {
    global $pdo;
    $sql = "SELECT n.notes_id, n.user_id, n.plan_id, n.title, n.content, n.image_path,
                   p.title AS plan_title
            FROM notes_table n
            LEFT JOIN plans_table p ON n.plan_id = p.plan_id
            WHERE n.notes_id = :note_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':note_id' => $noteId]);
    return $stmt->fetch() ?: null;
}

function updateNote($noteId, $planId, $title, $content, $imagePath = null) {
    global $pdo;
    $cleanPlanId = (!empty($planId) && is_numeric($planId) && (int)$planId > 0) ? (int)$planId : null;

    if ($imagePath !== null) {
        $sql = "UPDATE notes_table SET plan_id = :plan_id, title = :title, content = :content, image_path = :image_path WHERE notes_id = :note_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':image_path', $imagePath, PDO::PARAM_STR);
    } else {
        $sql = "UPDATE notes_table SET plan_id = :plan_id, title = :title, content = :content WHERE notes_id = :note_id";
        $stmt = $pdo->prepare($sql);
    }

    if ($cleanPlanId === null) {
        $stmt->bindValue(':plan_id', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':plan_id', $cleanPlanId, PDO::PARAM_INT);
    }
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);
    $stmt->bindValue(':note_id', $noteId, PDO::PARAM_INT);

    return $stmt->execute();
}

function deleteNote($noteId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM notes_table WHERE notes_id = :note_id");
    return $stmt->execute([':note_id' => $noteId]);
}

// ─── CATEGORY LOOKUPS ───────────────────────────────────

function getUrgencyCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT urgency_id, urgency_name, urgency_desk FROM urgency_level_cagetory ORDER BY urgency_id ASC");
    return $stmt->fetchAll();
}

function getImportanceCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT importance_id, importance_name, importance_description FROM importance_level_category ORDER BY importance_id ASC");
    return $stmt->fetchAll();
}
?>
