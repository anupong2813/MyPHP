<?php
// list.php
require __DIR__ . '/config.php';

// Fetch products
$sql = "SELECT id, productname, detail, price, img FROM Nindam_Products ORDER BY id DESC";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Product List</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body { font-family: system-ui, Arial, sans-serif; padding: 24px; }
.container { max-width: 1000px; margin: auto; }
table { width: 100%; border-collapse: collapse; margin-top: 16px; }
th, td { border: 1px solid #ddd; padding: 10px; vertical-align: top; }
th { background: #f5f5f5; text-align: left; }
img { max-width: 120px; height: auto; border-radius: 6px; }
.price { text-align: right; }
.actions a { margin-right: 8px; text-decoration: none; color: #0d6efd; }
</style>
</head>
<body>

<div class="container">
<h1>All Products</h1>

<p>
  <a href="create.php">➕ Add New Product</a>
</p>

<?php if (!$products): ?>
  <p><em>No products found.</em></p>
<?php else: ?>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Image</th>
      <th>Product Name</th>
      <th>Detail</th>
      <th class="price">Price</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($products as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['id']) ?></td>
        <td>
          <?php if (!empty($p['img'])): ?>
            <?php if (preg_match('~^https?://~i', $p['img'])): ?>
              <img src="<?= htmlspecialchars($p['img']) ?>" alt="">
            <?php else: ?>
              <img src="<?= htmlspecialchars($p['img']) ?>" alt="">
            <?php endif; ?>
          <?php else: ?>
            <em>No image</em>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($p['productname']) ?></td>
        <td><?= nl2br(htmlspecialchars($p['detail'])) ?></td>
        <td class="price"><?= number_format($p['price'], 2) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

</div>

</body>
</html>
