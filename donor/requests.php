
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

$sql = "SELECT
            c.id AS claim_id,
            c.quantity_requested,
            c.status AS claim_status,
            c.requested_at,
            d.food_name,
            d.quantity AS donation_quantity,
            d.quantity_unit,
            u.name AS recipient_name,
            u.email AS recipient_email,
            u.phone AS recipient_phone
        FROM claims c
        JOIN donations d ON c.donation_id = d.id
        JOIN users u ON c.recipient_id = u.id
        WHERE d.donor_id = ?
        ORDER BY c.requested_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION["user_id"]]);
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Received Requests | SharePlate</title>

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

        .request-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
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
            font-size: 21px;
            margin-bottom: 15px;
        }

        .card p {
            margin: 11px 0;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            background: #fff0d5;
            color: #805500;
            font-size: 13px;
            font-weight: bold;
        }

        .empty {
            background: white;
            padding: 40px 20px;
            text-align: center;
            border-radius: 14px;
        }

        .empty p {
            color: #66736a;
            margin-top: 12px;
        }
    </style>
</head>

<body>

<header>
    <div>
        <h1>Received Requests</h1>
        <p class="subtitle">
            View requests from recipients for your food donations.
        </p>
    </div>

    <a class="btn" href="../dashboard.php">Back to Dashboard</a>
</header>

<?php if (count($requests) > 0): ?>

    <div class="request-list">

        <?php foreach ($requests as $request): ?>
            <div class="card">

                <h2>
                    <?= htmlspecialchars($request["food_name"]) ?>
                </h2>

                <p>
                    <strong>Recipient:</strong>
                    <?= htmlspecialchars($request["recipient_name"]) ?>
                </p>

                <p>
                    <strong>Email:</strong>
                    <?= htmlspecialchars($request["recipient_email"]) ?>
                </p>

                <p>
                    <strong>Phone:</strong>
                    <?= htmlspecialchars($request["recipient_phone"] ?: "Not provided") ?>
                </p>

                <p>
                    <strong>Requested quantity:</strong>
                    <?= (int)$request["quantity_requested"] ?>
                    <?= htmlspecialchars($request["quantity_unit"]) ?>
                </p>

                <p>
                    <strong>Request date:</strong>
                    <?= htmlspecialchars($request["requested_at"]) ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <span class="status">
                        <?= htmlspecialchars(ucfirst($request["claim_status"])) ?>
                    </span>
                </p>

            </div>
        <?php endforeach; ?>

    </div>

<?php else: ?>

    <div class="empty">
        <h2>No requests received yet</h2>
        <p>Recipient requests for your donations will appear here.</p>
    </div>

<?php endif; ?>

</body>
</html>
