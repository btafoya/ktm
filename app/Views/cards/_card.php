<?php
$priorityColors = [
    'low' => 'bg-success',
    'medium' => 'bg-warning',
    'high' => 'bg-danger',
];
$priorityClass = $priorityColors[$card['priority']] ?? 'bg-secondary';
$isOverdue = $card['due_date'] && strtotime($card['due_date']) < time() && !$card['is_completed'];
?>

<div class="kanban-card bg-dark rounded border border-secondary mb-2 p-2 cursor-pointer"
     data-card-id="<?= $card['id'] ?>">
    <?php if ($card['priority'] !== 'low'): ?>
    <span class="badge <?= $priorityClass ?> badge-priority"><?= ucfirst($card['priority']) ?></span>
    <?php endif; ?>

    <div class="card-title small mb-1"><?= esc($card['title']) ?></div>

    <?php if (!empty($card['description'])): ?>
    <div class="card-description text-muted small text-truncate"><?= strip_tags($card['description']) ?></div>
    <?php endif; ?>

    <div class="card-meta d-flex justify-content-between align-items-center mt-2">
        <?php if ($card['due_date']): ?>
        <div class="small <?= $isOverdue ? 'text-danger' : 'text-muted' ?>">
            <i class="bi bi-calendar3"></i>
            <?= date('M j', strtotime($card['due_date'])) ?>
            <?php if ($isOverdue): ?>
            <i class="bi bi-exclamation-circle-fill"></i>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="card-actions">
            <button class="btn btn-sm btn-link text-muted p-0" data-action="toggle-complete"
                    title="<?= $card['is_completed'] ? 'Mark incomplete' : 'Mark complete' ?>">
                <i class="bi bi<?= $card['is_completed'] ? '-check-circle-fill text-success' : '-circle' ?>"></i>
            </button>
        </div>
    </div>
</div>