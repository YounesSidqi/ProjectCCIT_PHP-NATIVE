<?php
require_once __DIR__ . '/../config/config.php';

// ─── USER ───────────────────────────────────────────────

function getUserById($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, name, email FROM Users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// ─── DASHBOARD ──────────────────────────────────────────

function getDashboardTasks($userId) {
    global $conn;
    $sql = "SELECT p.plan_id, p.title, p.description, p.urgency_status, p.important_status,
                   u.urgency_name, u.urgency_desk,
                   i.importance_name, i.importance_description
            FROM plans_table p
            LEFT JOIN urgency_level_cagetory u ON p.urgency_status = u.urgency_id
            LEFT JOIN importance_level_category i ON p.important_status = i.importance_id
            WHERE p.user_id = ?
            ORDER BY p.plan_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ─── PLANS ──────────────────────────────────────────────

function getAllPlans($userId) {
    global $conn;
    $sql = "SELECT p.plan_id, p.title, p.description, p.urgency_status, p.important_status,
                   u.urgency_name, u.urgency_desk,
                   i.importance_name, i.importance_description
            FROM plans_table p
            LEFT JOIN urgency_level_cagetory u ON p.urgency_status = u.urgency_id
            LEFT JOIN importance_level_category i ON p.important_status = i.importance_id
            WHERE p.user_id = ?
            ORDER BY p.plan_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getPlanById($planId) {
    global $conn;
    $sql = "SELECT p.plan_id, p.user_id, p.title, p.description, p.urgency_status, p.important_status,
                   u.urgency_name, u.urgency_desk,
                   i.importance_name, i.importance_description
            FROM plans_table p
            LEFT JOIN urgency_level_cagetory u ON p.urgency_status = u.urgency_id
            LEFT JOIN importance_level_category i ON p.important_status = i.importance_id
            WHERE p.plan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $planId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function insertPlan($userId, $title, $description, $urgencyStatus, $importantStatus) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO plans_table (user_id, title, description, urgency_status, important_status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $userId, $title, $description, $urgencyStatus, $importantStatus);
    return $stmt->execute();
}

function updatePlan($planId, $title, $description, $urgencyStatus, $importantStatus) {
    global $conn;
    $stmt = $conn->prepare("UPDATE plans_table SET title = ?, description = ?, urgency_status = ?, important_status = ? WHERE plan_id = ?");
    $stmt->bind_param("ssssi", $title, $description, $urgencyStatus, $importantStatus, $planId);
    return $stmt->execute();
}

function deletePlan($planId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM plans_table WHERE plan_id = ?");
    $stmt->bind_param("i", $planId);
    return $stmt->execute();
}

// ─── NOTES ──────────────────────────────────────────────

function getAllNotes($userId) {
    global $conn;
    $sql = "SELECT n.notes_id, n.user_id, n.plan_id, n.title, n.content, n.image_path,
                   p.title AS plan_title
            FROM notes_table n
            LEFT JOIN plans_table p ON n.plan_id = p.plan_id
            WHERE n.user_id = ?
            ORDER BY n.notes_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getNoteById($noteId) {
    global $conn;
    $sql = "SELECT n.notes_id, n.user_id, n.plan_id, n.title, n.content, n.image_path,
                   p.title AS plan_title
            FROM notes_table n
            LEFT JOIN plans_table p ON n.plan_id = p.plan_id
            WHERE n.notes_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $noteId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function insertNote($userId, $planId, $title, $content, $imagePath) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notes_table (user_id, plan_id, title, content, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $userId, $planId, $title, $content, $imagePath);
    return $stmt->execute();
}

function updateNote($noteId, $planId, $title, $content, $imagePath) {
    global $conn;
    if ($imagePath !== null) {
        $stmt = $conn->prepare("UPDATE notes_table SET plan_id = ?, title = ?, content = ?, image_path = ? WHERE notes_id = ?");
        $stmt->bind_param("isssi", $planId, $title, $content, $imagePath, $noteId);
    } else {
        $stmt = $conn->prepare("UPDATE notes_table SET plan_id = ?, title = ?, content = ? WHERE notes_id = ?");
        $stmt->bind_param("issi", $planId, $title, $content, $noteId);
    }
    return $stmt->execute();
}

function deleteNote($noteId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM notes_table WHERE notes_id = ?");
    $stmt->bind_param("i", $noteId);
    return $stmt->execute();
}

// ─── CATEGORY LOOKUPS ───────────────────────────────────

function getUrgencyCategories() {
    global $conn;
    $result = $conn->query("SELECT urgency_id, urgency_name, urgency_desk FROM urgency_level_cagetory ORDER BY urgency_id ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getImportanceCategories() {
    global $conn;
    $result = $conn->query("SELECT importance_id, importance_name, importance_description FROM importance_level_category ORDER BY importance_id ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>
