
<?php
session_start();
require_once __DIR__ . "/config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];
$userName = $_SESSION["user_name"] ?? "User";
$userRole = $_SESSION["user_role"] ?? "recipient";

if ($userRole === "admin") {
    header("Location: admin/dashboard.php");
    exit;
}

if (!in_array($userRole, ["donor", "recipient"], true)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$donationCount = 0;
$claimCount = 0;
$availableCount = 0;

if ($userRole === "donor") {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM donations WHERE donor_id = ?"
    );
    $stmt->execute([$userId]);
    $donationCount = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM claims c
         INNER JOIN donations d ON c.donation_id = d.id
         WHERE d.donor_id = ?"
    );
    $stmt->execute([$userId]);
    $claimCount = (int) $stmt->fetchColumn();
} else {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM claims WHERE recipient_id = ?"
    );
    $stmt->execute([$userId]);
    $claimCount = (int) $stmt->fetchColumn();

    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM donations
         WHERE status = 'available' AND expiry_date >= CURDATE()"
    );
    $availableCount = (int) $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SharePlate</title>
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
            background: white;
            border-bottom: 1px solid #e9e8dc;
        }

        nav {
            max-width: 1120px;
            width: 90%;
            min-height: 76px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .logo {
            font: 800 25px 'Manrope', sans-serif;
            color: #173f2b;
        }

        .logo span { color: #76a66e; }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 13px;
        }

        .role {
            padding: 6px 11px;
            border-radius: 20px;
            background: #e9f0e6;
            color: #286747;
            font-weight: 700;
            text-transform: capitalize;
        }

        .logout {
            color: #a12b20;
            font-weight: 700;
        }

        main {
            max-width: 1120px;
            width: 90%;
            margin: 45px auto 70px;
        }

        .welcome {
            background: #e9efe2;
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 30px;
        }

        .welcome .eyebrow {
            color: #286747;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        h1 {
            font: 800 clamp(28px, 4vw, 40px) 'Manrope', sans-serif;
            color: #173f2b;
            letter-spacing: -1.3px;
            margin-bottom: 12px;
        }

        .welcome p {
            color: #617164;
            font-size: 14px;
            line-height: 1.8;
        }

        .section-title {
            font: 800 23px 'Manrope', sans-serif;
            color: #173f2b;
            margin: 32px 0 18px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .stat-card, .action-card {
            background: white;
            border: 1px solid #edede3;
            border-radius: 16px;
            padding: 25px;
        }

        .stat-icon, .action-icon {
            width: 45px;
            height: 45px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #e9f0e6;
            font-size: 22px;
            margin-bottom: 17px;
        }

        .stat-number {
            font: 800 30px 'Manrope', sans-serif;
            color: #173f2b;
        }

        .stat-label {
            color: #6d796f;
            font-size: 13px;
            margin-top: 4px;
        }

        .actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .action-card {
            transition: 0.2s;
        }

        .action-card:hover {
            border-color: #a7c3a1;
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(29, 64, 41, 0.06);
        }

        .action-card h3 {
            font: 700 18px 'Manrope', sans-serif;
            color: #173f2b;
            margin-bottom: 9px;
        }

        .action-card p {
            color: #6d796f;
            font-size: 13px;
            line-height: 1.8;
            margin-bottom: 17px;
        }

        .action-link {
            color: #286747;
            font-size: 13px;
            font-weight: 700;
        }

        footer {
            padding: 25px 5%;
            border-top: 1px solid #e9e8dc;
            color: #6d796f;
            text-align: center;
            font-size: 12px;
        }

        @media (max-width: 700px) {
            nav { min-height: 70px; }
            .logo { font-size: 22px; }
            .nav-right { gap: 8px; }
            main { margin-top: 25px; }
            .welcome { padding: 25px 20px; }
            .stats { grid-template-columns: 1fr; }
            .actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<header>
    <nav>
        <a href="index.php" class="logo">Share<span>Plate.</span></a>

        <div class="nav-right">
            <span class="role">
                <?= htmlspecialchars($userRole, ENT_QUOTES, "UTF-8") ?>
            </span>
            <a href="logout.php" class="logout">Log out</a>
        </div>
    </nav>
</header>

<main>
    <section class="welcome">
        <div class="eyebrow">YOUR SHAREPLATE SPACE</div>

        <h1>
            Welcome,
            <?= htmlspecialchars($userName, ENT_QUOTES, "UTF-8") ?>! 👋
        </h1>

        <?php if ($userRole === "donor"): ?>
            <p>Every donation can make a difference. Share your surplus food and help your community.</p>
        <?php else: ?>
            <p>Find available food, submit requests, and help ensure good food reaches the people who need it.</p>
        <?php endif; ?>
    </section>

    <h2 class="section-title">Your overview</h2>

    <section class="stats">
        <?php if ($userRole === "donor"): ?>
            <article class="stat-card">
                <div class="stat-icon">🍲</div>
                <div class="stat-number"><?= $donationCount ?></div>
                <div class="stat-label">Total donations created</div>
            </article>

            <article class="stat-card">
                <div class="stat-icon">🤝</div>
                <div class="stat-number"><?= $claimCount ?></div>
                <div class="stat-label">Requests received</div>
            </article>

            <article class="stat-card">
                <div class="stat-icon">💚</div>
                <div class="stat-number"><?= $donationCount + $claimCount ?></div>
                <div class="stat-label">Donations and requests activity</div>
            </article>
        <?php else: ?>
            <article class="stat-card">
                <div class="stat-icon">🥗</div>
                <div class="stat-number"><?= $availableCount ?></div>
                <div class="stat-label">Available food listings</div>
            </article>

            <article class="stat-card">
                <div class="stat-icon">📋</div>
                <div class="stat-number"><?= $claimCount ?></div>
                <div class="stat-label">Requests you have made</div>
            </article>

            <article class="stat-card">
                <div class="stat-icon">💚</div>
                <div class="stat-number">Share</div>
                <div class="stat-label">Together, we can reduce food waste</div>
            </article>
        <?php endif; ?>
    </section>

    <h2 class="section-title">What would you like to do?</h2>

    <section class="actions">
        <?php if ($userRole === "donor"): ?>
            <a class="action-card" href="donor/add_donation.php">
                <div class="action-icon">➕</div>
                <h3>Add a food donation</h3>
                <p>List surplus food with its quantity, expiry date, and pickup address.</p>
                <span class="action-link">Create donation &rarr;</span>
            </a>

            <a class="action-card" href="donor/my_donations.php">
                <div class="action-icon">📦</div>
                <h3>My donations</h3>
                <p>View your listings and check their availability and request status.</p>
                <span class="action-link">View donations &rarr;</span>
            </a>

            <a class="action-card" href="donor/requests.php">
                <div class="action-icon">🤝</div>
                <h3>Received requests</h3>
                <p>Review recipient requests for your listed food.</p>
                <span class="action-link">Manage requests &rarr;</span>
            </a>
        <?php else: ?>
            <a class="action-card" href="recipient/browse_food.php">
                <div class="action-icon">🥗</div>
                <h3>Browse available food</h3>
                <p>Explore food donations and find listings that meet your needs.</p>
                <span class="action-link">Find food &rarr;</span>
            </a>

            <a class="action-card" href="recipient/my_requests.php">
                <div class="action-icon">📋</div>
                <h3>My food requests</h3>
                <p>Check the status of your food requests and collection updates.</p>
                <span class="action-link">View requests &rarr;</span>
            </a>
        <?php endif; ?>
    </section>
</main>

<footer>
    SharePlate &copy; <?= date("Y") ?> · Sharing food, strengthening communities.
</footer>
</body>
</html>
