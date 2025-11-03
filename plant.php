<?php require_once __DIR__ . '/partials/header.php';
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM plants WHERE ID = ?");
$stmt->execute([$id]);
$plant = $stmt->fetch();
if(!$plant){ echo "<p class='alert'>Plant not found.</p>"; require_once __DIR__ . '/partials/footer.php'; exit; }
?>
<?php if($plant['Status'] !== 'approved' && !isAdmin() && (!isLoggedIn() || (empty($plant['Created_By']) || $plant['Created_By'] != currentUser()['ID']))): ?>
  <p class='alert'>This plant is not available publicly yet.</p>
  <?php require_once __DIR__ . '/partials/footer.php'; exit; endif; ?>

<div class="flex">
  <div style="flex:2">
    <?php if(!empty($plant['Image'])): ?>
      <img style="max-width:100%;border-radius:1rem" src="uploads/<?= e($plant['Image']) ?>" alt="<?= e($plant['Name']) ?>">
    <?php endif; ?>
  </div>
  <div style="flex:3">
    <h1><?= e($plant['Name']) ?></h1>
    <p><em><?= e($plant['Scientific_Name']) ?></em></p>
    <p><strong>Watering:</strong> <?= e($plant['Watering_Schedule']) ?></p>
    <p><strong>Sunlight:</strong> <?= e($plant['Sunlight']) ?></p>
    <p><strong>Soil:</strong> <?= e($plant['Soil_Type']) ?></p>
    <p><strong>Common Pests & Remedies:</strong><br><?= nl2br(e($plant['Pest_Info'])) ?></p>
    <?php if(isLoggedIn()): ?>
      <form method="post" action="profile_add_plant.php">
        <input type="hidden" name="plant_id" value="<?= e($plant['ID']) ?>">
        <label>Reminder Frequency</label>
        <select name="frequency" required>
          <option value="daily">Daily</option>
          <option value="weekly" selected>Weekly</option>
        </select>
        <button class="btn" type="submit">Add to My Plants</button>
      </form>
    <?php else: ?>
      <p><a class="btn" href="auth/login.php">Login</a> or <a class="btn link" href="auth/register.php">Register</a> to add this plant and get reminders.</p>
    <?php endif; ?>
  </div>
</div>

    <?php
      // contributor
      if(!empty($plant['Created_By'])){
        $u = $pdo->prepare("SELECT Name FROM users WHERE ID = ?");
        $u->execute([$plant['Created_By']]);
        $contrib = $u->fetchColumn();
        if($contrib){
          echo "<p><small>Contributed by: ".e($contrib)."</small></p>";
        }
      }
      if(isAdmin() || (!empty($plant['Created_By']) && isLoggedIn() && $plant['Created_By'] == currentUser()['ID'])){
        echo '<p><a class="btn link" href="contribute.php?id='.e($plant['ID']).'">Edit this plant</a></p>';
      }
    ?>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
