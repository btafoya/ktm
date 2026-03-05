<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Kanban Task Manager</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/icons/bootstrap-icons.css') ?>">
    <style>
        body {
            background-color: #212529;
            color: #f8f9fa;
            min-height: 100vh;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #495057;
        }
        .error-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="text-center p-5">
            <div class="error-code">404</div>
            <h1 class="error-title">Page Not Found</h1>
            <p class="text-muted mb-4">
                <?php if (ENVIRONMENT !== 'production') : ?>
                    <?= nl2br(esc($message)) ?>
                <?php else : ?>
                    The page you're looking for doesn't exist or has been moved.
                <?php endif; ?>
            </p>
            <a href="<?= base_url('boards') ?>" class="btn btn-primary">
                <i class="bi bi-house"></i> Go to Boards
            </a>
        </div>
    </div>
</body>
</html>