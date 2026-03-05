<!DOCTYPE html>
<html lang="en" data-bs-theme="dark" style="height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kanban Task Manager') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/icons/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/kanban.css') ?>">
</head>
<body class="bg-dark text-light h-100 d-flex flex-column overflow-hidden" style="height: 100vh;">
    <!-- Skip Link for Accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <?php if (session()->get('user_id')): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary" role="navigation" aria-label="Main navigation">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url() ?>" aria-label="Kanban Task Manager home">
                <i class="bi bi-kanban" aria-hidden="true"></i> KTM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas"
                    aria-label="Toggle menu" aria-controls="sidebarOffcanvas" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('settings') ?>">
                            <i class="bi bi-gear" aria-hidden="true"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('auth/logout') ?>">
                            <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="d-flex flex-grow-1 overflow-hidden">
        <aside class="sidebar d-none d-md-block bg-dark-subtle border-end border-secondary overflow-y-auto" aria-label="Board navigation">
            <div class="sidebar-content p-3">
                <a href="<?= base_url('boards/create') ?>" class="btn btn-primary w-100 mb-3" aria-label="Create new board">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> New Board
                </a>
                <nav class="nav flex-column" aria-label="Boards">
                    <?php if (isset($boards)): foreach ($boards as $board): ?>
                    <a href="<?= base_url("boards/{$board['id']}") ?>"
                       class="nav-link <?= (isset($currentBoard) && $currentBoard['id'] == $board['id']) ? 'active' : '' ?>"
                       aria-current="<?= (isset($currentBoard) && $currentBoard['id'] == $board['id']) ? 'page' : 'false' ?>">
                        <i class="bi bi-grid-3x3" aria-hidden="true"></i> <?= esc($board['name']) ?>
                    </a>
                    <?php endforeach; endif; ?>
                </nav>
            </div>
        </aside>

        <div class="offcanvas offcanvas-start bg-dark-subtle" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
            <div class="offcanvas-header border-bottom border-secondary">
                <h5 class="offcanvas-title" id="sidebarOffcanvasLabel">Boards</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-3">
                <a href="<?= base_url('boards/create') ?>" class="btn btn-primary w-100 mb-3" aria-label="Create new board">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> New Board
                </a>
                <nav class="nav flex-column" aria-label="Mobile boards">
                    <?php if (isset($boards)): foreach ($boards as $board): ?>
                    <a href="<?= base_url("boards/{$board['id']}") ?>" class="nav-link"
                       data-bs-dismiss="offcanvas"
                       aria-label="Open board: <?= esc($board['name']) ?>">
                        <i class="bi bi-grid-3x3" aria-hidden="true"></i> <?= esc($board['name']) ?>
                    </a>
                    <?php endforeach; endif; ?>
                </nav>
                <hr class="border-secondary my-3">
                <a href="<?= base_url('settings') ?>" class="nav-link" data-bs-dismiss="offcanvas" aria-label="Open settings">
                    <i class="bi bi-gear" aria-hidden="true"></i> Settings
                </a>
                <a href="<?= base_url('auth/logout') ?>" class="nav-link" data-bs-dismiss="offcanvas" aria-label="Logout">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Logout
                </a>
            </div>
        </div>

        <main id="main-content" class="main-content flex-grow-1 overflow-hidden d-flex flex-column" tabindex="-1">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
    <?php else: ?>
    <div class="auth-container">
        <?= $this->renderSection('content') ?>
    </div>
    <?php endif; ?>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;" role="status" aria-live="polite"></div>

    <!-- ARIA Live Region for Screen Readers -->
    <div id="aria-live-region" class="aria-live" aria-live="polite" aria-atomic="true"></div>

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/sortable.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/marked.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/turndown.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/tiptap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/tiptap-wrapper.js') ?>"></script>
    <script src="<?= base_url('assets/js/tiptap/image.js') ?>"></script>
    <script src="<?= base_url('assets/js/tiptap/editor.js') ?>"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>