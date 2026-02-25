<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Boards</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBoardModal">
        <i class="bi bi-plus-lg me-1"></i>New Board
    </button>
</div>

<?php if (empty($boards)): ?>
    <div class="text-center py-5">
        <i class="bi bi-kanban text-muted" style="font-size: 4rem;"></i>
        <h3 class="mt-3 text-muted">No boards yet</h3>
        <p class="text-muted">Create your first board to get started</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBoardModal">
            <i class="bi bi-plus-lg me-1"></i>Create Board
        </button>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($boards as $b): ?>
            <div class="col">
                <a href="/boards/<?= $b['id'] ?>" class="card h-100 text-decoration-none text-body-hover">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-kanban me-2 text-primary"></i>
                            <?= esc($b['name']) ?>
                        </h5>
                        <p class="card-text text-muted small">
                            <i class="bi bi-clock"></i>
                            Created <?= date('M j, Y', strtotime($b['created_at'])) ?>
                        </p>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php $this->endSection() ?>