<?php require_once __DIR__ . '/partials/header.php';
$category = $_GET['category'] ?? '';
$valid = ['Indoor','Outdoor','Herbs','Succulents'];
if (!in_array($category, $valid)) { redirect('categories.php'); }
$stmt = $pdo->prepare("SELECT * FROM plants WHERE Category = ? AND Is_Public = 1 AND Status = 'approved' ORDER BY Name");
$stmt->execute([$category]);
$rows = $stmt->fetchAll();
?>
<h1><?= e($category) ?> Plants</h1>
<?php if(!$rows): ?><p class="alert">No plants yet in this category. Check back later.</p><?php endif; ?>
<div class="card-grid">
<?php foreach($rows as $p): ?>
  <div class="card">
    <?php if(!empty($p['Image'])): ?>
      <img src="uploads/<?= e($p['Image']) ?>" alt="<?= e($p['Name']) ?>">
    <?php endif; ?>
    <div class="p">
      <h3><?= e($p['Name']) ?></h3>
      <p><em><?= e($p['Scientific_Name']) ?></em></p>
      <a class="btn" href="plant.php?id=<?= e($p['ID']) ?>">View</a>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
