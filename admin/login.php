<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand, .footer { color: #007bff !important; }
        .footer { padding: 20px 0; text-align: center; background: #343a40; color: #fff; margin-top: 40px; }
        .container { margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <form action="/admin/authenticate.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
