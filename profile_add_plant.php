<?php require_once __DIR__ . '/config.php';
if(!isLoggedIn()) redirect('auth/login.php');

$plant_id = intval($_POST['plant_id'] ?? 0);
$freq = $_POST['frequency'] === 'daily' ? 'daily' : 'weekly';

// Insert or ignore if exists
$stmt = $pdo->prepare("INSERT INTO user_plants (User_id, Plant_id, Frequency) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE Frequency = VALUES(Frequency)");
$stmt->execute([currentUser()['ID'], $plant_id, $freq]);

// Create next reminder for tomorrow (or today) to kick things off
$nextDate = date('Y-m-d', strtotime('+1 day'));
$stmt2 = $pdo->prepare("INSERT INTO reminders (User_id, Plant_id, Reminder_Date) VALUES (?, ?, ?)");
$stmt2->execute([currentUser()['ID'], $plant_id, $nextDate]);

redirect('profile.php?ok=1');
