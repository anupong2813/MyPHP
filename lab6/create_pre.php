<?php
session_start();
require __DIR__ . '/csrf.php';

function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')
) {
    die('Invalid request');
}

$productname = trim($_POST['productname'] ?? '');
$detail      = trim($_POST['detail'] ?? '');
$price       = trim($_POST['price'] ?? '');
$img_url     = trim($_POST['img_url'] ?? '');

$errors = [];

if ($productname === '') $errors[] = 'Product name required';
if ($detail === '')      $errors[] = 'Detail required';
if ($price === '' || !is_numeric($price) || $price <= 0)
    $errors[] = 'Invalid price';

$preview_img = null;
$source = 'none';

/* IMAGE UPLOAD */
if (!empty($_FILES['img_upload']['tmp_name'])) {
    $info = getimagesize($_FILES['img_upload']['tmp_name']);
    if (!$info) $errors[] = 'Invalid image file';

    $extMap = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
    if (!isset($extMap[$info['mime']])) $errors[] = 'Image type not allowed';

    if (!$errors) {
        $tmpDir = __DIR__.'/temp';
        if (!is_dir($tmpDir)) mkdir($tmpDir,0777,true);

        $name = 'pre_'.bin2hex(random_bytes(10)).'.'.$extMap[$info['mime']];
        move_uploaded_file($_FILES['img_upload']['tmp_name'], "$tmpDir/$name");

        $preview_img = "temp/$name";
        $source = 'upload';
    }
}

/* IMAGE URL */
if (!$preview_img && filter_var($img_url,FILTER_VALIDATE_URL)) {
    $preview_img = $img_url;
    $source = 'url';
}

if ($errors) {
    $_SESSION['sticky'] = compact('productname','detail','price','img_url');
    die('<h3>Validation Errors</h3><ul><li>'.implode('</li><li>',$errors).'</li></ul><a href="create.php">Back</a>');
}

/* SAVE PREVIEW TO SESSION */
$_SESSION['preview'] = compact(
    'productname','detail','price','preview_img','source'
);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Preview</title></head>
<body>
<h2>Preview</h2>
<p><b>Name:</b> <?= e($productname) ?></p>
<p><b>Detail:</b> <?= nl2br(e($detail)) ?></p>
<p><b>Price:</b> <?= e($price) ?></p>

<?php if ($preview_img): ?>
<img src="<?= e($preview_img) ?>" width="200">
<?php endif; ?>

<form method="POST" action="create_save.php">
    <button type="submit">Confirm & Save</button>
</form>
<a href="create.php">Back</a>
</body>
</html>
