
<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

if (($_SESSION["user_role"] ?? "") !== "donor") {
    http_response_code(403);
    exit("Access denied. Only donors can add food donations.");
}

$errors = [];
$foodName = "";
$description = "";
$quantity = "";
$quantityUnit = "servings";
$expiryDate = "";
$pickupAddress = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $foodName = trim($_POST["food_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $quantity = trim($_POST["quantity"] ?? "");
    $quantityUnit = $_POST["quantity_unit"] ?? "servings";
    $expiryDate = $_POST["expiry_date"] ?? "";
    $pickupAddress = trim($_POST["pickup_address"] ?? "");

    $allowedUnits = ["servings", "kg", "packets", "boxes", "litres"];

    if ($foodName === "" || strlen($foodName) > 150) {
        $errors[] = "Enter a food name (maximum 150 characters).";
    }

    if (strlen($description) > 5000) {
        $errors[] = "Description must be under 5000 characters.";
    }

    if (filter_var($quantity, FILTER_VALIDATE_INT) === false ||
        (int)$quantity < 1 || (int)$quantity > 100000) {
        $errors[] = "Enter a quantity between 1 and 100000.";
    }

    if (!in_array($quantityUnit, $allowedUnits, true)) {
        $errors[] = "Select a valid quantity unit.";
    }

    $date = DateTimeImmutable::createFromFormat("!Y-m-d", $expiryDate);
    $dateErrors = DateTimeImmutable::getLastErrors();

    if (!$date || ($dateErrors !== false &&
        ($dateErrors["warning_count"] > 0 || $dateErrors["error_count"] > 0)) ||
        $date->format("Y-m-d") !== $expiryDate ||
        $expiryDate < date("Y-m-d")) {
        $errors[] = "Choose a valid expiry date today or later.";
    }

    if ($pickupAddress === "" || strlen($pickupAddress) > 5000) {
        $errors[] = "Enter a pickup address (maximum 5000 characters).";
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            "INSERT INTO donations
            (donor_id, food_name, description, quantity, quantity_unit,
             expiry_date, pickup_address, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'available')"
        );

        $stmt->execute([
            (int)$_SESSION["user_id"],
            $foodName,
            $description,
            (int)$quantity,
            $quantityUnit,
            $expiryDate,
            $pickupAddress
        ]);

        $_SESSION["donation_success"] = "Your food donation has been listed successfully!";
        header("Location: my_donations.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Food Donation | SharePlate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #faf8ef;
            color: #25372c;
            min-height: 100vh;
        }

        a { text-decoration: none; color: inherit; }

        header {
            background: #fff;
            border-bottom: 1px solid #e9e8dc;
        }

        nav {
            width: min(1050px, 90%);
            min-height: 72px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .logo {
            font: 800 24px 'Manrope', sans-serif;
            color: #173f2b;
        }

        .logo span { color: #76a66e; }

        .back {
            color: #286747;
            font-size: 14px;
            font-weight: 700;
        }

        main {
            width: min(760px, 92%);
            margin: 42px auto;
        }

        .heading { margin-bottom: 25px; }

        .eyebrow {
            color: #43875a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        h1 {
            color: #173f2b;
            font: 800 clamp(28px, 4vw, 36px) 'Manrope', sans-serif;
            letter-spacing: -1px;
            margin-bottom: 10px;
        }

        .heading p, .hint {
            color: #6d796f;
            font-size: 14px;
            line-height: 1.7;
        }

        form {
            background: #fff;
            border: 1px solid #edede3;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 12px 35px rgba(29, 64, 41, 0.05);
        }

        .field { margin-bottom: 20px; }

        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #dfe4da;
            border-radius: 9px;
            font: 14px 'DM Sans', sans-serif;
            color: #25372c;
            background: #fff;
            outline: none;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #43875a;
            box-shadow: 0 0 0 3px #e9f0e6;
        }

        textarea {
            min-height: 95px;
            resize: vertical;
        }

        .hint { font-size: 12px; margin-top: 6px; }

        .errors {
            background: #fff0ed;
            color: #a12b20;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .errors ul { padding-left: 18px; }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            padding: 13px 20px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border: 0;
        }

        .primary { background: #286747; color: #fff; }
        .primary:hover { background: #173f2b; }
        .secondary { background: #e9f0e6; color: #286747; }

        @media (max-width: 560px) {
            main { margin: 28px auto; }
            form { padding: 22px 18px; }
            .two-columns { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>
<header>
    <nav>
        <a href="../index.php" class="logo">Share<span>Plate.</span></a>
        <a href="../dashboard.php" class="back">&larr; Dashboard</a>
    </nav>
</header>

<main>
    <div class="heading">
        <div class="eyebrow">MAKE A DIFFERENCE</div>
        <h1>Add a food donation</h1>
        <p>Share accurate details so recipients can decide whether the food meets their needs.</p>
    </div>

    <?php if ($errors): ?>
        <div class="errors" role="alert">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="add_donation.php">
        <div class="field">
            <label for="food_name">Food name *</label>
            <input id="food_name" name="food_name" type="text"
                   maxlength="150"
                   value="<?= htmlspecialchars($foodName, ENT_QUOTES, "UTF-8") ?>"
                   placeholder="e.g. Vegetable rice" required>
        </div>

        <div class="field">
            <label for="description">Description and food details</label>
            <textarea id="description" name="description" maxlength="5000"
                      placeholder="Mention ingredients, preparation time, storage conditions and allergens where known."><?= htmlspecialchars($description, ENT_QUOTES, "UTF-8") ?></textarea>
            <p class="hint">Only list food that is safe to donate. Keep it stored and transported safely.</p>
        </div>

        <div class="two-columns">
            <div class="field">
                <label for="quantity">Quantity *</label>
                <input id="quantity" name="quantity" type="number"
                       min="1" max="100000" step="1"
                       value="<?= htmlspecialchars($quantity, ENT_QUOTES, "UTF-8") ?>"
                       required>
            </div>

            <div class="field">
                <label for="quantity_unit">Unit *</label>
                <select id="quantity_unit" name="quantity_unit" required>
                    <?php
                    $units = ["servings", "kg", "packets", "boxes", "litres"];
                    foreach ($units as $unit):
                    ?>
                        <option value="<?= $unit ?>"
                            <?= $quantityUnit === $unit ? "selected" : "" ?>>
                            <?= ucfirst($unit) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="field">
            <label for="expiry_date">Expiry date *</label>
            <input id="expiry_date" name="expiry_date" type="date"
                   min="<?= date("Y-m-d") ?>"
                   value="<?= htmlspecialchars($expiryDate, ENT_QUOTES, "UTF-8") ?>"
                   required>
        </div>

        <div class="field">
            <label for="pickup_address">Food collection address *</label>
            <textarea id="pickup_address" name="pickup_address"
                      maxlength="5000" autocomplete="street-address"
                      placeholder="Enter the location where the food can be collected."
                      required><?= htmlspecialchars($pickupAddress, ENT_QUOTES, "UTF-8") ?></textarea>
        </div>

        <div class="actions">
            <button type="submit" class="btn primary">Publish donation &rarr;</button>
            <a href="../dashboard.php" class="btn secondary">Cancel</a>
        </div>
    </form>
</main>
</body>
</html>
