
<?php
$pageTitle = "SharePlate | Share Food, Share Hope";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --green: #286747;
            --dark-green: #173f2b;
            --cream: #faf8ef;
            --light-green: #e9f0e6;
            --text: #25372c;
            --muted: #6d796f;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text);
            line-height: 1.6;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: min(1120px, 90%);
            margin: auto;
        }

        header {
            background: rgba(250, 248, 239, 0.96);
            border-bottom: 1px solid #e9e8dc;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        nav {
            min-height: 78px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .logo {
            font-family: 'Manrope', sans-serif;
            font-size: 25px;
            font-weight: 800;
            color: var(--dark-green);
            letter-spacing: -1px;
        }

        .logo span {
            color: #76a66e;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            font-size: 14px;
            font-weight: 600;
        }

        .nav-links a:hover {
            color: var(--green);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 12px 20px;
            border-radius: 9px;
            font-weight: 700;
            font-size: 14px;
            transition: 0.2s;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--green);
            color: white;
        }

        .btn-primary:hover {
            background: var(--dark-green);
            transform: translateY(-1px);
        }

        .btn-outline {
            border-color: #d7dfd3;
            color: var(--dark-green);
        }

        .btn-outline:hover {
            background: var(--light-green);
        }

        .hero {
            padding: 78px 0 76px;
            overflow: hidden;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            align-items: center;
            gap: 60px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            border-radius: 30px;
            background: var(--light-green);
            color: var(--green);
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 22px;
        }

        .eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #68a76b;
        }

        h1, h2, h3 {
            font-family: 'Manrope', sans-serif;
            line-height: 1.15;
            letter-spacing: -1.5px;
        }

        h1 {
            font-size: clamp(40px, 5vw, 62px);
            color: var(--dark-green);
            margin-bottom: 22px;
        }

        h1 span {
            color: #6b9d62;
        }

        .hero-description {
            max-width: 490px;
            color: var(--muted);
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.9;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 13px;
        }

        .hero-note {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
            margin-top: 24px;
        }

        .hero-note strong {
            color: var(--green);
        }

        .hero-visual {
            min-height: 420px;
            background: #e7eddd;
            border-radius: 28px;
            padding: 28px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .food-illustration {
            width: 100%;
            max-width: 400px;
            aspect-ratio: 1 / 0.85;
            background: #f7f3e7;
            border-radius: 22px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .food-illustration::before,
        .food-illustration::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: #d6e4ce;
            opacity: 0.75;
        }

        .food-illustration::before {
            width: 190px;
            height: 190px;
            top: -80px;
            right: -45px;
        }

        .food-illustration::after {
            width: 150px;
            height: 150px;
            bottom: -80px;
            left: -40px;
        }

        .food-emoji {
            font-size: clamp(80px, 10vw, 125px);
            line-height: 1.3;
            position: relative;
            z-index: 1;
        }

        .food-illustration h3 {
            color: var(--dark-green);
            font-size: 22px;
            letter-spacing: -0.5px;
            margin-top: 12px;
            position: relative;
            z-index: 1;
        }

        .food-illustration p {
            color: var(--muted);
            font-size: 13px;
            margin-top: 8px;
            position: relative;
            z-index: 1;
        }

        .floating-card {
            position: absolute;
            background: white;
            padding: 14px 17px;
            border-radius: 13px;
            box-shadow: 0 12px 35px rgba(29, 64, 41, 0.10);
            font-size: 12px;
        }

        .floating-card strong {
            display: block;
            color: var(--dark-green);
            font-size: 15px;
        }

        .floating-card span {
            color: var(--muted);
        }

        .card-top {
            top: 22px;
            left: -18px;
        }

        .card-bottom {
            right: -15px;
            bottom: 28px;
        }

        .impact {
            background: var(--dark-green);
            color: white;
            padding: 35px 0;
        }

        .impact-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            text-align: center;
        }

        .impact-item strong {
            display: block;
            font-family: 'Manrope', sans-serif;
            font-size: 30px;
            margin-bottom: 5px;
        }

        .impact-item span {
            color: #c8d7c8;
            font-size: 13px;
        }

        section.content-section {
            padding: 85px 0;
        }

        .section-heading {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 45px;
        }

        .section-heading .eyebrow {
            margin-bottom: 15px;
        }

        .section-heading h2 {
            font-size: clamp(30px, 4vw, 42px);
            color: var(--dark-green);
            margin-bottom: 14px;
        }

        .section-heading p {
            color: var(--muted);
            font-size: 15px;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .step-card {
            background: white;
            border: 1px solid #edede3;
            padding: 30px;
            border-radius: 18px;
            transition: 0.2s;
        }

        .step-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(31, 65, 42, 0.06);
        }

        .step-icon {
            width: 53px;
            height: 53px;
            border-radius: 14px;
            background: var(--light-green);
            display: grid;
            place-items: center;
            font-size: 25px;
            margin-bottom: 22px;
        }

        .step-card h3 {
            font-size: 20px;
            color: var(--dark-green);
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .step-card p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.8;
        }

        .cta {
            background: #e9efe2;
            border-radius: 24px;
            padding: 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
        }

        .cta h2 {
            color: var(--dark-green);
            font-size: clamp(27px, 4vw, 38px);
            margin-bottom: 12px;
        }

        .cta p {
            color: var(--muted);
            font-size: 14px;
            max-width: 500px;
        }

        footer {
            padding: 30px 0;
            border-top: 1px solid #e7e7dc;
        }

        .footer-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            color: var(--muted);
            font-size: 12px;
        }

        .footer-inner strong {
            color: var(--dark-green);
            font-size: 17px;
        }

        @media (max-width: 850px) {
            .nav-links {
                display: none;
            }

            .hero-grid {
                grid-template-columns: 1fr;
                gap: 42px;
            }

            .hero {
                padding-top: 55px;
            }

            .hero-visual {
                min-height: 350px;
                margin: 0 12px;
            }

            .steps-grid {
                grid-template-columns: 1fr;
            }

            .cta {
                flex-direction: column;
                align-items: flex-start;
                padding: 32px;
            }
        }

        @media (max-width: 520px) {
            .nav-actions .btn-outline {
                display: none;
            }

            .logo {
                font-size: 22px;
            }

            .hero-visual {
                padding: 18px;
                min-height: 310px;
            }

            .card-top {
                left: -10px;
            }

            .card-bottom {
                right: -10px;
            }

            .impact-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .impact-item strong {
                font-size: 26px;
            }

            .footer-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            section.content-section {
                padding: 60px 0;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="container">
            <a href="index.php" class="logo">Share<span>Plate.</span></a>

            <div class="nav-links">
                <a href="#how-it-works">How it works</a>
                <a href="#our-mission">Our mission</a>
                <a href="#get-involved">Get involved</a>
            </div>

            <div class="nav-actions">
                <a href="login.php" class="btn btn-outline">Log in</a>
                <a href="register.php" class="btn btn-primary">Join SharePlate</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero" id="our-mission">
            <div class="container hero-grid">
                <div class="hero-copy">
                    <div class="eyebrow">
                        <span class="eyebrow-dot"></span>
                        GOOD FOOD. GREATER GOOD.
                    </div>

                    <h1>Good food deserves a <span>second chance.</span></h1>

                    <p class="hero-description">
                        SharePlate connects people with surplus food to those who need it.
                        Together, we can reduce food waste, support our communities, and
                        make every meal count.
                    </p>

                    <div class="hero-actions">
                        <a href="register.php" class="btn btn-primary">Start sharing food &rarr;</a>
                        <a href="#how-it-works" class="btn btn-outline">Discover how it works</a>
                    </div>

                    <div class="hero-note">
                        <span>🌱</span>
                        <span><strong>Every plate matters.</strong> Every small act creates change.</span>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="floating-card card-top">
                        <strong>🥗 Share surplus</strong>
                        <span>Food, not waste</span>
                    </div>

                    <div class="food-illustration">
                        <div class="food-emoji" aria-label="Fresh food" role="img">🥦🥕🍅</div>
                        <h3>Fresh food. Fresh hope.</h3>
                        <p>Bringing communities together, one meal at a time.</p>
                    </div>

                    <div class="floating-card card-bottom">
                        <strong>💚 Make an impact</strong>
                        <span>Be part of the change</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="impact" aria-label="Our goals">
            <div class="container impact-grid">
                <div class="impact-item">
                    <strong>Less Waste</strong>
                    <span>Give surplus food a purpose</span>
                </div>
                <div class="impact-item">
                    <strong>More Meals</strong>
                    <span>Connect food with communities</span>
                </div>
                <div class="impact-item">
                    <strong>One Mission</strong>
                    <span>Build a more caring world</span>
                </div>
            </div>
        </section>

        <section class="content-section" id="how-it-works">
            <div class="container">
                <div class="section-heading">
                    <div class="eyebrow">SIMPLE STEPS, REAL CHANGE</div>
                    <h2>Good things happen when we share.</h2>
                    <p>
                        Whether you have extra food or need a helping hand,
                        SharePlate makes the process simple.
                    </p>
                </div>

                <div class="steps-grid">
                    <article class="step-card">
                        <div class="step-icon">🍲</div>
                        <h3>1. Share food</h3>
                        <p>
                            Donors can list safe, edible surplus food with its quantity,
                            collection location, and expiry or best-before information.
                        </p>
                    </article>

                    <article class="step-card">
                        <div class="step-icon">🤝</div>
                        <h3>2. Connect locally</h3>
                        <p>
                            Recipients can browse available donations and request food
                            that matches their needs.
                        </p>
                    </article>

                    <article class="step-card">
                        <div class="step-icon">💚</div>
                        <h3>3. Make an impact</h3>
                        <p>
                            Track requests and collection status while helping good food
                            reach people instead of going to waste.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section class="content-section" id="get-involved">
            <div class="container">
                <div class="cta">
                    <div>
                        <h2>Have food to share?</h2>
                        <p>
                            Join a community that believes good food should help people,
                            not end up in the bin. Your next small step could make
                            someone's day better.
                        </p>
                    </div>
                    <a href="register.php" class="btn btn-primary">Become a member &rarr;</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container footer-inner">
            <strong>Share<span style="color:#76a66e">Plate.</span></strong>
            <span>Sharing food. Reducing waste. Strengthening communities.</span>
            <span>&copy; <?php echo date("Y"); ?> SharePlate</span>
        </div>
    </footer>
</body>
</html>
