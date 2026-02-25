<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($title ?? 'Kanban Task Manager') ?></title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/kanban.css') ?>">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-body-secondary">

    <!-- Main Navigation (Desktop) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom" id="mainNavbar">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <i class="bi bi-kanban me-2"></i>
                Kanban Task Manager
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                <i class="bi bi-list"></i>
            </button>

            <?php if (session()->get('user_id')): ?>
                <div class="d-none d-lg-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= esc(session()->get('display_name')) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/settings"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (Desktop) -->
            <?php if (session()->get('user_id')): ?>
                <nav class="col-lg-2 d-none d-lg-block bg-dark sidebar" id="sidebar">
                    <div class="position-sticky pt-3">
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Boards</span>
                            <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="modal" data-bs-target="#newBoardModal">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </h6>
                        <ul class="nav flex-column mb-2">
                            <?php
                            $currentBoardId = isset($board) ? $board['id'] : null;
                            $userBoards = model('App\Models\BoardModel')->getForUser(session()->get('user_id'));
                            foreach ($userBoards as $b):
                            ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $currentBoardId === $b['id'] ? 'active' : '' ?>"
                                   href="/boards/<?= $b['id'] ?>">
                                    <i class="bi bi-kanban me-2"></i>
                                    <?= esc($b['name']) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </nav>
            <?php endif; ?>

            <!-- Main Content -->
            <main class="col-12 col-lg-10 px-md-4 py-4 main-content">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= esc(session()->getFlashdata('success')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Offcanvas -->
    <?php if (session()->get('user_id')): ?>
        <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="mobileSidebar">
            <div class="offcanvas-header border-bottom border-secondary">
                <h5 class="offcanvas-title">Boards</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="nav flex-column">
                    <?php foreach ($userBoards as $b): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white <?= $currentBoardId === $b['id'] ? 'active bg-primary' : '' ?>"
                           href="/boards/<?= $b['id'] ?>" data-bs-dismiss="offcanvas">
                            <?= esc($b['name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <li class="nav-item mt-3">
                        <a class="nav-link text-white" href="/settings"><i class="bi bi-gear me-2"></i>Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <!-- New Board Modal -->
    <?php if (session()->get('user_id')): ?>
        <div class="modal fade" id="newBoardModal" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" action="/boards" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Board</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="boardName" class="form-label">Board Name</label>
                            <input type="text" class="form-control" id="boardName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Board</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/sortable.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>