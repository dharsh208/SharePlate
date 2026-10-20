
<?php
session_start();

require_once "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION["user_role"] !== "donor") {
    header("Location: ../dashboard.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT * FROM donations WHERE donor_id = ? ORDER BY created_at DESC"
);
$stmt->execute([$_SESSION["user_id"]]);
$donations = $stmt->fetchAll();

$success = $_SESSION["donation_success"] ?? "";
unset($_SESSION["donation_success"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Donations | SharePlate</title>

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
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        h1 {
            color: #26734d;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #66736a;
        }

        a {
            text-decoration: none;
        }

        .btn {
            display: inline-block;
            background: #26734d;
            color: white;
            padding: 11px 17px;
            border-radius: 8px;
            font-weight: bold;
        }

        .btn:hover {
            background: #1c593a;
        }

        .success {
            background: #e2f5e8;
            color: #20683c;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .donation-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-top: 25px;
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

        .status {
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
            margin-top: 25px;
            border-radius: 14px;
        }

        .empty p {
            margin: 12px 0 20px;
            color: #66736a;
        }
    </style>
</head>

<body>

<header>
    <div>
        <h1>My Donations</h1>
        <p class="subtitle">
            Manage the food you have shared with your community.
        </p>
    </div>

    <a class="btn" href="add_donation.php">+ Add Donation</a>
</header>

<?php if ($success): ?>
    <div class="success">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<?php if (count($donations) > 0): ?>

    <div class="donation-list">

        <?php foreach ($donations as $donation): ?>
            <div class="card">

                <h2>
                    <?= htmlspecialchars($donation["food_name"]) ?>
                </h2>

                <p>
                    <strong>Description:</strong>
                    <?= htmlspecialchars($donation["description"] ?: "No description") ?>
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
                    <strong>Status:</strong>
                    <span class="status">
                        <?= htmlspecialchars(ucfirst($donation["status"])) ?>
                    </span>
                </p>

                <p>
                    <strong>Added on:</strong>
                    <?= htmlspecialchars($donation["created_at"]) ?>
                </p>

            </div>
        <?php endforeach; ?>

    </div>

<?php else: ?>

    <div class="empty">
        <h2>No donations yet</h2>
        <p>Your food donations will appear here after you add them.</p>
        <a class="btn" href="add_donation.php">Add Your First Donation</a>
    </div>

<?php endif; ?>

</body>
</html>
