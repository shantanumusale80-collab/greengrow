<?php
// Run this daily via cron: php /path/to/cron_send_reminders.php
require_once __DIR__ . '/config.php';

$today = date('Y-m-d');

// Get due reminders that haven't been sent
$due = $pdo->prepare("
  SELECT r.ID as RID, r.User_id, r.Plant_id, u.Email, u.Name as UserName, p.Name as PlantName
  FROM reminders r
  JOIN users u ON u.ID = r.User_id
  JOIN plants p ON p.ID = r.Plant_id
  WHERE r.Reminder_Date = ? AND r.Sent = 0
");
$due->execute([$today]);
$list = $due->fetchAll();

foreach($list as $row){
    $to = $row['Email'];
    $subject = 'GreenGrow Reminder: Care for ' . $row['PlantName'];
    $message = "Hi {$row['UserName']},\n\nThis is your scheduled reminder to care for your plant: {$row['PlantName']}.\n\nHappy gardening!\n" . SITE_NAME;
    $headers = "From: " . FROM_EMAIL . "\r\n";
    // Attempt to send
    $sent = @mail($to, $subject, $message, $headers);

    if($sent){
        // mark as sent
        $pdo->prepare("UPDATE reminders SET Sent = 1 WHERE ID = ?")->execute([$row['RID']]);
        // schedule next based on user frequency
        $freqStmt = $pdo->prepare("SELECT Frequency FROM user_plants WHERE User_id = ? AND Plant_id = ?");
        $freqStmt->execute([$row['User_id'], $row['Plant_id']]);
        $freq = $freqStmt->fetchColumn();
        $interval = $freq === 'daily' ? '+1 day' : '+7 days';
        $nextDate = date('Y-m-d', strtotime($interval));
        $pdo->prepare("INSERT INTO reminders (User_id, Plant_id, Reminder_Date) VALUES (?,?,?)")
            ->execute([$row['User_id'], $row['Plant_id'], $nextDate]);
    }
}

echo "Processed ".count($list)." reminders for $today\n";
