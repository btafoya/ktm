<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Boards</h2>
        <a href="<?= base_url('boards/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Board
        </a>
    </div>

    <?php if (empty($boards)): ?>
    <div class="text-center py-5">
        <i class="bi bi-grid-3x3 display-1 text-muted"></i>
        <h3 class="mt-3">No boards yet</h3>
        <p class="text-muted">Create your first kanban board to get started.</p>
        <a href="<?= base_url('boards/create') ?>" class="btn btn-primary">Create Board</a>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach ($boards as $board): ?>
        <div class="col-md-4 col-lg-3">
            <a href="<?= base_url("boards/{$board['id']}") ?>" class="text-decoration-none">
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
                            <span class="badge bg-primary">Default</span>
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