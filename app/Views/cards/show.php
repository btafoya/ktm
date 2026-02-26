<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('boards') ?>">Boards</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url("boards/{$card['board_id']}") ?>">
                <?= esc($card['column_name']) ?>
            </a></li>
            <li class="breadcrumb-item active"><?= esc($card['title']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><?= esc($card['title']) ?></h3>
                    <span class="badge <?= $card['priority'] === 'high' ? 'bg-danger' : ($card['priority'] === 'medium' ? 'bg-warning' : 'bg-success') ?>">
                        <?= ucfirst($card['priority']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if ($card['description']): ?>
                    <div class="card-text mb-3 markdown-content"><?= $card['description'] ?></div>
                    <?php endif; ?>

                    <?php if ($card['due_date']): ?>
                    <div class="mb-3">
                        <strong><i class="bi bi-calendar3"></i> Due Date:</strong>
                        <?= date('F j, Y g:i A', strtotime($card['due_date'])) ?>
                        <?php if (strtotime($card['due_date']) < time() && !$card['is_completed']): ?>
                        <span class="text-danger">(Overdue)</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($card['google_event_id']): ?>
                    <div class="mb-3">
                        <i class="bi bi-calendar-event text-primary"></i>
                        Synced with Google Calendar
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header bg-dark border-secondary">
                    <h5>Checklist</h5>
                </div>
                <div class="card-body" id="checklistContainer">
                    <?php if (empty($card['checklist_items'])): ?>
                    <p class="text-muted mb-0">No checklist items.</p>
                    <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($card['checklist_items'] as $item): ?>
                        <li class="list-group-item bg-transparent border-secondary d-flex align-items-center gap-2"
                            data-item-id="<?= $item['id'] ?>">
                            <input type="checkbox" class="form-check-input checklist-toggle"
                                   <?= $item['is_completed'] ? 'checked' : '' ?>>
                            <span class="<?= $item['is_completed'] ? 'text-decoration-through text-muted' : '' ?>">
                                <?= esc($item['title']) ?>
                            </span>
                            <button class="btn btn-sm btn-link text-muted ms-auto" data-action="delete-item">
                                <i class="bi bi-trash"></i>
                            </button>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <div class="mt-3">
                        <div class="input-group">
                            <input type="text" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="newChecklistItem" placeholder="Add checklist item...">
                            <button class="btn btn-primary" id="addChecklistItemBtn">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header bg-dark border-secondary">
                    <h5>Attachments</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($card['attachments'])): ?>
                    <p class="text-muted mb-0">No attachments.</p>
                    <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($card['attachments'] as $attachment): ?>
                        <a href="<?= base_url("attachments/{$attachment['id']}/download") ?>"
                           class="list-group-item list-group-item-action bg-dark-subtle border-secondary">
                            <i class="bi bi-file-earmark"></i> <?= esc($attachment['file_name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <form id="uploadForm" class="mt-3">
                        <input type="file" class="form-control bg-dark-subtle text-light border-secondary" id="fileInput">
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header bg-dark border-secondary">
                    <h5>Actions</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="<?= base_url("cards/{$card['id']}/edit") ?>" class="btn btn-outline-light">
                        <i class="bi bi-pencil"></i> Edit Card
                    </a>
                    <button class="btn btn-outline-light" data-action="move-card">
                        <i class="bi bi-arrow-left-right"></i> Move to Column
                    </button>
                    <button class="btn btn-<?= $card['is_completed'] ? 'outline-success' : 'success' ?>"
                            data-action="toggle-complete">
                        <i class="bi bi-<?= $card['is_completed'] ? 'arrow-counterclockwise' : 'check-lg' ?>"></i>
                        <?= $card['is_completed'] ? 'Mark Incomplete' : 'Mark Complete' ?>
                    </button>
                    <button class="btn btn-outline-danger" data-action="delete-card">
                        <i class="bi bi-trash"></i> Delete Card
                    </button>
                </div>
            </div>

            <div class="card bg-dark border-secondary">
                <div class="card-header bg-dark border-secondary">
                    <h5>Tags</h5>
                </div>
                <div class="card-body">
                    <div id="cardTags" class="mb-3">
                        <?php if (!empty($card['tags'])): foreach ($card['tags'] as $tag): ?>
                        <span class="badge me-1" style="background-color: <?= $tag['color'] ?>">
                            <?= esc($tag['name']) ?>
                        </span>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const cardId = <?= $card['id'] ?>;

    if (typeof marked !== 'undefined') {
        marked.setOptions({
            breaks: true,
            gfm: true,
            headerIds: false,
            mangle: false
        });

        $('.markdown-content').each(function() {
            const markdown = $(this).text();
            $(this).html(marked.parse(markdown));
        });
    }
});

    $('[data-action="toggle-complete"]').on('click', function() {
        $.ajax({
            url: `<?= base_url('cards') ?>/${cardId}`,
            method: 'PUT',
            data: { is_completed: !<?= $card['is_completed'] ? 1 : 0 ?> },
            success: () => location.reload(),
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Failed to update card.';
                showAlert(msg, 'danger');
            }
        });
    });

    $('#addChecklistItemBtn').on('click', function() {
        const title = $('#newChecklistItem').val().trim();
        if (title) {
            $.post('<?= base_url('checklists') ?>', {
                card_id: cardId,
                title: title
            }, () => location.reload()).fail(function(xhr) {
                const msg = xhr.responseJSON?.message || 'Failed to add checklist item.';
                showAlert(msg, 'danger');
            });
        }
    });

    $('.checklist-toggle').on('change', function() {
        const itemId = $(this).closest('li').data('item-id');
        $.post(`<?= base_url('checklists') ?>/${itemId}/toggle`, () => location.reload())
            .fail(function(xhr) {
                const msg = xhr.responseJSON?.message || 'Failed to toggle item.';
                showAlert(msg, 'danger');
            });
    });

    $('[data-action="delete-item"]').on('click', function() {
        const itemId = $(this).closest('li').data('item-id');
        if (confirm('Delete this checklist item?')) {
            $.ajax({
                url: `<?= base_url('checklists') ?>/${itemId}`,
                method: 'DELETE',
                success: () => location.reload(),
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to delete item.';
                    showAlert(msg, 'danger');
                }
            });
        }
    });

    $('[data-action="delete-card"]').on('click', function() {
        if (confirm('Are you sure you want to delete this card?')) {
            $.ajax({
                url: `<?= base_url('cards') ?>/${cardId}`,
                method: 'DELETE',
                success: () => window.location.href = `<?= base_url("boards/{$card['board_id']}") ?>`,
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to delete card.';
                    showAlert(msg, 'danger');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>