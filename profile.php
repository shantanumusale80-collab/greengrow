<?php require_once __DIR__ . '/partials/header.php';
if(!isLoggedIn()) redirect('auth/login.php');

$user = currentUser();
// Fetch user's plants
$stmt = $pdo->prepare("
  SELECT up.ID as UPID, p.*, up.Frequency, up.Added_Date
  FROM user_plants up
  JOIN plants p ON p.ID = up.Plant_id
  WHERE up.User_id = ?
  ORDER BY p.Name
");
$stmt->execute([$user['ID']]);
$my = $stmt->fetchAll();

// Upcoming reminders (next 14 days)
$rem = $pdo->prepare("
  SELECT r.*, p.Name
  FROM reminders r
  JOIN plants p ON p.ID = r.Plant_id
  WHERE r.User_id = ? AND r.Reminder_Date >= CURDATE() AND r.Reminder_Date <= DATE_ADD(CURDATE(), INTERVAL 14 DAY)
  ORDER BY r.Reminder_Date ASC
");
$rem->execute([$user['ID']]);
$reminders = $rem->fetchAll();
?>
<h1>My Plants</h1>
<?php if(isset($_GET['ok'])): ?><div class="flash alert">Updated!</div><?php endif; ?>
<?php if(!$my): ?><p class="alert">You haven't added any plants yet. Browse plants and add your first!</p><?php endif; ?>
<div class="card-grid">
<?php foreach($my as $p): ?>
  <div class="card">
    <?php if(!empty($p['Image'])): ?>
      <img src="uploads/<?= e($p['Image']) ?>" alt="<?= e($p['Name']) ?>">
    <?php endif; ?>
    <div class="p">
      <h3><?= e($p['Name']) ?></h3>
      <p>Frequency: <strong><?= e($p['Frequency']) ?></strong></p>
      <form method="post" action="profile_update_plant.php" class="flex" style="gap:.5rem;align-items:flex-end">
        <input type="hidden" name="upid" value="<?= e($p['UPID']) ?>">
        <label>Change Frequency</label>
        <select name="frequency">
          <option value="daily" <?= $p['Frequency']==='daily'?'selected':'' ?>>Daily</option>
          <option value="weekly" <?= $p['Frequency']==='weekly'?'selected':'' ?>>Weekly</option>
        </select>
        <button class="btn" type="submit">Save</button>
        <a class="btn link" href="profile_remove_plant.php?upid=<?= e($p['UPID']) ?>" onclick="return confirm('Remove this plant?')">Remove</a>
      </form>
    </div>
  </div>
<?php endforeach; ?>
</div>

<h2>Upcoming Reminders (next 14 days)</h2>
<?php if(!$reminders): ?><p>No reminders scheduled in the next two weeks.</p><?php endif; ?>
<table class="table">
  <tr><th>Date</th><th>Plant</th><th>Status</th></tr>
  <?php foreach($reminders as $r): ?>
    <tr>
      <td><?= e($r['Reminder_Date']) ?></td>
      <td><?= e($r['Name']) ?></td>
      <td><?= $r['Sent'] ? 'Sent' : 'Scheduled' ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
