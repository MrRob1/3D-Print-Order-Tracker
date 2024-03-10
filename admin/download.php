<?php
if (isset($_GET['file'])) {
    $fileName = basename($_GET['file']); // Ensure the file name is isolated without any path
    $filePath = __DIR__ . '/files/' . $fileName;

    if (file_exists($filePath)) {
        // Set headers to serve the file as a download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        flush(); // Flush system output buffer
        readfile($filePath);
        exit;
    } else {
        echo "File not found.";
    }
} else {
    echo "No file specified.";
}
?>