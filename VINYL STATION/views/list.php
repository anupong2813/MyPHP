<?php
session_start();
require __DIR__ . '/../core/config.php';

if (!isset($_SESSION['favorites'])) { $_SESSION['favorites'] = []; }
$isAdmin = !empty($_SESSION['is_admin']);

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$sort = trim($_GET['sort'] ?? 'newest');

$sql = "SELECT id, productname, detail, price, img, color, format, released_date, category FROM products WHERE 1=1";
$params = [];

// เงื่อนไขค้นหา (เพิ่ม category LIKE :s5 เพื่อให้ค้นหาแนวเพลงผ่านช่อง Search ได้)
if ($search !== '') {
    if (preg_match('/^(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)$/', $search, $matches)) {
        $sql .= " AND price BETWEEN :min AND :max";
        $params[':min'] = (float)$matches[1]; $params[':max'] = (float)$matches[2];
    } else {
        $sql .= " AND (productname LIKE :s1 OR detail LIKE :s2 OR color LIKE :s3 OR format LIKE :s4 OR category LIKE :s5";
        $params[':s1'] = "%$search%"; $params[':s2'] = "%$search%"; $params[':s3'] = "%$search%"; $params[':s4'] = "%$search%"; $params[':s5'] = "%$search%";
        if (is_numeric($search)) { $sql .= " OR price = :exact_price"; $params[':exact_price'] = (float)$search; }
        $sql .= ")";
    }
}

// เงื่อนไขหมวดหมู่จาก Dropdown (เปลี่ยนจาก = เป็น LIKE เพื่อให้เจอแนวเพลงที่ผสมกัน)
if ($category !== '') {
    $sql .= " AND category LIKE :cat";
    $params[':cat'] = "%$category%";
}

// เงื่อนไขการจัดเรียง
if ($sort === 'price_asc') { $orderBy = "price ASC"; }
elseif ($sort === 'price_desc') { $orderBy = "price DESC"; }
else { $orderBy = "id DESC"; }

$sql .= " ORDER BY $orderBy";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

function getDisplayImg($img) {
    if (empty($img)) return '';
    if (filter_var($img, FILTER_VALIDATE_URL)) return $img;
    return '../' . (strpos($img, 'storage/') === 0 ? '' : 'storage/') . $img;
}

if (isset($_GET['ajax_live'])) {
    if (!$products) { echo '<div style="text-align: left; color: var(--text-muted); margin-top: 40px; font-size: 18px;">ไม่พบรายการที่ค้นหา...</div>'; } else {
        echo '<div class="grid">';
        foreach ($products as $p): 
            $imgSrc = getDisplayImg($p['img']); 
            $is_fav = in_array($p['id'], $_SESSION['favorites']);
            ?>
            <div class="card">
                <?php if (!$isAdmin): ?>
                <a href="javascript:void(0);" onclick="toggleFavorite(event, <?= $p['id'] ?>, this)" class="fav-icon-card" data-fav="<?= $is_fav ? '1' : '0' ?>" title="<?= $is_fav ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                    <?= $is_fav ? '❤️' : '🤍' ?>
                </a>
                <?php endif; ?>
                <a href="view.php?id=<?= $p['id'] ?>"><div class="img-wrapper">
                <?php if ($imgSrc): ?><img src="<?= htmlspecialchars($imgSrc) ?>" alt="cover"><?php else: ?><div class="no-cover">NO COVER</div><?php endif; ?>
                <span class="badge">VINYL</span></div></a><div class="card-content"><a href="view.php?id=<?= $p['id'] ?>" class="card-link"><div class="card-name"><?= htmlspecialchars($p['productname']) ?></div></a>
                <div class="card-price">฿<?= number_format($p['price'], 2) ?></div>
                <?php if ($isAdmin): ?>
                <div class="actions">
                <a href="../crud/edit.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn-edit">Edit</a>
                <a href="../crud/delete.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                </div>
                <?php endif; ?>
            </div></div>
        <?php endforeach; echo '</div>';
    } exit;
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>My Vinyl Collection</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <script>const savedTheme = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', savedTheme);</script>
    <style>
        :root { --bg: #f5f5f7; --card: #ffffff; --text-main: #1d1d1f; --text-muted: #86868b; --border: #e5e5ea; --input-bg: #f5f5f7; --shadow: rgba(0,0,0,0.05); }
        [data-theme="dark"] { --bg: #161618; --card: #1c1c1e; --text-main: #f5f5f7; --text-muted: #86868b; --border: #38383a; --input-bg: #2c2c2e; --shadow: rgba(0,0,0,0.3); }
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Kanit', sans-serif; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; transition: 0.3s; }
        a { text-decoration: none !important; }
        .navbar { background-color: var(--card); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; transition: 0.3s; flex-wrap: wrap; gap: 15px;}
        .logo { font-family: 'Montserrat', sans-serif; font-weight: 900; font-style: italic; font-size: 26px; color: var(--text-main); letter-spacing: 1px; }
        
        .filter-group { flex-grow: 1; max-width: 600px; margin: 0 20px; display: flex; gap: 8px; background-color: var(--input-bg); border-radius: 25px; padding: 5px 15px; border: 1px solid var(--border); transition: 0.3s; align-items: center; }
        .filter-group:focus-within { border-color: var(--text-main); }
        
        .filter-group input, .filter-group select { background: transparent; border: none; color: var(--text-main); font-size: 13px; font-weight:600; outline: none; padding: 8px; font-family: 'Kanit', sans-serif; cursor: pointer; text-transform: uppercase; text-align: center; text-align-last: center; }
        .filter-group input { flex-grow: 1; cursor: text; text-transform: none; font-weight:400; text-align: left;}
        
        .filter-group input::placeholder { color: var(--text-muted); }
        .filter-group select option { background: var(--bg); color: var(--text-main); }
        .divider { width: 1px; height: 20px; background: var(--border); margin: 0 5px; }
        
        .nav-actions { display: flex; align-items: center; gap: 15px; }
        .nav-btn { padding: 10px 20px; border-radius: 25px; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 13px; text-transform: capitalize; transition: 0.3s; display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .cart-btn { background: #ffffff !important; color: #111111 !important; border: 2px solid #111111 !important; }
        .cart-btn:hover { background: #f0f0f0 !important; }
        .add-btn { background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important; }
        .add-btn:hover { background: #333333 !important; }
        .fav-btn { background: transparent !important; color: var(--text-main) !important; border: 2px solid var(--border) !important; }
        .fav-btn:hover { border-color: var(--text-main) !important; }
        .logout-btn { border: 1px solid var(--border); color: var(--text-muted); background: transparent; }
        .logout-btn:hover { color: var(--text-main); border-color: var(--text-main); }
        
        .theme-btn-inline { background: var(--card); border: 2px solid var(--text-main); color: var(--text-main); width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s; box-shadow: 0 2px 5px var(--shadow); }
        .theme-btn-inline:hover { transform: scale(1.1); background: var(--text-main); color: var(--bg); }

        .main-content { padding: 40px; max-width: 1400px; margin: 0 auto; }
        .section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; }
        .section-header h2 { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 32px; margin: 0; text-transform: uppercase; letter-spacing: 1px; color: var(--text-main); }
        .header-actions { display: flex; gap: 15px; align-items: center; }
        .clear-search { color: var(--text-muted); font-size: 12px; font-weight: 800; transition: 0.3s; }
        .clear-search:hover { color: var(--text-main); }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 30px; }
        .card { position: relative; background: var(--card); border-radius: 16px; display: flex; flex-direction: column; transition: 0.3s; box-shadow: 0 4px 15px var(--shadow); border: 1px solid var(--border); }
        .card:hover { transform: translateY(-6px); box-shadow: 0 12px 25px var(--shadow); border-color: var(--text-muted); }
        
        .fav-icon-card { position: absolute; top: 12px; right: 12px; z-index: 10; background: var(--card); border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: 0.3s; border: 1px solid var(--border); font-size: 15px;}
        .fav-icon-card:hover { transform: scale(1.1); }

        .img-wrapper { aspect-ratio: 1/1; position: relative; overflow: hidden; border-radius: 15px 15px 0 0; background: #000000; display: flex; align-items:center; justify-content:center; border-bottom: 1px solid #222;}
        .img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .no-cover { font-family: 'Montserrat', sans-serif; font-weight: 900; color: #555; font-size: 20px;}
        .badge { position: absolute; bottom: 12px; right: 12px; background: var(--text-main); color: var(--bg); border: 1px solid var(--border); font-size: 10px; font-weight: 700; padding: 5px 10px; border-radius: 20px; transition: 0.3s;}
        
        .card-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-name { font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 16px; color: var(--text-main); margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; transition: 0.3s; }
        .card-link:hover .card-name { opacity: 0.7; }
        .card-price { color: var(--text-main); font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 20px; margin-top: auto; margin-bottom: 15px; }
        
        .actions { display: flex; gap: 10px; }
        .actions a { flex: 1; text-align: center; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 12px; padding: 10px 0; border-radius: 25px; transition: 0.3s; text-transform: capitalize;}
        .btn-edit { background: #ffffff !important; color: #111111 !important; border: 2px solid #111111 !important; }
        .btn-edit:hover { background: #f0f0f0 !important; }
        .btn-delete { background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important; }
        .btn-delete:hover { background: #333333 !important; }
        @media (max-width: 900px) { .filter-group { max-width: 100%; margin: 10px 0;} }
    </style>
</head>
<body>
<div class="navbar">
    <a href="list.php" class="logo">VINYL STATION</a>
    
    <form class="filter-group" id="filterForm">
        <input type="text" name="search" id="searchInput" placeholder="Search albums or genres..." value="<?= htmlspecialchars($search) ?>" autocomplete="off">
        <div class="divider"></div>
        <select name="category" id="catFilter">
            <option value="">ALL GENRES</option>
            <option value="Pop" <?= $category === 'Pop' ? 'selected' : '' ?>>POP</option>
            <option value="Rock" <?= $category === 'Rock' ? 'selected' : '' ?>>ROCK</option>
            <option value="R&B" <?= $category === 'R&B' ? 'selected' : '' ?>>R&B</option>
            <option value="Hip-Hop" <?= $category === 'Hip-Hop' ? 'selected' : '' ?>>HIP-HOP</option>
            <option value="K-Pop" <?= $category === 'K-Pop' ? 'selected' : '' ?>>K-POP</option>
            <option value="Jazz" <?= $category === 'Jazz' ? 'selected' : '' ?>>JAZZ</option>
            <option value="Electronic" <?= $category === 'Electronic' ? 'selected' : '' ?>>ELECTRONIC</option>
            <option value="Other" <?= $category === 'Other' ? 'selected' : '' ?>>OTHER</option>
        </select>
        <div class="divider"></div>
        <select name="sort" id="sortFilter">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>NEWEST</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>PRICE: HIGH TO LOW</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>PRICE: LOW TO HIGH</option>
        </select>
    </form>

    <div class="nav-actions">
        <?php if ($isAdmin): ?>
            <a href="../index.php" class="nav-btn add-btn">+ Add New Vibe</a>
            <a href="../logout.php" class="nav-btn logout-btn">Logout (Admin)</a>
        <?php else: ?>
            <a href="favorites.php" class="nav-btn fav-btn">Favorites (<span id="fav-count"><?= count($_SESSION['favorites']) ?></span>)</a>
            <a href="cart.php" class="nav-btn cart-btn">My Cart (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
            <a href="../login.php" class="nav-btn add-btn" style="text-transform: uppercase;">Admin Login</a>
        <?php endif; ?>
    </div>
</div>
<div class="main-content">
    <div class="section-header">
        <h2>ON SALE / COLLECTION</h2>
        <div class="header-actions">
            <?php if ($search || $category || $sort !== 'newest'): ?><a href="list.php" class="clear-search">CLEAR FILTERS</a><?php endif; ?>
            <button id="theme-toggle" class="theme-btn-inline">🌙</button>
        </div>
    </div>
    <div id="result-container">
        <?php if (!$products): ?>
            <div style="text-align: left; color: var(--text-muted); margin-top: 40px; font-size: 18px;">ไม่พบรายการที่ค้นหา...</div>
        <?php else: ?>
        <div class="grid">
            <?php foreach ($products as $p): 
                $imgSrc = getDisplayImg($p['img']); 
                $is_fav = in_array($p['id'], $_SESSION['favorites']);
            ?>
            <div class="card">
                <?php if (!$isAdmin): ?>
                <a href="javascript:void(0);" onclick="toggleFavorite(event, <?= $p['id'] ?>, this)" class="fav-icon-card" data-fav="<?= $is_fav ? '1' : '0' ?>" title="<?= $is_fav ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                    <?= $is_fav ? '❤️' : '🤍' ?>
                </a>
                <?php endif; ?>

                <a href="view.php?id=<?= $p['id'] ?>">
                    <div class="img-wrapper">
                        <?php if ($imgSrc): ?><img src="<?= htmlspecialchars($imgSrc) ?>" alt=""><?php else: ?><div class="no-cover">NO COVER</div><?php endif; ?>
                        <span class="badge">VINYL</span>
                    </div>
                </a>
                <div class="card-content">
                    <a href="view.php?id=<?= $p['id'] ?>" class="card-link"><div class="card-name"><?= htmlspecialchars($p['productname']) ?></div></a>
                    <div class="card-price">฿<?= number_format($p['price'], 2) ?></div>
                    
                    <?php if ($isAdmin): ?>
                    <div class="actions">
                        <a href="../crud/edit.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn-edit">Edit</a>
                        <a href="../crud/delete.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<script>
function fetchResults() {
    const search = document.getElementById('searchInput').value;
    const category = document.getElementById('catFilter').value;
    const sort = document.getElementById('sortFilter').value;
    fetch(`list.php?ajax_live=1&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&sort=${encodeURIComponent(sort)}`)
        .then(res => res.text())
        .then(html => { document.getElementById('result-container').innerHTML = html; });
}

function toggleFavorite(e, id, el) {
    e.preventDefault();
    const isFav = el.getAttribute('data-fav') === '1';
    const action = isFav ? 'remove' : 'add';
    
    fetch(`favorites.php?action=${action}&id=${id}&ajax=1`)
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                if (isFav) {
                    el.setAttribute('data-fav', '0'); el.innerHTML = '🤍'; el.title = 'Add to Favorites';
                } else {
                    el.setAttribute('data-fav', '1'); el.innerHTML = '❤️'; el.title = 'Remove from Favorites';
                }
                const countSpan = document.getElementById('fav-count');
                if (countSpan) countSpan.innerText = data.count;
            }
        }).catch(err => console.error(err));
}

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

    let timeout = null;
    document.getElementById('searchInput').addEventListener('input', () => {
        clearTimeout(timeout); timeout = setTimeout(fetchResults, 300);
    });
    document.getElementById('catFilter').addEventListener('change', fetchResults);
    document.getElementById('sortFilter').addEventListener('change', fetchResults);
});
</script>
</body>
</html>