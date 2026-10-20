
<?php
session_start();
require_once __DIR__ . "/config/db.php";

$error = "";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === "") {
        $error = "Please enter a valid email address and password.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?
             LIMIT 1"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];

            if ($user["role"] === "admin") {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $error = "Incorrect email or password.";
        }
    }
}

$success = $_SESSION["success"] ?? "";
unset($_SESSION["success"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in | SharePlate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #faf8ef;
            color: #25372c;
            min-height: 100vh;
            padding: 30px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        a { color: #286747; text-decoration: none; }

        .logo {
            font: 800 28px 'Manrope', sans-serif;
            color: #173f2b;
            margin-bottom: 25px;
        }

        .logo span { color: #76a66e; }

        .card {
            background: white;
            width: 100%;
            max-width: 450px;
            padding: 35px;
            border: 1px solid #edede3;
            border-radius: 20px;
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
            margin-bottom: 27px;
        }

        .field { margin-bottom: 19px; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #dfe4da;
            border-radius: 9px;
            font: 14px 'DM Sans', sans-serif;
            outline: none;
        }

        input:focus {
            border-color: #43875a;
            box-shadow: 0 0 0 3px #e9f0e6;
        }

        .btn {
            width: 100%;
            border: none;
            border-radius: 9px;
            padding: 14px;
            color: white;
            background: #286747;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn:hover { background: #173f2b; }

        .error, .success {
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .error { background: #fff0ed; color: #a12b20; }
        .success { background: #e9f5e9; color: #286747; }

        .footer {
            text-align: center;
            margin-top: 22px;
            font-size: 13px;
            color: #6d796f;
        }

        @media (max-width: 480px) {
            .card { padding: 25px 20px; }
        }
    </style>
</head>
<body>
    <a href="index.php" class="logo">Share<span>Plate.</span></a>

    <main class="card">
        <h1>Welcome back!</h1>
        <p class="subtitle">Log in to continue making a difference.</p>

        <?php if ($success): ?>
            <div class="success" role="status">
                <?= htmlspecialchars($success, ENT_QUOTES, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="field">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email"
                       autocomplete="email" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn">Log in to SharePlate</button>
        </form>

        <p class="footer">
            Don't have an account?
            <a href="register.php"><strong>Create one</strong></a>
        </p>
    </main>
</body>
</html>
