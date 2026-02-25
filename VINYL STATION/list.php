<?php
session_start();
require __DIR__ . '/config.php';

$search = trim($_GET['search'] ?? '');
$sql = "SELECT id, productname, detail, price, img, color, format, released_date FROM products";
$params = [];
$orderBy = "id DESC"; // ค่าเริ่มต้น: เรียงจากใหม่ไปเก่า

if ($search !== '') {
    // 1. ตรวจสอบว่าเป็นช่วงราคาหรือไม่ (เช่น 500-1500)
    if (preg_match('/^(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)$/', $search, $matches)) {
        $sql .= " WHERE price BETWEEN :min AND :max";
        $params = [':min' => (float)$matches[1], ':max' => (float)$matches[2]];
        $orderBy = "price ASC"; // ถ้าค้นราคา ให้เรียงจากถูกไปแพง
    } else {
        // 2. ค้นหาทั่วไปแบบเน้นความแม่นยำ
        $sql .= " WHERE (productname LIKE :s1 
                  OR detail LIKE :s2 
                  OR color LIKE :s3 
                  OR format LIKE :s4";
        
        $params = [
            ':s1' => "%$search%",
            ':s2' => "%$search%",
            ':s3' => "%$search%",
            ':s4' => "%$search%"
        ];

        // ถ้าสิ่งที่พิมพ์เป็นตัวเลข ให้ค้นหาจากราคาแบบเป๊ะๆ เพิ่มเติม
        if (is_numeric($search)) {
            $sql .= " OR price = :exact_price";
            $params[':exact_price'] = (float)$search;
        }
        
        $sql .= ")";

        /**
         * 3. ระบบจัดลำดับความสำคัญ (Relevance Sorting)
         * - ชื่อสินค้าตรงเป๊ะ (Rank 1)
         * - ชื่อสินค้าขึ้นต้นด้วยคำที่พิมพ์ (Rank 2)
         * - ชื่อสินค้ามีคำที่พิมพ์อยู่ข้างใน (Rank 3)
         * - สีหรือรูปแบบตรงเป๊ะ (Rank 4-5)
         * - คำที่เจอในรายละเอียด (Rank 6)
         */
        $orderBy = "CASE 
            WHEN productname = :o1 THEN 1 
            WHEN productname LIKE :o2 THEN 2 
            WHEN productname LIKE :o3 THEN 3 
            WHEN color LIKE :o4 THEN 4 
            WHEN format LIKE :o5 THEN 5 
            ELSE 6 END, id DESC";
            
        $params[':o1'] = $search;
        $params[':o2'] = "$search%";
        $params[':o3'] = "%$search%";
        $params[':o4'] = $search;
        $params[':o5'] = $search;
    }
}

$sql .= " ORDER BY $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// === ส่วน AJAX Live Search (เหมือนเดิมแต่แม่นยำขึ้น) ===
if (isset($_GET['ajax_live'])) {
    if (!$products) {
        echo '<div style="text-align: left; color: #888; margin-top: 40px; font-size: 18px;">ไม่พบแผ่นเสียงที่คุณกำลังมองหา...</div>';
    } else {
        echo '<div class="grid">';
        foreach ($products as $p): 
            $imgSrc = !empty($p['img']) ? htmlspecialchars($p['img']) : '';
            ?>
            <div class="card">
                <a href="view.php?id=<?= htmlspecialchars($p['id']) ?>" style="display:block;">
                    <div class="img-wrapper">
                        <?php if ($imgSrc): ?>
                            <img src="<?= $imgSrc ?>" alt="cover">
                        <?php else: ?>
                            <div class="no-cover">NO COVER</div>
                        <?php endif; ?>
                        <span class="badge">VINYL</span>
                    </div>
                </a>
                
                <div class="card-content">
                    <a href="view.php?id=<?= htmlspecialchars($p['id']) ?>" class="card-link">
                        <div class="card-name" title="<?= htmlspecialchars($p['productname'] ?? '') ?>">
                            <?= htmlspecialchars($p['productname'] ?? 'Unknown Artist') ?>
                        </div>
                    </a>
                    
                    <div class="card-price">฿<?= number_format($p['price'] ?? 0, 2) ?></div>
                    
                    <div class="actions">
                        <a href="edit.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn-edit">Edit</a>
                        <a href="delete.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this vibe?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach;
        echo '</div>';
    }
    exit;
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>My Vinyl Collection</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <style>
        :root { --bg-color: #111111; --nav-bg: #1a1a1a; --accent: #ff00e6; --text-main: #ffffff; --text-muted: #a0a0a0; --card-bg: transparent; }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: 'Kanit', sans-serif; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        
        .navbar { background-color: var(--nav-bg); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333; position: sticky; top: 0; z-index: 100; }
        .logo { font-family: 'Montserrat', sans-serif; font-weight: 900; font-style: italic; font-size: 28px; color: #fff; text-decoration: none; letter-spacing: 1px; }
        
        .search-container { flex-grow: 1; max-width: 500px; margin: 0 40px; }
        .search-form { display: flex; background-color: #2a2a2a; border-radius: 4px; overflow: hidden; border: 1px solid transparent; transition: 0.3s; }
        .search-form:focus-within { border-color: var(--accent); box-shadow: 0 0 10px rgba(255, 0, 230, 0.2); }
        .search-form input { width: 100%; padding: 12px 20px; background: transparent; border: none; color: #fff; font-family: 'Kanit', sans-serif; font-size: 14px; outline: none; }
        .search-form input::placeholder { color: #888; }
        .search-form button { background: transparent; border: none; color: var(--accent); padding: 0 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        
        .nav-actions { display: flex; align-items: center; gap: 25px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; }
        .nav-actions a { color: var(--text-main); text-decoration: none; transition: color 0.3s; display: flex; align-items: center; gap: 8px; }
        .nav-actions a:hover { color: var(--accent); }
        .nav-actions .cart-link { color: var(--accent); }

        .main-content { padding: 40px; max-width: 1400px; margin: 0 auto; }
        .section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; }
        .section-header h2 { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 32px; margin: 0; letter-spacing: 1px; text-transform: uppercase; }
        .browse-all { color: var(--text-main); text-decoration: none; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 12px; letter-spacing: 1px; border-bottom: 1px solid var(--text-main); padding-bottom: 2px; transition: 0.3s; }
        .browse-all:hover { color: var(--accent); border-color: var(--accent); }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 30px; }
        .card { background: #1c1c1e; border-radius: 16px; display: flex; flex-direction: column; height: 100%; transition: transform 0.3s, box-shadow 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        .card:hover { transform: translateY(-6px); box-shadow: 0 12px 25px rgba(0,0,0,0.6); }
        .img-wrapper { background: #111; aspect-ratio: 1/1; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 16px 16px 0 0; }
        .img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .badge { position: absolute; bottom: 12px; right: 12px; background: rgba(0,0,0,0.8); color: #fff; font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 700; padding: 5px 10px; border-radius: 20px; }
        .card-content { display: flex; flex-direction: column; flex-grow: 1; padding: 20px; }
        .card-link { text-decoration: none; display: block; }
        .card-name { font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 17px; color: #fff; margin-bottom: 8px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; transition: color 0.3s; }
        .card-link:hover .card-name { color: var(--accent); }
        .card-price { color: var(--accent); font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 20px; margin-top: auto; margin-bottom: 15px; }
        .actions { display: flex; gap: 12px; margin-top: auto; }
        .actions a { flex: 1; text-align: center; text-decoration: none; font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 13px; padding: 10px 0; border-radius: 8px; transition: 0.3s; }
        .btn-edit { color: var(--accent); border: 1px solid var(--accent); background: transparent; }
        .btn-edit:hover { background: rgba(255, 0, 230, 0.1); }
        .btn-delete { color: #ff4d4d; border: 1px solid #ff4d4d; background: transparent; }
        .btn-delete:hover { background: rgba(255, 77, 77, 0.1); }
        
        @media (max-width: 768px) { .navbar { flex-direction: column; gap: 15px; padding: 15px; } .search-container { margin: 0; width: 100%; } }
    </style>
</head>
<body>

<div class="navbar">
    <a href="list.php" class="logo">VINYL STATION</a>
    <div class="search-container">
        <form class="search-form" method="GET" action="list.php">
            <input type="text" id="searchInput" name="search" placeholder="พิมพ์ชื่อศิลปิน, สี, ราคา (เช่น 500-1000)..." value="<?= htmlspecialchars($search) ?>" autocomplete="off">
            <button type="submit">🔍</button>
        </form>
    </div>
    <div class="nav-actions">
        <a href="cart.php" class="cart-link">🛒 MY CART (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
        <a href="index.php">+ ADD NEW VIBE</a>
    </div>
</div>

<div class="main-content">
    <div class="section-header">
        <h2>ON SALE / COLLECTION</h2>
        <?php if ($search): ?>
            <a href="list.php" class="browse-all">CLEAR SEARCH</a>
        <?php endif; ?>
    </div>

    <div id="result-container">
        <?php if (!$products): ?>
            <div style="text-align: left; color: #888; margin-top: 40px; font-size: 18px;">ไม่พบแผ่นเสียงที่คุณกำลังมองหา...</div>
        <?php else: ?>
        <div class="grid">
            <?php foreach ($products as $p): ?>
            <div class="card">
                <a href="view.php?id=<?= htmlspecialchars($p['id']) ?>">
                    <div class="img-wrapper">
                        <?php if (!empty($p['img'])): ?>
                            <img src="<?= htmlspecialchars($p['img']) ?>" alt="cover">
                        <?php else: ?>
                            <div class="no-cover">NO COVER</div>
                        <?php endif; ?>
                        <span class="badge">VINYL</span>
                    </div>
                </a>
                <div class="card-content">
                    <a href="view.php?id=<?= htmlspecialchars($p['id']) ?>" class="card-link">
                        <div class="card-name"><?= htmlspecialchars($p['productname'] ?? 'Unknown') ?></div>
                    </a>
                    <div class="card-price">฿<?= number_format($p['price'] ?? 0, 2) ?></div>
                    <div class="actions">
                        <a href="edit.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn-edit">Edit</a>
                        <a href="delete.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const resultContainer = document.getElementById('result-container');
    let timeout = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const val = this.value;
        timeout = setTimeout(() => {
            fetch('list.php?ajax_live=1&search=' + encodeURIComponent(val))
                .then(response => response.text())
                .then(html => {
                    resultContainer.innerHTML = html;
                })
                .catch(err => console.error('Error:', err));
        }, 300);
    });
});
</script>
</body>
</html>