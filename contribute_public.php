<?php require_once __DIR__ . '/partials/header.php';

// Generate math captcha numbers & store in session
if (!isset($_SESSION['captcha_a'])) {
  $_SESSION['captcha_a'] = rand(2,9);
  $_SESSION['captcha_b'] = rand(2,9);
}

$err = '';
$ok = false;

if($_SERVER['REQUEST_METHOD']==='POST'){
  $name = trim($_POST['Name'] ?? '');
  $email = trim($_POST['Email'] ?? '');
  $sci = trim($_POST['Scientific_Name'] ?? '');
  $cat = $_POST['Category'] ?? 'Indoor';
  $water = trim($_POST['Watering_Schedule'] ?? '');
  $sun = trim($_POST['Sunlight'] ?? '');
  $soil = trim($_POST['Soil_Type'] ?? '');
  $pest = trim($_POST['Pest_Info'] ?? '');
  $captcha = intval($_POST['captcha'] ?? 0);
  $imageName = '';

  // Validate captcha
  $expected = intval(($_SESSION['captcha_a'] ?? 0)) + intval(($_SESSION['captcha_b'] ?? 0));
  if ($captcha !== $expected) {
    $err = 'Captcha incorrect. Please try again.';
    // regenerate
    $_SESSION['captcha_a'] = rand(2,9);
    $_SESSION['captcha_b'] = rand(2,9);
  } else if(!$name){
    $err = 'Name is required.';
  } else {
    if(isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK){
      $ext = pathinfo($_FILES['Image']['name'], PATHINFO_EXTENSION);
      $imageName = 'plant_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . strtolower($ext);
      move_uploaded_file($_FILES['Image']['tmp_name'], __DIR__ . '/uploads/' . $imageName);
    }
    // Insert as pending, not public, no owner
    $stmt = $pdo->prepare("INSERT INTO plants (Name, Scientific_Name, Category, Watering_Schedule, Sunlight, Soil_Type, Pest_Info, Image, Created_By, Is_Public, Status, Submitted_Email) VALUES (?,?,?,?,?,?,?,?,NULL,0,'pending',?)");
    $stmt->execute([$name,$sci,$cat,$water,$sun,$soil,$pest,$imageName,$email]);
    $ok = true;
    // reset captcha
    unset($_SESSION['captcha_a'], $_SESSION['captcha_b']);
  }
}

?>
<h1>Contribute a Plant (No Login)</h1>
<p>Submit plant details. Submissions go to **admin moderation** before they appear publicly.</p>
<?php if($err): ?><div class="alert"><?= e($err) ?></div><?php endif; ?>
<?php if($ok): ?><div class="alert">Thanks! Your submission is received and awaiting approval.</div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <label>Your Email (optional, for credit/queries)</label>
  <input type="email" name="Email" placeholder="you@example.com">
  <label>Plant Name</label>
  <input name="Name" required>
  <label>Scientific Name</label>
  <input name="Scientific_Name">
  <label>Category</label>
  <select name="Category">
    <option>Indoor</option><option>Outdoor</option><option>Herbs</option><option>Succulents</option>
  </select>
  <label>Watering Schedule</label>
  <input name="Watering_Schedule">
  <label>Sunlight</label>
  <input name="Sunlight">
  <label>Soil Type</label>
  <input name="Soil_Type">
  <label>Common Pests & Remedies</label>
  <textarea name="Pest_Info" rows="4"></textarea>
  <label>Image</label>
  <input type="file" name="Image" accept="image/*">
  <label>Captcha: What is <?= e($_SESSION['captcha_a']) ?> + <?= e($_SESSION['captcha_b']) ?> ?</label>
  <input name="captcha" inputmode="numeric" required>
  <button class="btn" type="submit">Submit for Review</button>
</form>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
