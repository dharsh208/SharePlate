
<?php
session_start();
require_once __DIR__ . "/config/db.php";

$errors = [];
$name = "";
$email = "";
$phone = "";
$address = "";
$role = "recipient";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $role = $_POST["role"] ?? "recipient";
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($name === "" || strlen($name) > 100) {
        $errors[] = "Please enter a name (maximum 100 characters).";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
        $errors[] = "Please enter a valid email address.";
    }

    if (!in_array($role, ["donor", "recipient"], true)) {
        $errors[] = "Please choose a valid account type.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Your password must contain at least 8 characters.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "The passwords do not match.";
    }

    if (!$errors) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $errors[] = "An account with this email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password, phone, address, role)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $name,
                $email,
                $hashedPassword,
                $phone,
                $address,
                $role
            ]);

            $_SESSION["success"] = "Your account has been created. Please log in.";
            header("Location: login.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join SharePlate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #faf8ef;
            color: #25372c;
            min-height: 100vh;
            padding: 30px 16px;
        }

        a { text-decoration: none; color: #286747; }

        .logo {
            display: block;
            width: fit-content;
            margin: 0 auto 25px;
            font: 800 27px 'Manrope', sans-serif;
            color: #173f2b;
        }

        .logo span { color: #76a66e; }

        .card {
            max-width: 540px;
            margin: auto;
            background: white;
            border: 1px solid #edede3;
            border-radius: 20px;
            padding: 34px;
            box-shadow: 0 15px 45px rgba(29, 64, 41, 0.06);
        }

        h1 {
            font: 800 30px 'Manrope', sans-serif;
            letter-spacing: -1px;
            color: #173f2b;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #6d796f;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .field { margin-bottom: 17px; }

        label {
            display: block;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 7px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #dfe4da;
            border-radius: 9px;
            font: 14px 'DM Sans', sans-serif;
            background: #fff;
            color: #25372c;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #43875a;
            box-shadow: 0 0 0 3px #e9f0e6;
        }

        textarea { resize: vertical; min-height: 75px; }

        .hint { color: #6d796f; font-size: 12px; margin-top: 5px; }

        .btn {
            display: block;
            width: 100%;
            padding: 13px;
            border: 0;
            border-radius: 9px;
            background: #286747;
            color: white;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
        }

        .btn:hover { background: #173f2b; }

        .errors {
            padding: 13px 16px;
            margin-bottom: 20px;
            border-radius: 9px;
            background: #fff0ed;
            color: #a12b20;
            font-size: 13px;
        }

        .errors ul { padding-left: 18px; }

        .footer {
            text-align: center;
            color: #6d796f;
            font-size: 13px;
            margin-top: 22px;
        }

        @media (max-width: 520px) {
            .card { padding: 24px 20px; }
            h1 { font-size: 26px; }
        }
    </style>
</head>
<body>
    <a href="index.php" class="logo">Share<span>Plate.</span></a>

    <main class="card">
        <h1>Create your account</h1>
        <p class="subtitle">Join us in reducing food waste and helping communities.</p>

        <?php if ($errors): ?>
            <div class="errors" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="field">
                <label for="name">Full name *</label>
                <input id="name" name="name" type="text" maxlength="100"
                       value="<?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>"
                       autocomplete="name" required>
            </div>

            <div class="field">
                <label for="email">Email address *</label>
                <input id="email" name="email" type="email" maxlength="150"
                       value="<?= htmlspecialchars($email, ENT_QUOTES, "UTF-8") ?>"
                       autocomplete="email" required>
            </div>

            <div class="field">
                <label for="phone">Phone number</label>
                <input id="phone" name="phone" type="tel" maxlength="20"
                       value="<?= htmlspecialchars($phone, ENT_QUOTES, "UTF-8") ?>"
                       autocomplete="tel">
            </div>

            <div class="field">
                <label for="address">Address</label>
                <textarea id="address" name="address" maxlength="2000"
                          autocomplete="street-address"><?= htmlspecialchars($address, ENT_QUOTES, "UTF-8") ?></textarea>
            </div>

            <div class="field">
                <label for="role">I want to *</label>
                <select id="role" name="role" required>
                    <option value="recipient" <?= $role === "recipient" ? "selected" : "" ?>>
                        Receive food
                    </option>
                    <option value="donor" <?= $role === "donor" ? "selected" : "" ?>>
                        Donate surplus food
                    </option>
                </select>
            </div>

            <div class="field">
                <label for="password">Password *</label>
                <input id="password" name="password" type="password"
                       minlength="8" autocomplete="new-password" required>
                <p class="hint">Use at least 8 characters.</p>
            </div>

            <div class="field">
                <label for="confirm_password">Confirm password *</label>
                <input id="confirm_password" name="confirm_password"
                       type="password" minlength="8"
                       autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn">Create my account</button>
        </form>

        <p class="footer">
            Already a member? <a href="login.php"><strong>Log in</strong></a>
        </p>
    </main>
</body>
</html>
