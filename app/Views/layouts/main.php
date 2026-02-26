<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kanban Task Manager') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/icons/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/kanban.css') ?>">
</head>
<body class="bg-dark text-light">
    <?php if (session()->get('user_id')): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <i class="bi bi-kanban"></i> KTM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('settings') ?>">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('auth/logout') ?>">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        <aside class="sidebar d-none d-md-block bg-dark-subtle border-end border-secondary">
            <div class="sidebar-content p-3">
                <a href="<?= base_url('boards/create') ?>" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-plus-lg"></i> New Board
                </a>
                <nav class="nav flex-column">
                    <?php if (isset($boards)): foreach ($boards as $board): ?>
                    <a href="<?= base_url("boards/{$board['id']}") ?>"
                       class="nav-link <?= (isset($currentBoard) && $currentBoard['id'] == $board['id']) ? 'active' : '' ?>">
                        <i class="bi bi-grid-3x3"></i> <?= esc($board['name']) ?>
                    </a>
                    <?php endforeach; endif; ?>
                </nav>
            </div>
        </aside>

        <div class="offcanvas offcanvas-start bg-dark-subtle" tabindex="-1" id="sidebarOffcanvas">
            <div class="offcanvas-header border-bottom border-secondary">
                <h5 class="offcanvas-title">Boards</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-3">
                <a href="<?= base_url('boards/create') ?>" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-plus-lg"></i> New Board
                </a>
                <nav class="nav flex-column">
                    <?php if (isset($boards)): foreach ($boards as $board): ?>
                    <a href="<?= base_url("boards/{$board['id']}") ?>" class="nav-link"
                       data-bs-dismiss="offcanvas">
                        <i class="bi bi-grid-3x3"></i> <?= esc($board['name']) ?>
                    </a>
                    <?php endforeach; endif; ?>
                </nav>
                <hr class="border-secondary my-3">
                <a href="<?= base_url('settings') ?>" class="nav-link" data-bs-dismiss="offcanvas">
                    <i class="bi bi-gear"></i> Settings
                </a>
                <a href="<?= base_url('auth/logout') ?>" class="nav-link" data-bs-dismiss="offcanvas">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

        <main class="main-content flex-grow-1 overflow-auto">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
    <?php else: ?>
    <div class="auth-container">
        <?= $this->renderSection('content') ?>
    </div>
    <?php endif; ?>

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/sortable.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/marked.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/turndown.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/tiptap/core.js') ?>"></script>
    <script src="<?= base_url('assets/js/tiptap/starter-kit.js') ?>"></script>
    <script src="<?= base_url('assets/js/tiptap/task-list.js') ?>"></script>
    <script src="<?= base_url('assets/js/tiptap/task-item.js') ?>"></script>
    <script src="<?= base_url('assets/js/tiptap/editor.js') ?>"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>