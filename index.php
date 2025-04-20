<?php
require 'vendor/autoload.php';

$db = new MySQLHandler();
$items = [];
$itemDetails = null;

if($db->connect()) {  
    try {
        if (isset($_GET['id'])) {
            $itemDetails = $db->get_record_by_id($_GET['id'], 'id');
        } else {
            $items = $db->get_data();
        }
    } finally {
        $db->disconnect();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glasses Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .glasses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .glass-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: #333;
        }

        .glass-card:hover {
            transform: translateY(-5px);
        }

        .glass-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            margin-bottom: 10px;
            background-color: #f8f8f8;
            border-radius: 5px;
            padding: 10px;
        }

        .glass-info {
            margin-top: 10px;
        }

        .glass-title {
            font-size: 1.2em;
            margin-bottom: 5px;
            color: #333;
        }

        .glass-price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1em;
        }

        .glass-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 0.9em;
            color: #666;
        }

        .glass-rating {
            color: #f39c12;
            font-weight: bold;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 800px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .close:hover {
            color: #333;
        }

        .item-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .item-image {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            background-color: #f8f8f8;
            border-radius: 5px;
            padding: 10px;
        }

        .item-info {
            padding: 10px;
        }

        .item-title {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #333;
        }

        .item-price {
            font-size: 1.2em;
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .item-specs {
            margin-top: 20px;
        }

        .item-specs p {
            margin-bottom: 8px;
            color: #555;
        }

        .item-specs strong {
            color: #333;
            display: inline-block;
            width: 120px;
        }

        .stock-info {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .stock-item {
            text-align: center;
        }

        .stock-value {
            font-size: 1.2em;
            font-weight: bold;
            color: #2c3e50;
        }

        .stock-label {
            font-size: 0.9em;
            color: #666;
        }

        @media (max-width: 768px) {
            .item-details {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Glasses Shop</h1>
        
        <div class="glasses-grid">
            <?php foreach ($items as $item): ?>
                <a href="?id=<?php echo $item->id; ?>" class="glass-card">
                    <img src="images/<?php echo $item->Photo; ?>" alt="<?php echo $item->product_name; ?>" class="glass-image">
                    <h3 class="glass-title"><?php echo $item->product_name; ?></h3>
                    <p class="glass-price">$<?php echo number_format($item->list_price, 2); ?></p>
                    <div class="glass-meta">
                        <span class="glass-rating">★ <?php echo $item->Rating; ?></span>
                        <span><?php echo $item->Country; ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($itemDetails): ?>
        <div id="itemModal" class="modal" style="display: block;">
            <div class="modal-content">
                <span class="close" onclick="window.location.href='index.php'">&times;</span>
                <div class="item-details">
                    <img src="images/<?php echo $itemDetails->Photo; ?>" alt="<?php echo $itemDetails->product_name; ?>" class="item-image">
                    <div class="item-info">
                        <h2 class="item-title"><?php echo $itemDetails->product_name; ?></h2>
                        <p class="item-price">$<?php echo number_format($itemDetails->list_price, 2); ?></p>
                        
                        <div class="stock-info">
                            <div class="stock-item">
                                <div class="stock-value"><?php echo $itemDetails->Units_In_Stock; ?></div>
                                <div class="stock-label">In Stock</div>
                            </div>
                            <div class="stock-item">
                                <div class="stock-value"><?php echo $itemDetails->reorder_level; ?></div>
                                <div class="stock-label">Reorder Level</div>
                            </div>
                            <div class="stock-item">
                                <div class="stock-value">★ <?php echo $itemDetails->Rating; ?></div>
                                <div class="stock-label">Rating</div>
                            </div>
                        </div>

                        <div class="item-specs">
                            <p><strong>Category:</strong> <?php echo $itemDetails->category; ?></p>
                            <p><strong>Country:</strong> <?php echo $itemDetails->CouNtry; ?></p>
                            <p><strong>Date Added:</strong> <?php echo date('F j, Y', strtotime($itemDetails->date)); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
