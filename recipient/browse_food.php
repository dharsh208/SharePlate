
<?php
session_start();

require_once "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION["user_role"] !== "recipient") {
    header("Location: ../dashboard.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT d.*, u.name AS donor_name
     FROM donations d
     JOIN users u ON d.donor_id = u.id
     WHERE d.status = 'available'
       AND d.expiry_date >= CURDATE()
     ORDER BY d.created_at DESC"
);

$stmt->execute();
$donations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Food | SharePlate</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f7f8f1;
            color: #26382c;
            padding: 30px 7%;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }

        h1 {
            color: #26734d;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #66736a;
        }

        .btn {
            display: inline-block;
            background: #26734d;
            color: white;
            padding: 11px 17px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .btn:hover {
            background: #1c593a;
        }

        .food-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 23px;
            border-radius: 14px;
            border: 1px solid #e5e9df;
            box-shadow: 0 5px 16px rgba(30, 60, 40, 0.06);
        }

        .card h2 {
            color: #26734d;
            margin-bottom: 14px;
            font-size: 21px;
        }

        .card p {
            margin: 10px 0;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .badge {
            display: inline-block;
            background: #e5f4e9;
            color: #26734d;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .empty {
            background: white;
            text-align: center;
            padding: 40px 20px;
            border-radius: 14px;
        }

        .empty p {
            margin-top: 12px;
            color: #66736a;
        }
    </style>
</head>

<body>

<header>
    <div>
        <h1>Available Food</h1>
        <p class="subtitle">
            Discover food donations available in your community.
        </p>
    </div>

    <a class="btn" href="../dashboard.php">Back to Dashboard</a>
</header>

<?php if (count($donations) > 0): ?>

    <div class="food-list">

        <?php foreach ($donations as $donation): ?>
            <div class="card">

                <h2>
                    <?= htmlspecialchars($donation["food_name"]) ?>
                </h2>

                <p>
                    <strong>Description:</strong>
                    <?= htmlspecialchars($donation["description"] ?: "No description provided") ?>
                </p>

                <p>
                    <strong>Quantity:</strong>
                    <?= (int)$donation["quantity"] ?>
                    <?= htmlspecialchars($donation["quantity_unit"]) ?>
                </p>

                <p>
                    <strong>Expiry date:</strong>
                    <?= htmlspecialchars($donation["expiry_date"]) ?>
                </p>

                <p>
                    <strong>Pickup address:</strong>
                    <?= nl2br(htmlspecialchars($donation["pickup_address"])) ?>
                </p>

                <p>
                    <strong>Donated by:</strong>
                    <?= htmlspecialchars($donation["donor_name"]) ?>
                </p>

                <p>
                    <span class="badge">Available</span>
                </p>

            </div>
        <?php endforeach; ?>

    </div>

<?php else: ?>

    <div class="empty">
        <h2>No food donations available right now</h2>
        <p>Please check again later for new donations.</p>
    </div>

<?php endif; ?>

</body>
</html>
