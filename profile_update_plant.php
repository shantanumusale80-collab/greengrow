<?php require_once __DIR__ . '/config.php';
if(!isLoggedIn()) redirect('auth/login.php');

$upid = intval($_POST['upid'] ?? 0);
$freq = $_POST['frequency'] === 'daily' ? 'daily' : 'weekly';

// Update frequency
$stmt = $pdo->prepare("UPDATE user_plants SET Frequency = ? WHERE ID = ? AND User_id = ?");
$stmt->execute([$freq, $upid, currentUser()['ID']]);
redirect('profile.php?ok=1');
