<?php

session_start();
require_once('db.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - UrbanHarvest</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Raleway&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Raleway", sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            color: #333;
        }

        h1, h2, h3 {
            font-family: "Poppins", sans-serif;
            font-weight: bolder;
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        section {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        h1 {
            font-size: 2.5rem;
            color: #FFFFFF;
            margin-bottom: 20px;
            text-align: center;
        }

        h2 {
            font-size: 2rem;
            color: #4e7056;
            margin: 25px 0 15px;
            text-align: center;
        }

        p {
            margin-bottom: 15px;
            color: #555;
            font-size: 1.1rem;
        }

        .btn {
            display: inline-block;
            background-color: #618264;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            text-align: center;
            margin: 20px auto;
        }

        .btn:hover {
            background-color: #4e7056;
        }

        .reviews-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .review-card {
            background-color: #f8f8f8;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .review-card:hover {
            transform: translateY(-5px);
        }

        .review-card p {
            font-style: italic;
            text-align: center;
        }

        .review-card strong {
            color: #618264;
            display: block;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<main>
    <section>
        <h1>Why Choose UrbanHarvest?</h1>
        <p>At UrbanHarvest, we are dedicated to bringing you the freshest and highest quality grocery items directly to your doorstep. Our mission is to provide convenience, affordability, and premium service in the online grocery shopping industry.</p>
        <button class="btn" id="learnMoreBtn">Learn More</button>
    </section>

    <section>
        <h2>Client's Reviews</h2>
        <div class="reviews-container">
            <?php 
            try {
                require_once('db.php');
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Fetch latest messages as reviews (limit to 4)
                $stmt = $conn->prepare("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 4");
                $stmt->execute();
                $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($reviews) > 0) {
                    foreach ($reviews as $review) {
                        echo '<div class="review-card">';
                        echo '<p>"' . htmlspecialchars($review['message']) . '"</p>';
                        echo '<strong>' . htmlspecialchars($review['name']) . '</strong>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No messages found.</p>';
                }
            } catch (PDOException $e) {
                echo "<p>Error: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </section>
</main>
<?php include 'footer.php'; ?>
<script>
    // Learn More button functionality
    document.addEventListener('DOMContentLoaded', function() {
        const learnMoreBtn = document.getElementById('learnMoreBtn');
        if (learnMoreBtn) {
            learnMoreBtn.addEventListener('click', function() {
                // Send AJAX request to track button click
                fetch('track_click.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'button_id=learnMoreBtn&page=about'
                })
                .then(response => {
                    if (!response.ok) {
                        console.error('Tracking failed');
                    }
                    return response.text();
                })
                .catch(error => {
                    console.error('Error:', error);
                });

                alert('Thank you for your interest in UrbanHarvest! Our team will contact you with more information soon.');
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId !== '#') {
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    });
</script>
</body>
</html>
