<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

require '../config.php'; // Include the database configuration file

// Handle additional file uploads to an existing order's zip file
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['additional_files'])) {
    $orderId = $_POST['orderId'];
    // Fetch the existing zip file name from the database
    $fetchSql = "SELECT file_name FROM orders WHERE id = ?";
    $fetchStmt = $pdo->prepare($fetchSql);
    $fetchStmt->execute([$orderId]);
    $fileRow = $fetchStmt->fetch();

    if ($fileRow && !empty($fileRow['file_name'])) {
        $filesDir = realpath(__DIR__ . '/files');
        $filePath = $filesDir . '/' . $fileRow['file_name'];
        $zip = new ZipArchive();
        if ($zip->open($filePath) === TRUE) {
            foreach ($_FILES['additional_files']['tmp_name'] as $key => $tmpName) {
                $fileName = $_FILES['additional_files']['name'][$key];
                $zip->addFromString($fileName, file_get_contents($tmpName));
            }
            $zip->close();
            echo "<div class='alert alert-success' role='alert'>Files added successfully.</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Failed to open zip file.</div>";
        }
    }
}

// Handle status change and potentially update postal_tracking_number
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status']) && isset($_POST['orderId'])) {
    $newStatus = $_POST['status'];
    $orderId = $_POST['orderId'];
    $updateSql = "UPDATE orders SET status = ? WHERE id = ?";
    $params = [$newStatus, $orderId];

    // Check if postal_tracking_number needs to be updated
    if ($newStatus == 'Dispatched' && !empty($_POST['postalTrackingNumber'])) {
        $postalTrackingNumber = $_POST['postalTrackingNumber'];
        $updateSql = "UPDATE orders SET status = ?, postal_tracking_number = ? WHERE id = ?";
        $params = [$newStatus, $postalTrackingNumber, $orderId];
    }

    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute($params);
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteOrderId'])) {
    $deleteOrderId = $_POST['deleteOrderId'];

    // First, fetch the file name associated with the order
    $fetchSql = "SELECT file_name FROM orders WHERE id = ?";
    $fetchStmt = $pdo->prepare($fetchSql);
    $fetchStmt->execute([$deleteOrderId]);
    $fileRow = $fetchStmt->fetch();

    if ($fileRow && !empty($fileRow['file_name'])) {
        $filesDir = realpath(__DIR__ . '/files'); // Adjust the path according to your directory structure
        $filePath = $filesDir . '/' . $fileRow['file_name'];

        if (file_exists($filePath)) {
            unlink($filePath); // Delete the file
        }
    }

    // Then, delete the order from the database
    $deleteSql = "DELETE FROM orders WHERE id = ?";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute([$deleteOrderId]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
        .status-col {
            width: 25%;
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
                    <a class="nav-link" href="/admin/create.php">Create Order</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/orders.php">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1>Orders</h1>
    <table class="table">
        <colgroup>
            <col>
            <col>
            <col class="status-col">
            <col>
            <col>
            <col>
        </colgroup>
        <thead class="thead-light">
            <tr>
                <th>Customer Name</th>
                <th>Tracking Number</th>
                <th>Status</th>
                <th>Change Status</th>
                <th>Upload</th>
                <th>Files</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query to select all orders
            $sql = "SELECT id, tracking_number, status, customer_name, file_name FROM orders"; // Include customer_name and file_name in the SELECT query
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            // Check if there are any orders
            if($stmt->rowCount() > 0) {
                // Fetch all orders
                while($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tracking_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>";
                    // Status change form
                    echo "<div class='status-update-form'>";
                    echo "<form action='' method='POST'>";
                    echo "<select name='status' onchange='this.form.postalTrackingNumber.style.display = this.value == \"Dispatched\" ? \"block\" : \"none\";'>";
                    echo "<option value='Pending'" . ($row['status'] == 'Pending' ? ' selected' : '') . ">Pending</option>";
                    echo "<option value='In-Progress'" . ($row['status'] == 'In-Progress' ? ' selected' : '') . ">In-Progress</option>";
                    echo "<option value='Pending Dispatch'" . ($row['status'] == 'Pending Dispatch' ? ' selected' : '') . ">Pending Dispatch</option>";
                    echo "<option value='Dispatched'" . ($row['status'] == 'Dispatched' ? ' selected' : '') . ">Dispatched</option>";
                    echo "</select>";
                    echo "<input type='text' name='postalTrackingNumber' style='display: none;' placeholder='Postal Tracking Number'>";
                    echo "<input type='hidden' name='orderId' value='" . $row['id'] . "'>";
                    echo "<input type='submit' value='Update'>";
                    echo "</form>";
                    echo "</div>";
                    echo "</td>";
                    echo "<td>";
                    // Upload files form
                    echo "<form action='' method='POST' enctype='multipart/form-data'>";
                    echo "<input type='file' name='additional_files[]' multiple>";
                    echo "<input type='hidden' name='orderId' value='" . $row['id'] . "'>";
                    echo "<input type='submit' value='Upload'>";
                    echo "</form>";
                    echo "</td>";
                    echo "<td>";
                    // Files button
                    if (!empty($row['file_name'])) {
                        echo "<a href='download.php?file=" . urlencode($row['file_name']) . "' class='btn btn-primary btn-sm'>Download</a>";
                    } else {
                        echo "No file";
                    }
                    echo "</td>";
                    echo "<td>";
                    // Delete button form
                    echo "<form action='' method='POST'>";
                    echo "<input type='hidden' name='deleteOrderId' value='" . $row['id'] . "'>";
                    echo "<input type='submit' class='btn btn-danger btn-sm' value='Delete' onclick='return confirm(\"Are you sure you want to delete this order?\");'>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No orders found.</td></tr>";
            }
                ?>
                </tbody>
            </table>
        </div>

<div class="footer">
    &copy; <?= date("Y"); ?> Print Forge. All Rights Reserved.
</div>

<!-- Optional JavaScript and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

