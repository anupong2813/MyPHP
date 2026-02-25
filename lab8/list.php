<?php
require __DIR__ . '/config.php';

// --- ส่วน Logic การค้นหา ---
$search = trim($_GET['search'] ?? '');
$sql = "SELECT id, productname, detail, price, img FROM products";
$params = [];

if ($search !== '') {
    if (is_numeric($search)) {
        $sql .= " WHERE price = :price";
        $params = [':price' => (float)$search];
    }
    elseif (preg_match('/^(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)$/', $search, $matches)) {
        $sql .= " WHERE price BETWEEN :min AND :max";
        $params = [':min' => (float)$matches[1], ':max' => (float)$matches[2]];
    }
    else {
        // [แก้ไขที่นี่] แยกตัวแปรเป็น :s1 และ :s2 เพื่อไม่ให้ Error
        $sql .= " WHERE productname LIKE :s1 OR detail LIKE :s2";
        $params = [
            ':s1' => "%$search%", 
            ':s2' => "%$search%"
        ];
    }
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params); // บรรทัดที่ 26 ที่เคย Error จะทำงานได้ปกติแล้วครับ
$products = $stmt->fetchAll();
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Vinyl Station</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* โทนสีเทาเข้ม */
        body { 
            background-color: #222222; 
            color: #eeeeee; 
            font-family: 'Kanit', sans-serif; 
            padding: 40px; 
            margin: 0; 
        }
        
        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; max-width: 1100px; margin: 0 auto 30px; }
        h1 { 
            font-weight: 700; 
            color: #ffffff; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            margin: 0; 
            font-size: 32px;
        }
        
        /* ปุ่ม Add New Vibe สีเขียวมิ้นท์ */
        .btn-add { 
            background: #00cca3; 
            color: #111; 
            text-decoration: none; 
            padding: 12px 24px; 
            border-radius: 4px; 
            font-weight: 700; 
            transition: 0.3s; 
            font-size: 14px;
            text-transform: uppercase;
        }
        .btn-add:hover { background: #ffffff; color: #00cca3; }
        
        /* Search Box สีเทาเข้มกลมกลืน */
        .search-container { 
            max-width: 1100px; 
            margin: 0 auto 50px; 
            background: #333333; 
            padding: 15px; 
            border-radius: 6px; 
            display: flex;
            gap: 10px;
        }
        .search-input { 
            flex-grow: 1; 
            background: transparent; 
            border: none; 
            padding: 10px; 
            color: #fff; 
            font-family: 'Kanit', sans-serif; 
            font-size: 16px; 
        }
        .search-input:focus { outline: none; }
        .search-input::placeholder { color: #888; }
        
        .btn-search { 
            background: #00cca3; 
            color: #111; 
            border: none; 
            padding: 10px 30px; 
            border-radius: 4px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: 0.3s; 
            text-transform: uppercase;
        }
        .btn-search:hover { background: #fff; }
        .clear-link { color: #888; text-decoration: none; align-self: center; font-size: 14px; margin-left: 10px; transition:0.3s; font-weight:600; }
        .clear-link:hover { color: #fff; }

        /* Grid Layout */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 40px; max-width: 1100px; margin: auto; }
        
        /* Card */
        .card { 
            background: transparent; 
            display: flex; 
            flex-direction: column; 
            height: 100%; 
        }
        .card img { 
            width: 100%; 
            aspect-ratio: 1/1; 
            object-fit: cover; 
            border-radius: 4px; 
            margin-bottom: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: 0.3s;
        }
        .card:hover img { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.5); }

        .card-content { flex-grow: 1; display: flex; flex-direction: column; }
        
        .card-name { 
            font-weight: 700; 
            font-size: 18px; 
            margin-bottom: 5px; 
            color: #ffffff; 
            line-height: 1.3;
        }
        
        .card-detail { 
            color: #999; 
            font-size: 14px; 
            margin-bottom: 15px; 
            height: 40px; 
            overflow: hidden; 
            font-weight: 300;
        }
        
        .card-price { 
            color: #00cca3; 
            font-weight: 700; 
            font-size: 20px; 
            margin-bottom: 15px; 
            margin-top: auto; 
        }
        
        .actions { 
            display: flex; 
            gap: 15px; 
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .btn-action { color: #666; text-decoration: none; transition: 0.3s; }
        .btn-action:hover { color: #fff; }
        
        .no-results { text-align: center; color: #666; margin-top: 50px; font-size: 18px; }
    </style>
</head>
<body>

<div class="header">
    <h1>FRESH DROPS 💿</h1>
    <a href="index.php" class="btn-add">+ ADD NEW VIBE</a>
</div>

<div class="search-container">
    <form method="GET" style="display:flex; width:100%;">
        <input type="text" name="search" class="search-input" 
               placeholder="ค้นหาชื่อศิลปิน, อัลบั้ม, ราคา หรือช่วงราคา (เช่น 500-1500)..." 
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn-search">SEARCH</button>
        <?php if ($search): ?>
            <a href="list.php" class="clear-link">CLEAR</a>
        <?php endif; ?>
    </form>
</div>

<?php if (!$products): ?>
    <div class="no-results">
        ไม่พบแผ่นเสียงที่คุณกำลังค้นหา...
    </div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($products as $p): ?>
        <div class="card">
            <img src="<?= htmlspecialchars($p['img'] ?: '') ?>" alt="cover">
            
            <div class="card-content">
                <div class="card-name"><?= htmlspecialchars($p['productname'] ?? 'Unknown Artist') ?></div>
                <div class="card-detail"><?= nl2br(htmlspecialchars($p['detail'] ?? '')) ?></div>
                <div class="card-price">฿<?= number_format($p['price'] ?? 0, 2) ?></div>
                
                <div class="actions">
                    <a href="edit.php?id=<?= $p['id'] ?>" class="btn-action">EDIT</a>
                    <a href="delete.php?id=<?= $p['id'] ?>" class="btn-action" onclick="return confirm('Delete this vibe?')">DELETE</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

</body>
</html>