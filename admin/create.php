<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
require_once '../config.php'; // Include the database configuration file

// Function to generate a random tracking number
function generateRandomTrackingNumber($length = 10) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Function to generate a random string
function generateRandomString($length = 5) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate a random tracking number
    $trackingNumber = generateRandomTrackingNumber();
    $customerName = $_POST['customer_name']; // Get the customer name from the form
    $email = isset($_POST['email']) ? $_POST['email'] : null; // Get the email from the form

    // Handle multiple file uploads
    if (isset($_FILES['order_files']) && count($_FILES['order_files']['name']) > 0) {
        $zip = new ZipArchive();
        $fileName = $trackingNumber . generateRandomString() . '.zip';
        $destPath = __DIR__ . '/files/' . $fileName;
        if ($zip->open($destPath, ZipArchive::CREATE) === TRUE) {
            for ($i = 0; $i < count($_FILES['order_files']['name']); $i++) {
                $fileTmpPath = $_FILES['order_files']['tmp_name'][$i];
                $zip->addFromString(basename($_FILES['order_files']['name'][$i]), file_get_contents($fileTmpPath));
            }
            $zip->close();
            // File upload success
        } else {
            echo "<div class='alert alert-danger' role='alert'>File upload failed.</div>";
        }
    }

    // Prepare an insert statement
    $sql = "INSERT INTO orders (tracking_number, status, customer_name, file_name, email) VALUES (:tracking_number, 'Pending', :customer_name, :file_name, :email)";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(':tracking_number', $trackingNumber, PDO::PARAM_STR);
        $stmt->bindParam(':customer_name', $customerName, PDO::PARAM_STR);
        $stmt->bindParam(':file_name', $fileName, PDO::PARAM_STR); // Bind file name
        $stmt->bindParam(':email', $email, PDO::PARAM_STR); // Bind email

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            echo "<div class='alert alert-success' role='alert'>Order generated successfully. Tracking Number: " . $trackingNumber . "</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Oops! Something went wrong. Please try again later.</div>";
        }

        // Close statement
        unset($stmt);
    }
}

// Close connection
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate New Order</title>
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
        <a class="navbar-brand" href="#">Print Forge</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="create.php">Create Order</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="orders.php">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1>Generate New Order</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="mb-3" enctype="multipart/form-data">
        <div class="form-group">
            <label for="customerName">Customer Name:</label>
            <input type="text" class="form-control" id="customerName" name="customer_name" placeholder="Enter customer name" required>
        </div>
        <div class="form-group">
            <label for="email">Email (optional):</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address">
        </div>
        <div class="form-group">
            <label for="orderFile">Order Files:</label>
            <input type="file" class="form-control" id="orderFile" name="order_files[]" multiple required>
        </div>
        <button type="submit" class="btn btn-lg btn-primary btn-block">Generate New Order</button>
    </form>
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
