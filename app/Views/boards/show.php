<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-3">
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <a href="<?= base_url('boards') ?>" class="text-decoration-none text-muted me-2">
                <i class="bi bi-grid-3x3"></i>
            </a>
            <?= esc($board['name']) ?>
        </h2>
        <div class="dropdown">
            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="<?= base_url("boards/{$board['id']}/edit") ?>">
                    <i class="bi bi-pencil"></i> Edit Board
                </a></li>
                <li><a class="dropdown-item" href="#" data-action="set-default">
                    <i class="bi bi-star"></i> Set as Default
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" data-action="delete-board">
                    <i class="bi bi-trash"></i> Delete Board
                </a></li>
            </ul>
        </div>
    </div>

    <?php if ($board['description']): ?>
    <p class="text-muted mb-4"><?= esc($board['description']) ?></p>
    <?php endif; ?>

    <div class="kanban-board" data-board-id="<?= $board['id'] ?>">
        <div class="kanban-columns d-flex gap-3 overflow-x-auto pb-3">
            <?php foreach ($board['columns'] as $column): ?>
            <div class="kanban-column bg-dark-subtle rounded border border-secondary"
                 data-column-id="<?= $column['id'] ?>">
                <div class="column-header p-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="column-color rounded-circle d-inline-block"
                              style="width: 12px; height: 12px; background-color: <?= $column['color'] ?>"></span>
                        <h6 class="mb-0 column-name" data-column-id="<?= $column['id'] ?>">
                            <?= esc($column['name']) ?>
                            <span class="text-muted ms-1 small">(<?= count($column['cards']) ?>)</span>
                        </h6>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="#" data-action="edit-column">
                                <i class="bi bi-pencil"></i> Edit
                            </a></li>
                            <li><a class="dropdown-item text-danger" href="#" data-action="delete-column">
                                <i class="bi bi-trash"></i> Delete
                            </a></li>
                        </ul>
                    </div>
                </div>
                <div class="column-cards p-2 min-vh-100" data-column-id="<?= $column['id'] ?>">
                    <?php foreach ($column['cards'] as $card): ?>
                    <?= view('cards/_card', ['card' => $card, 'column' => $column]) ?>
                    <?php endforeach; ?>
                </div>
                <div class="p-2 pt-0">
                    <button class="btn btn-sm btn-outline-light w-100 add-card-btn" data-column-id="<?= $column['id'] ?>">
                        <i class="bi bi-plus"></i> Add Card
                    </button>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="kanban-column-new">
                <button class="btn btn-outline-light d-flex align-items-center gap-2 p-3 w-100 h-100" id="addColumnBtn">
                    <i class="bi bi-plus-lg"></i> Add Column
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cardModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-light border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="cardModalTitle">Card</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cardModalBody"></div>
            <div class="modal-footer border-secondary" id="cardModalFooter"></div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const boardId = $('.kanban-board').data('board-id');

    new Sortable(document.querySelector('.kanban-columns'), {
        animation: 150,
        handle: '.column-header',
        ghostClass: 'sortable-ghost',
        onEnd: function(evt) {
            const columnIds = Array.from(document.querySelectorAll('.kanban-column'))
                .map(el => $(el).data('column-id'));
            $.post('<?= base_url("boards/{$board['id']}/reorder-columns") ?>', {
                column_ids: JSON.stringify(columnIds)
            });
        }
    });

    $('.column-cards').each(function() {
        const columnId = $(this).data('column-id');
        new Sortable(this, {
            group: 'cards',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                const targetColumn = $(evt.to).closest('.kanban-column');
                const columnId = targetColumn.data('column-id');
                const cardIds = Array.from(evt.to.querySelectorAll('.kanban-card'))
                    .map(el => $(el).data('card-id'));
                $.post('<?= base_url("cards/move") ?>', {
                    card_id: $(evt.item).data('card-id'),
                    column_id: columnId,
                    card_ids: JSON.stringify(cardIds)
                });
            }
        });
    });

    $('.kanban-board').on('click', '.add-card-btn', function() {
        const columnId = $(this).data('column-id');
        showCardForm(columnId);
    });

    $('.kanban-board').on('click', '[data-action="edit-column"]', function(e) {
        e.preventDefault();
        const columnId = $(this).closest('.kanban-column').data('column-id');
        const columnName = $(this).closest('.kanban-column').find('.column-name').text().trim().split('(')[0].trim();
        const columnColor = $(this).closest('.kanban-column').find('.column-color').css('background-color');
        editColumn(columnId, columnName, rgbToHex(columnColor));
    });

    $('.kanban-board').on('click', '[data-action="delete-column"]', function(e) {
        e.preventDefault();
        const columnId = $(this).closest('.kanban-column').data('column-id');
        if (confirm('Are you sure you want to delete this column and all its cards?')) {
            $.ajax({
                url: `<?= base_url('columns') ?>/${columnId}`,
                method: 'DELETE',
                success: () => location.reload()
            });
        }
    });

    $('#addColumnBtn').on('click', function() {
        const name = prompt('Column name:');
        if (name) {
            $.post('<?= base_url('columns') ?>', {
                board_id: boardId,
                name: name,
                color: '#0d6efd'
            }, () => location.reload());
        }
    });

    $('[data-action="set-default"]').on('click', function(e) {
        e.preventDefault();
        $.post(`<?= base_url("boards/{$board['id']}/set-default") ?>`, () => {
            alert('Board set as default');
        });
    });

    $('[data-action="delete-board"]').on('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this board?')) {
            $.ajax({
                url: `<?= base_url("boards/{$board['id']}") ?>`,
                method: 'DELETE',
                success: () => window.location.href = '<?= base_url('boards') ?>'
            });
        }
    });

    function showCardForm(columnId, cardData = null) {
        const isEdit = !!cardData;
        $('#cardModalTitle').text(isEdit ? 'Edit Card' : 'New Card');
        const bodyHtml = `
            <form id="cardForm">
                <input type="hidden" name="column_id" value="${columnId}">
                ${cardData ? `<input type="hidden" name="card_id" value="${cardData.id}">` : ''}
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control bg-dark-subtle text-light border-secondary"
                           name="title" value="${cardData ? cardData.title : ''}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control bg-dark-subtle text-light border-secondary"
                              name="description" rows="4">${cardData ? cardData.description || '' : ''}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Priority</label>
                        <select class="form-select bg-dark-subtle text-light border-secondary" name="priority">
                            <option value="low" ${cardData?.priority === 'low' ? 'selected' : ''}>Low</option>
                            <option value="medium" ${!cardData || cardData?.priority === 'medium' ? 'selected' : ''}>Medium</option>
                            <option value="high" ${cardData?.priority === 'high' ? 'selected' : ''}>High</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="datetime-local" class="form-control bg-dark-subtle text-light border-secondary"
                               name="due_date" value="${cardData?.due_date || ''}">
                    </div>
                </div>
            </form>
        `;
        $('#cardModalBody').html(bodyHtml);
        $('#cardModalFooter').html(`
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveCardBtn">${isEdit ? 'Update' : 'Create'}</button>
        `);
        const modal = new bootstrap.Modal(document.getElementById('cardModal'));
        modal.show();

        $('#saveCardBtn').on('click', function() {
            const form = $('#cardForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const data = {
                column_id: columnId,
                title: $(form).find('[name="title"]').val(),
                description: $(form).find('[name="description"]').val(),
                priority: $(form).find('[name="priority"]').val(),
                due_date: $(form).find('[name="due_date"]').val() || null
            };
            if (cardData) {
                $.ajax({
                    url: `<?= base_url('cards') ?>/${cardData.id}`,
                    method: 'PUT',
                    data: data,
                    success: () => location.reload()
                });
            } else {
                $.post('<?= base_url('cards') ?>', data, () => location.reload());
            }
        });
    }

    function editColumn(columnId, name, color) {
        const newName = prompt('Column name:', name);
        if (newName !== null) {
            $.ajax({
                url: `<?= base_url('columns') ?>/${columnId}`,
                method: 'PUT',
                data: { name: newName, color: color },
                success: () => location.reload()
            });
        }
    }

    function rgbToHex(rgb) {
        if (rgb.startsWith('#')) return rgb;
        const result = rgb.match(/\d+/g);
        if (!result) return '#0d6efd';
        return '#' + result.slice(0, 3).map(x => parseInt(x).toString(16).padStart(2, '0')).join('');
    }
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>