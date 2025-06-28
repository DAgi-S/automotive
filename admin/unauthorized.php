<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access - Automotive System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            max-width: 500px;
            text-align: center;
            padding: 2rem;
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <i class="fas fa-exclamation-circle error-icon"></i>
            <h1 class="h3 mb-3">Unauthorized Access</h1>
            <p class="text-muted mb-4">
                You don't have permission to access this page. Please contact your administrator if you believe this is an error.
            </p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="login.php" class="btn btn-primary me-sm-2">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
                <a href="../index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-2"></i>Go to Homepage
                </a>
            </div>
        </div>
    </div>
</body>
</html> 