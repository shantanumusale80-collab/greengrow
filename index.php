<?php require_once __DIR__ . '/partials/header.php';

// Featured plant of the day (deterministic by date)
$count = $pdo->query("SELECT COUNT(*) as c FROM plants WHERE Is_Public = 1 AND Status = 'approved'")->fetch()['c'] ?? 0;
$featured = null;
if ($count > 0) {
    $dayIndex = intval(date('z')); // 0-365
    $offset = $dayIndex % $count;
    $stmt = $pdo->prepare("SELECT * FROM plants WHERE Is_Public = 1 AND Status = 'approved' ORDER BY ID LIMIT 1 OFFSET :off");
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $featured = $stmt->fetch();
}

$cats = ['Indoor','Outdoor','Herbs','Succulents'];
?>
<section class="hero">
  <div>
    <h1>Welcome to <?= e(SITE_NAME) ?></h1>
    <p>Our mission is to make urban gardening simple and joyful. Explore care guides, add your plants, and get reminders so your green buddies thrive.</p>
    <div class="flex">
      <a class="btn" href="categories.php">Browse Plant Categories</a>
      <?php if(!isLoggedIn()): ?><a class="btn link" href="auth/register.php">Create an account</a><?php endif; ?>
    </div>
  </div>
  <div>
    <?php if ($featured): ?>
      <div class="card">
        <?php if (!empty($featured['Image'])): ?>
          <img src="uploads/<?= e($featured['Image']) ?>" alt="<?= e($featured['Name']) ?>">
        <?php endif; ?>
        <div class="p">
          <span class="badge">Featured Plant of the Day</span>
          <h3><?= e($featured['Name']) ?></h3>
          <p><em><?= e($featured['Scientific_Name']) ?></em></p>
          <p><strong>Water:</strong> <?= e($featured['Watering_Schedule']) ?> <br>
             <strong>Sunlight:</strong> <?= e($featured['Sunlight']) ?></p>
          <a class="btn" href="plant.php?id=<?= e($featured['ID']) ?>">Care Details</a>
        </div>
      </div>
    <?php else: ?>
      <div class="alert">Add plants in the Admin panel to see the featured plant.</div>
    <?php endif; ?>
  </div>
</section>

<h2>Categories</h2>
<div class="card-grid">
  <?php foreach($cats as $c): ?>
    <div class="card">
      <div class="p">
        <h3><?= e($c) ?></h3>
        <p>Explore <?= e($c) ?> plants with care guides.</p>
        <a class="btn" href="plants.php?category=<?= urlencode($c) ?>">View <?= e($c) ?></a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
