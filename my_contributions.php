<?php require_once __DIR__ . '/partials/header.php';
if(!isLoggedIn()) redirect('auth/login.php');

$stmt = $pdo->prepare("SELECT * FROM plants WHERE Created_By = ? ORDER BY Created_At DESC");
$stmt->execute([currentUser()['ID']]);
$rows = $stmt->fetchAll();
?>
<h1>My Contributions</h1>
<p><a class="btn" href="contribute.php">Add a Plant</a></p>
<?php if(isset($_GET['ok'])): ?><div class="flash alert">Saved!</div><?php endif; ?>
<table class="table">
  <tr><th>Name</th><th>Category</th><th>Public</th><th>Actions</th></tr>
  <?php foreach($rows as $r): ?>
    <tr>
      <td><?= e($r['Name']) ?></td>
      <td><?= e($r['Category']) ?></td>
      <td><?= $r['Is_Public'] ? 'Yes' : 'No' ?></td>
      <td>
        <a class="btn" href="contribute.php?id=<?= e($r['ID']) ?>">Edit</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
