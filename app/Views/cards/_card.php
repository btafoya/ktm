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
     data-card-id="<?= $card['id'] ?>" role="listitem" tabindex="0"
     aria-label="Card: <?= esc($card['title']) ?><?= $card['priority'] !== 'low' ? ', Priority: ' . ucfirst($card['priority']) : '' ?>">
    <?php if ($card['priority'] !== 'low'): ?>
    <span class="badge <?= $priorityClass ?> badge-priority" aria-label="Priority: <?= ucfirst($card['priority']) ?>">
        <?= ucfirst($card['priority']) ?>
    </span>
    <?php endif; ?>

    <div class="card-title small mb-1"><?= esc($card['title']) ?></div>

    <?php if (!empty($card['description'])): ?>
    <?php
    // Strip image references from Markdown: ![alt](url) -> empty
    $cleanDesc = preg_replace('/!\[([^\]]*)\]\([^)]+\)/', '', $card['description']);
    $cleanDesc = strip_tags($cleanDesc);
    $cleanDesc = trim($cleanDesc);
    ?>
    <?php if (!empty($cleanDesc)): ?>
    <div class="card-description text-muted small text-truncate" aria-hidden="true"><?= esc($cleanDesc) ?></div>
    <?php endif; ?>
    <?php endif; ?>

    <div class="card-meta d-flex justify-content-between align-items-center mt-2">
        <?php if ($card['due_date']): ?>
        <div class="small <?= $isOverdue ? 'text-danger' : 'text-muted' ?>">
            <i class="bi bi-calendar3" aria-hidden="true"></i>
            <span><?= date('M j', strtotime($card['due_date'])) ?></span>
            <?php if ($isOverdue): ?>
            <span class="sr-only">Overdue</span>
            <i class="bi bi-exclamation-circle-fill ms-1" aria-hidden="true"></i>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="card-actions">
            <button class="btn btn-sm btn-link text-muted p-0" data-action="toggle-complete"
                    title="<?= $card['is_completed'] ? 'Mark incomplete' : 'Mark complete' ?>"
                    aria-label="<?= $card['is_completed'] ? 'Mark card as incomplete' : 'Mark card as complete' ?>">
                <i class="bi bi<?= $card['is_completed'] ? '-check-circle-fill text-success' : '-circle' ?>" aria-hidden="true"></i>
                <span class="sr-only"><?= $card['is_completed'] ? 'Mark incomplete' : 'Mark complete' ?></span>
            </button>
        </div>
    </div>
</div>