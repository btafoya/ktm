<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Boards</h2>
        <a href="<?= base_url('boards/create') ?>" class="btn btn-primary" aria-label="Create new board">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> New Board
        </a>
    </div>

    <?php if (empty($boards)): ?>
    <div class="empty-state d-flex flex-column align-items-center justify-content-center py-5">
        <i class="bi bi-grid-3x3 text-muted mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
        <h3 class="text-muted mb-2">No boards yet</h3>
        <p class="text-muted mb-4">Create your first kanban board to get started.</p>
        <a href="<?= base_url('boards/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus" aria-hidden="true"></i> Create Board
        </a>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach ($boards as $board): ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="<?= base_url("boards/{$board['id']}") ?>" class="text-decoration-none" aria-label="Open board: <?= esc($board['name']) ?>">
                <div class="card bg-dark-subtle h-100 border-secondary <?= $board['is_default'] ? 'border-primary' : '' ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title text-light"><?= esc($board['name']) ?></h5>
                                <?php if ($board['description']): ?>
                                <p class="card-text text-muted small text-truncate"><?= esc($board['description']) ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if ($board['is_default']): ?>
                            <span class="badge bg-primary" aria-label="Default board">Default</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>