<?php require_once __DIR__ . '/config.php';
if(!isLoggedIn()) redirect('auth/login.php');
$upid = intval($_GET['upid'] ?? 0);

// Find plant id
$find = $pdo->prepare("SELECT Plant_id FROM user_plants WHERE ID = ? AND User_id = ?");
$find->execute([$upid, currentUser()['ID']]);
$row = $find->fetch();
if($row){
  $pdo->prepare("DELETE FROM user_plants WHERE ID = ?")->execute([$upid]);
  // Optionally also delete unsent future reminders for this plant+user
  $pdo->prepare("DELETE FROM reminders WHERE User_id = ? AND Plant_id = ? AND Sent = 0")->execute([currentUser()['ID'], $row['Plant_id']]);
}
redirect('profile.php?ok=1');
