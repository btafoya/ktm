<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Something went wrong | Kanban Task Manager</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/icons/bootstrap-icons.css') ?>">
    <style>
        body {
            background-color: #212529;
            color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-page {
            max-width: 500px;
            text-align: center;
            padding: 2rem;
        }
        .error-icon {
            font-size: 4rem;
            color: #495057;
            margin-bottom: 1.5rem;
        }
        .error-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #adb5bd;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h1 class="error-title">Something went wrong</h1>
        <p class="error-message">
            We encountered an unexpected error. Please try again later or contact support if the problem persists.
        </p>
        <a href="<?= base_url('boards') ?>" class="btn btn-primary">
            <i class="bi bi-house"></i> Go to Boards
        </a>
    </div>
</body>
</html>