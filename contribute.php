<?php require_once __DIR__ . '/partials/header.php';
if(!isLoggedIn()) redirect('auth/login.php');

// Owner or admin can edit; anyone logged-in can add
$id = intval($_GET['id'] ?? 0);
$plant = null;
if ($id) {
  $stmt = $pdo->prepare("SELECT * FROM plants WHERE ID = ?");
  $stmt->execute([$id]);
  $plant = $stmt->fetch();
  if(!$plant){ echo "<p class='alert'>Plant not found.</p>"; require_once __DIR__ . '/partials/footer.php'; exit; }
  if(!isAdmin() && $plant['Created_By'] != currentUser()['ID']) {
    echo "<p class='alert'>You can only edit plants you added.</p>";
    require_once __DIR__ . '/partials/footer.php'; exit;
  }
} else {
  $plant = ['ID'=>'','Name'=>'','Scientific_Name'=>'','Category'=>'Indoor','Watering_Schedule'=>'','Sunlight'=>'','Soil_Type'=>'','Pest_Info'=>'','Image'=>'','Is_Public'=>1];
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  $name = trim($_POST['Name'] ?? '');
  $sci = trim($_POST['Scientific_Name'] ?? '');
  $cat = $_POST['Category'] ?? 'Indoor';
  $water = trim($_POST['Watering_Schedule'] ?? '');
  $sun = trim($_POST['Sunlight'] ?? '');
  $soil = trim($_POST['Soil_Type'] ?? '');
  $pest = trim($_POST['Pest_Info'] ?? '');
  $is_public = isset($_POST['Is_Public']) ? 1 : 0;
  $imageName = $_POST['existing_image'] ?? '';

  if(isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK){
    $ext = pathinfo($_FILES['Image']['name'], PATHINFO_EXTENSION);
    $imageName = 'plant_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . strtolower($ext);
    move_uploaded_file($_FILES['Image']['tmp_name'], __DIR__ . '/uploads/' . $imageName);
  }

  if($_POST['id']){
    $idp = intval($_POST['id']);
    // permission check again on POST
    $chk = $pdo->prepare("SELECT Created_By FROM plants WHERE ID = ?");
    $chk->execute([$idp]);
    $owner = $chk->fetchColumn();
    if(isAdmin() || $owner == currentUser()['ID']){
      $stmt = $pdo->prepare("UPDATE plants SET Name=?, Scientific_Name=?, Category=?, Watering_Schedule=?, Sunlight=?, Soil_Type=?, Pest_Info=?, Image=?, Is_Public=? WHERE ID=?");
      $stmt->execute([$name,$sci,$cat,$water,$sun,$soil,$pest,$imageName,$is_public,$idp]);
      redirect('my_contributions.php?ok=1');
    } else {
      echo "<p class='alert'>Not allowed.</p>";
    }
  } else {
    $stmt = $pdo->prepare("INSERT INTO plants (Name, Scientific_Name, Category, Watering_Schedule, Sunlight, Soil_Type, Pest_Info, Image, Created_By, Is_Public) VALUES (?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$name,$sci,$cat,$water,$sun,$soil,$pest,$imageName,currentUser()['ID'],$is_public]);
    redirect('my_contributions.php?ok=1');
  }
}

?>
<h1><?= $plant['ID'] ? 'Edit Plant' : 'Add a Plant' ?></h1>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= e($plant['ID']) ?>">
  <label>Name</label><input name="Name" value="<?= e($plant['Name']) ?>" required>
  <label>Scientific Name</label><input name="Scientific_Name" value="<?= e($plant['Scientific_Name']) ?>">
  <label>Category</label>
  <select name="Category">
    <?php foreach(['Indoor','Outdoor','Herbs','Succulents'] as $c): ?>
      <option value="<?= e($c) ?>" <?= $plant['Category']===$c?'selected':'' ?>><?= e($c) ?></option>
    <?php endforeach; ?>
  </select>
  <label>Watering Schedule</label><input name="Watering_Schedule" value="<?= e($plant['Watering_Schedule']) ?>">
  <label>Sunlight</label><input name="Sunlight" value="<?= e($plant['Sunlight']) ?>">
  <label>Soil Type</label><input name="Soil_Type" value="<?= e($plant['Soil_Type']) ?>">
  <label>Common Pests & Remedies</label><textarea name="Pest_Info" rows="4"><?= e($plant['Pest_Info']) ?></textarea>
  <label>Image</label>
  <?php if($plant['Image']): ?><p>Current: <?= e($plant['Image']) ?></p><?php endif; ?>
  <input type="hidden" name="existing_image" value="<?= e($plant['Image']) ?>">
  <input type="file" name="Image" accept="image/*">
  <label><input type="checkbox" name="Is_Public" value="1" <?= !isset($plant['Is_Public']) || $plant['Is_Public'] ? 'checked' : '' ?>> Make this plant public</label>
  <button class="btn" type="submit">Save</button>
</form>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
