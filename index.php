<?php
require_once 'config.php'; // Include the database configuration file

$orderStatus = '';
$postalTrackingNumber = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the tracking number from the form submission
    $trackingNumber = htmlspecialchars($_POST['trackingNumber']);

    // Prepare SQL statement to prevent SQL injection
    $stmt = $pdo->prepare("SELECT status, postal_tracking_number FROM orders WHERE tracking_number = ?");
    $stmt->bindParam(1, $trackingNumber);
    $stmt->execute();

    $result = $stmt->fetchAll();

    if (count($result) > 0) {
        // output data of each row
        foreach($result as $row) {
            $orderStatus = $row["status"];
            $postalTrackingNumber = $row["postal_tracking_number"];
        }
    } else {
        $orderStatus = "No order found with that tracking number.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track Your Order - Print Forge</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand, .footer {
            color: #007bff !important;
        }
        .footer {
            padding: 20px 0;
            text-align: center;
            background: #343a40;
            color: #fff;
            margin-top: 40px;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
        }
        nav ul li {
            display: inline;
            margin-right: 10px;
        }
        .container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Print Forge</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Track Order</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<div class="container mt-5">
    <h2 class="mb-4">Track Your Order</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="mb-3">
      <div class="form-group">
        <label for="trackingNumber" class="sr-only">Tracking Number:</label>
        <input type="text" class="form-control form-control-lg" id="trackingNumber" name="trackingNumber" placeholder="Enter tracking number" required>
      </div>
      <button type="submit" name="submit" class="btn btn-lg btn-primary btn-block">Track</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo "<div class='alert alert-info' role='alert'>";
        echo "<h4 class='alert-heading'>Order Status: $orderStatus</h4>";
        if ($orderStatus === "Dispatched") {
            echo "<p>Postal Tracking Number: $postalTrackingNumber</p>";
        }
        echo "</div>";
    }
    ?>
</div>

<div class="footer">
    &copy; <?= date("Y"); ?> Print Forge. All rights reserved.
</div>

<!-- Optional JavaScript and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
