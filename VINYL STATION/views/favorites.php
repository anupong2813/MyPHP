<?php
session_start();

// --- ป้องกัน Admin เข้าถึงหน้า Favorites ---
if (!empty($_SESSION['is_admin'])) { header("Location: list.php"); exit; }

require __DIR__ . '/../core/config.php';

if (!isset($_SESSION['favorites'])) { $_SESSION['favorites'] = []; }

$action = $_GET['action'] ?? '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$redirect = $_GET['redirect'] ?? 'favorites.php';
$is_ajax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

if ($action === 'add' && $id) {
    if (!in_array($id, $_SESSION['favorites'])) { $_SESSION['favorites'][] = $id; }
    if ($is_ajax) { echo json_encode(['status' => 'success', 'count' => count($_SESSION['favorites'])]); exit; }
    header("Location: " . $redirect); exit;
} elseif ($action === 'remove' && $id) {
    $_SESSION['favorites'] = array_diff($_SESSION['favorites'], [$id]);
    if ($is_ajax) { echo json_encode(['status' => 'success', 'count' => count($_SESSION['favorites'])]); exit; }
    header("Location: " . $redirect); exit;
} elseif ($action === 'clear') {
    $_SESSION['favorites'] = [];
    if ($is_ajax) { echo json_encode(['status' => 'success', 'count' => 0]); exit; }
    header("Location: favorites.php"); exit;
}

$favoriteItems = [];
if (!empty($_SESSION['favorites'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['favorites']), '?'));
    $stmt = $pdo->prepare("SELECT id, productname, price, img FROM products WHERE id IN ($placeholders) ORDER BY id DESC");
    $stmt->execute(array_values($_SESSION['favorites']));
    $favoriteItems = $stmt->fetchAll();
}
function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function getDisplayImg($img) {
    if (empty($img)) return ''; if (filter_var($img, FILTER_VALIDATE_URL)) return $img;
    return '../' . (strpos($img, 'storage/') === 0 ? '' : 'storage/') . $img;
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>My Favorites - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <script>const savedTheme = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', savedTheme);</script>
    <style>
        :root { --bg: #f5f5f7; --card: #ffffff; --text-main: #1d1d1f; --text-muted: #86868b; --border: #e5e5ea; --input-bg: #f5f5f7; --shadow: rgba(0,0,0,0.05); }
        [data-theme="dark"] { --bg: #161618; --card: #1c1c1e; --text-main: #f5f5f7; --text-muted: #86868b; --border: #38383a; --input-bg: #2c2c2e; --shadow: rgba(0,0,0,0.3); }
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Kanit', sans-serif; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; transition: 0.3s; }
        a { text-decoration: none !important; }
        .navbar { background-color: var(--card); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; transition: 0.3s; }
        .logo { font-family: 'Montserrat', sans-serif; font-weight: 900; font-style: italic; font-size: 26px; color: var(--text-main); letter-spacing: 1px; }
        .nav-actions { display: flex; align-items: center; gap: 15px; }
        .nav-btn { padding: 10px 20px; border-radius: 25px; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 13px; text-transform: capitalize; transition: 0.3s; display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .cart-btn { background: #ffffff !important; color: #111111 !important; border: 2px solid #111111 !important; }
        .cart-btn:hover { background: #f0f0f0 !important; }
        .fav-btn { background: transparent !important; color: var(--text-main) !important; border: 2px solid var(--border) !important; }
        .fav-btn:hover { border-color: var(--text-main) !important; }
        
        .theme-btn-inline { background: var(--card); border: 2px solid var(--text-main); color: var(--text-main); width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s; box-shadow: 0 2px 5px var(--shadow); }
        .theme-btn-inline:hover { transform: scale(1.1); background: var(--text-main); color: var(--bg); }

        .main-content { padding: 40px; max-width: 1400px; margin: 0 auto; }
        .section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 20px;}
        .section-header h2 { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 32px; margin: 0; text-transform: uppercase; letter-spacing: 1px; color: var(--text-main); }
        .header-actions { display: flex; gap: 15px; align-items: center; }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 30px; }
        .card { position: relative; background: var(--card); border-radius: 16px; display: flex; flex-direction: column; transition: 0.3s; box-shadow: 0 4px 15px var(--shadow); border: 1px solid var(--border); }
        .card:hover { transform: translateY(-6px); box-shadow: 0 12px 25px var(--shadow); border-color: var(--text-muted); }
        
        .img-wrapper { aspect-ratio: 1/1; position: relative; overflow: hidden; border-radius: 15px 15px 0 0; background: #000000; display: flex; align-items:center; justify-content:center; border-bottom: 1px solid #222;}
        .img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .no-cover { font-family: 'Montserrat', sans-serif; font-weight: 900; color: #555; font-size: 20px;}
        
        .card-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-name { font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 16px; color: var(--text-main); margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; transition: 0.3s; }
        .card-price { color: var(--text-main); font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 20px; margin-top: auto; margin-bottom: 15px; }
        
        .btn-remove-fav { display: block; width: 100%; box-sizing: border-box; text-align: center; padding: 12px; border-radius: 25px; border: 2px solid var(--border); color: var(--text-muted); font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 13px; transition: 0.3s; background: transparent; cursor: pointer; text-transform: capitalize;}
        .btn-remove-fav:hover { color: #ff3b30; border-color: #ff3b30; background: rgba(255, 59, 48, 0.05); }
        
        .empty-state { text-align: center; padding: 80px 0; color: var(--text-muted); font-size: 18px; }
        @media (max-width: 850px) { .navbar { flex-direction: column; gap: 15px; padding: 20px; } }
    </style>
</head>
<body>
<div class="navbar">
    <a href="list.php" class="logo">VINYL STATION</a>
    <div class="nav-actions">
        <a href="favorites.php" class="nav-btn fav-btn" style="border-color: var(--text-main) !important;">Favorites (<span id="fav-count"><?= count($_SESSION['favorites']) ?></span>)</a>
        <a href="cart.php" class="nav-btn cart-btn">My Cart (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
    </div>
</div>

<div class="main-content">
    <div class="section-header">
        <h2>MY FAVORITES</h2>
        <div class="header-actions">
            <?php if (!empty($favoriteItems)): ?>
                <a href="favorites.php?action=clear" style="color: var(--text-muted); font-size: 12px; font-weight: 800;" onclick="return confirm('ต้องการล้างรายการโปรดทั้งหมด?')">CLEAR ALL</a>
            <?php endif; ?>
            <button id="theme-toggle" class="theme-btn-inline">🌙</button>
        </div>
    </div>

    <?php if (empty($favoriteItems)): ?>
        <div class="empty-state">
            <div style="font-size: 48px; margin-bottom: 15px;">🤍</div>
            ยังไม่มีรายการโปรด<br><br>
            <a href="list.php" style="color: var(--text-main); text-decoration: underline;">กลับไปเลือกดูแผ่นเสียง</a>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($favoriteItems as $p): $imgSrc = getDisplayImg($p['img']); ?>
            <div class="card">
                <a href="view.php?id=<?= $p['id'] ?>">
                    <div class="img-wrapper">
                        <?php if ($imgSrc): ?><img src="<?= e($imgSrc) ?>" alt="cover"><?php else: ?><div class="no-cover">NO COVER</div><?php endif; ?>
                    </div>
                </a>
                <div class="card-content">
                    <a href="view.php?id=<?= $p['id'] ?>" class="card-link"><div class="card-name"><?= e($p['productname']) ?></div></a>
                    <div class="card-price">฿<?= number_format($p['price'], 2) ?></div>
                    <a href="favorites.php?action=remove&id=<?= $p['id'] ?>&redirect=favorites.php" class="btn-remove-fav">Remove</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('theme-toggle');
    if(btn) {
        const updateIcon = (t) => btn.innerHTML = t === 'dark' ? '☀️' : '🌙';
        updateIcon(document.documentElement.getAttribute('data-theme'));
        btn.addEventListener('click', () => {
            const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next); updateIcon(next);
        });
    }
});
</script>
</body>
</html>