<?php require_once __DIR__ . '/partials/header.php';
$cats = ['Indoor','Outdoor','Herbs','Succulents'];
?>
<h1>Plant Categories</h1>
<div class="card-grid">
  <?php foreach($cats as $c): ?>
    <div class="card">
      <div class="p">
        <h3><?= e($c) ?></h3>
        <p>Dive into our curated <?= e($c) ?> plant list.</p>
        <a class="btn" href="plants.php?category=<?= urlencode($c) ?>">Browse</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
