<?php
session_start();
require __DIR__ . '/../core/config.php';

if (!isset($_SESSION['favorites'])) { $_SESSION['favorites'] = []; }
$isAdmin = !empty($_SESSION['is_admin']);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { die('ไม่พบรหัสแผ่นเสียง'); }
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { die('ไม่พบแผ่นเสียงที่คุณต้องการ'); }

// เช็คสถานะรายการโปรด
$is_fav = in_array($product['id'], $_SESSION['favorites']);

$embed_url = '';
if (!empty($product['spotify_url'])) {
    $embed_url = str_replace("https://open.spotify.com/", "https://open.spotify.com/embed/", $product['spotify_url']);
    $embed_url = explode('?', $embed_url)[0]; 
}

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function getDisplayImg($img) {
    if (empty($img)) return '';
    if (filter_var($img, FILTER_VALIDATE_URL)) return $img;
    return '../' . (strpos($img, 'storage/') === 0 ? '' : 'storage/') . $img;
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title><?= e($product['productname']) ?> - VINYL STATION</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Montserrat:ital,wght@0,400;0,700;0,900;1,900&display=swap" rel="stylesheet">
    <script>const savedTheme = localStorage.getItem('theme') || 'dark'; document.documentElement.setAttribute('data-theme', savedTheme);</script>
    <style>
        :root { --bg: #f5f5f7; --card: #ffffff; --text-main: #1d1d1f; --text-muted: #86868b; --border: #e5e5ea; --input-bg: #f5f5f7; --shadow: rgba(0,0,0,0.05); }
        [data-theme="dark"] { --bg: #161618; --card: #1c1c1e; --text-main: #f5f5f7; --text-muted: #86868b; --border: #38383a; --input-bg: #2c2c2e; --shadow: rgba(0,0,0,0.3); }
        body { background-color: var(--bg); color: var(--text-main); font-family: 'Kanit', sans-serif; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; transition: 0.3s;}
        
        .navbar { background-color: var(--card); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); transition: 0.3s;}
        .logo { font-family: 'Montserrat', sans-serif; font-weight: 900; font-style: italic; font-size: 28px; color: var(--text-main); text-decoration: none; letter-spacing: 1px; }
        .nav-actions { display: flex; align-items: center; gap: 15px; }
        .nav-btn { padding: 10px 20px; border-radius: 25px; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 13px; text-transform: capitalize; transition: 0.3s; display: flex; align-items: center; gap: 8px; cursor: pointer; text-decoration: none; }
        .cart-btn { background: #ffffff !important; color: #111111 !important; border: 2px solid #111111 !important; }
        .cart-btn:hover { background: #f0f0f0 !important; }
        .fav-btn { background: transparent !important; color: var(--text-main) !important; border: 2px solid var(--border) !important; }
        .fav-btn:hover { border-color: var(--text-main) !important; }
        .add-btn { background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important; }
        .add-btn:hover { background: #333333 !important; }
        .logout-btn { border: 1px solid var(--border); color: var(--text-muted); background: transparent; }
        .logout-btn:hover { color: var(--text-main); border-color: var(--text-main); }
        
        .view-container { max-width: 1200px; margin: 50px auto; padding: 0 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; position: relative;}
        
        .view-image-wrapper { background: transparent; display: flex; align-items: center; justify-content: center; position: relative; padding: 40px; border-radius: 12px; }
        .view-img-inner { background: #000000; width: 100%; max-width: 500px; aspect-ratio: 1/1; border-radius: 8px; box-shadow: 0 15px 30px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid #222; }
        .view-img-inner img { width: 100%; height: 100%; object-fit: cover; }
        
        .badge { position: absolute; top: 20px; right: 20px; background: var(--text-main); color: var(--bg); font-family: 'Montserrat', sans-serif; font-size: 12px; font-weight: 800; padding: 6px 14px; border-radius: 20px; transition: 0.3s;}
        .back-btn { position: absolute; top: 20px; left: 20px; color: var(--text-main); text-decoration: none; font-size: 24px; transition: 0.3s; font-weight: 900;}
        .back-btn:hover { opacity: 0.6; }

        .view-info { display: flex; flex-direction: column; position: relative; }
        
        .theme-float-btn { position: absolute; top: 0; right: 0; background: var(--card); border: 2px solid var(--text-main); color: var(--text-main); width: 38px; height: 38px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s; box-shadow: 0 2px 5px var(--shadow); z-index: 10; }
        .theme-float-btn:hover { transform: scale(1.1); background: var(--text-main); color: var(--bg); }
        
        .view-title { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 36px; margin: 0; text-transform: uppercase; line-height: 1.1; word-break: break-word; padding-right: 50px; }
        
        .view-subtitle { color: var(--text-main); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700; margin-top: 8px; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px; display: inline-block; padding: 5px 12px; border: 1px solid var(--border); border-radius: 20px;}
        
        .price-fav-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .view-price { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 28px; color: var(--text-main); }
        .btn-add-fav { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 25px; border: 2px solid var(--border); background: transparent; color: var(--text-main); font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 12px; text-decoration: none; transition: 0.3s; cursor: pointer; }
        .btn-add-fav:hover { border-color: var(--text-main); }
        .btn-add-fav.active { border-color: #ff3b30; color: #ff3b30; background: rgba(255, 59, 48, 0.05); }

        .btn-group { display: flex; gap: 15px; margin-bottom: 30px; }
        .btn-cart { background: #ffffff !important; color: #111111 !important; border: 2px solid #111111 !important; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 14px; padding: 12px 20px; border-radius: 25px; cursor: pointer; transition: 0.3s; text-transform: capitalize; width: 100%; display: flex; justify-content: center; align-items: center;}
        .btn-cart:hover { background: #f0f0f0 !important; }
        .btn-buy { background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important; font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 14px; padding: 12px 20px; border-radius: 25px; cursor: pointer; transition: 0.3s; text-transform: capitalize; width: 100%; display: flex; justify-content: center; align-items: center;}
        .btn-buy:hover { background: #333333 !important; }

        .detail-box { border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 25px 0; margin-bottom: 30px; }
        .detail-text { font-size: 14px; color: var(--text-muted); line-height: 1.6; }
        .specs-list { list-style: none; padding: 0; margin: 0; }
        .specs-list li { display: flex; align-items: center; gap: 15px; font-size: 15px; color: var(--text-muted); margin-bottom: 15px; }
        .specs-list li strong { color: var(--text-main); font-family: 'Montserrat', sans-serif; font-weight: 700; margin-right: 5px; }
        .spec-icon { color: var(--text-main); width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; }
        .spec-icon svg { width: 100%; height: 100%; }

        @media (max-width: 900px) { .view-container { grid-template-columns: 1fr; gap: 40px; } }
    </style>
</head>
<body>
<div class="navbar">
    <a href="list.php" class="logo">VINYL STATION</a>
    <div class="nav-actions">
        <?php if ($isAdmin): ?>
            <a href="../admin_orders.php" class="nav-btn add-btn" style="background: #ffffff !important; color: #111111 !important; border: 2px solid #111111 !important;">📦 Orders</a>
            <a href="../index.php" class="nav-btn add-btn" style="background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important;">+ Add New Vibe</a>
            <a href="../logout.php" class="nav-btn logout-btn" style="border: 1px solid var(--border); background: transparent; color: var(--text-muted);">Logout</a>
        <?php else: ?>
            <a href="favorites.php" class="nav-btn fav-btn">Favorites (<span id="fav-count"><?= count($_SESSION['favorites']) ?></span>)</a>
            <a href="cart.php" class="nav-btn cart-btn">My Cart (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
            <a href="../login.php" class="nav-btn add-btn" style="text-transform: uppercase; background: #111111 !important; color: #ffffff !important; border: 2px solid #111111 !important;">Admin Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="view-container">
    <div class="view-image-wrapper">
        <a href="list.php" class="back-btn" title="Back to Collection">❮</a>
        <span class="badge">VINYL</span>
        <div class="view-img-inner">
            <?php $imgSrc = getDisplayImg($product['img']); if ($imgSrc): ?>
                <img src="<?= e($imgSrc) ?>" alt="cover">
            <?php else: ?>
                <div style="font-family: 'Montserrat', sans-serif; font-weight: 900; color:#555; font-size:24px;">NO COVER</div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="view-info">
        <button id="theme-toggle" class="theme-float-btn">🌙</button>

        <h1 class="view-title"><?= e($product['productname']) ?></h1>
        
        <div class="view-subtitle">🎵 <?= e($product['category'] ?? 'Other') ?></div>
        
        <div class="price-fav-row">
            <div class="view-price">฿<?= number_format($product['price'], 2) ?></div>
            <?php if (!$isAdmin): ?>
            <a href="javascript:void(0);" onclick="toggleFavView(event, <?= $product['id'] ?>, this)" class="btn-add-fav <?= $is_fav ? 'active' : '' ?>" data-fav="<?= $is_fav ? '1' : '0' ?>">
                <?= $is_fav ? '❤️ Saved' : '🤍 Add to Favorites' ?>
            </a>
            <?php endif; ?>
        </div>
        
        <div class="btn-group">
            <?php if ($product['stock'] <= 0): ?>
                <div style="color: #ff3b30; font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 24px; padding: 10px 0; letter-spacing: 2px;">SOLD OUT</div>
            <?php elseif (!$isAdmin): ?>
                <form action="cart.php?action=add" method="POST" style="display:flex; width: 100%; gap: 15px;">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" class="btn-cart">Add to Cart</button>
                    <a href="../checkout.php?buy_now=<?= $product['id'] ?>" class="btn-buy" style="text-decoration:none;">Buy Now</a>
                </form>
            <?php else: ?>
                <a href="../crud/edit.php?id=<?= $product['id'] ?>" class="btn-cart" style="text-decoration:none;">Edit Vibe</a>
                <a href="../crud/delete.php?id=<?= $product['id'] ?>" class="btn-buy" style="text-decoration:none;" onclick="return confirm('Are you sure you want to delete this vibe?')">Delete Vibe</a>
            <?php endif; ?>
        </div>

        <div class="detail-box"><div class="detail-text"><?= nl2br(e($product['detail'])) ?></div></div>
        
        <?php if (!empty($embed_url)): ?>
            <div style="margin-bottom: 30px;">
                <iframe style="border-radius:12px" src="<?= e($embed_url) ?>?utm_source=generator&theme=0" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
            </div>
        <?php endif; ?>
        
        <ul class="specs-list">
            <li><div class="spec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="10.5" r="1.5"></circle><circle cx="8.5" cy="10.5" r="1.5"></circle><circle cx="11" cy="7" r="1.5"></circle><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.6-.7 1.6-1.6 0-.4-.1-.8-.4-1.1-.3-.3-.5-.7-.5-1.1 0-.9.7-1.6 1.6-1.6H16c3.3 0 6-2.7 6-6 0-5.5-4.5-10-10-10z"></path></svg></div>
                <div><strong>Color:</strong> <?= e($product['color']) ?: 'Standard Black' ?></div></li>
            <li><div class="spec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg></div>
                <div><strong>Format:</strong> <?= e($product['format']) ?: 'Vinyl 1LP' ?></div></li>
            <li><div class="spec-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><polygon points="12 14 13.5 17 17 17.5 14.5 20 15 23.5 12 21.5 9 23.5 9.5 20 7 17.5 10.5 17 12 14"></polygon></svg></div>
                <div><strong>First released:</strong> <?= e($product['released_date']) ?: 'Unknown' ?></div></li>
        </ul>
    </div>
</div>
<script>
function toggleFavView(e, id, el) {
    e.preventDefault();
    const isFav = el.getAttribute('data-fav') === '1';
    const action = isFav ? 'remove' : 'add';
    
    fetch(`favorites.php?action=${action}&id=${id}&ajax=1`)
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                if (isFav) {
                    el.setAttribute('data-fav', '0');
                    el.classList.remove('active');
                    el.innerHTML = '🤍 Add to Favorites';
                } else {
                    el.setAttribute('data-fav', '1');
                    el.classList.add('active');
                    el.innerHTML = '❤️ Saved';
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
});
</script>
</body>
</html>