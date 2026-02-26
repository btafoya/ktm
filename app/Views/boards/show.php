<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-3 flex-grow-1 d-flex flex-column overflow-hidden h-100">
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
        <div class="btn-group">
            <button class="btn btn-primary" id="addColumnBtn">
                <i class="bi bi-plus-lg"></i> Add Column
            </button>
            <button class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                <span class="visually-hidden">Toggle dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
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

    <div class="kanban-board flex-grow-1 d-flex flex-column overflow-hidden" data-board-id="<?= $board['id'] ?>">
        <div class="kanban-columns flex-grow-1 overflow-auto d-flex gap-3 align-items-stretch">
            <?php foreach ($board['columns'] as $column): ?>
            <div class="kanban-column bg-dark-subtle rounded border border-secondary d-flex flex-column h-100"
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
                <div class="column-cards p-2 flex-grow-1 overflow-y-auto" data-column-id="<?= $column['id'] ?>">
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
            }).fail(function(xhr) {
                const msg = xhr.responseJSON?.message || 'Failed to reorder columns.';
                showAlert(msg, 'danger');
                evt.item.parentNode.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
            });
        }
    });

    $('.column-cards').each(function() {
        const columnId = $(this).data('column-id');
        new Sortable(this, {
            group: 'cards',
            animation: 150,
            ghostClass: 'sortable-ghost',
            delay: 100,
            delayOnTouchOnly: true,
            onStart: function(evt) {
                $(evt.item).addClass('dragging');
            },
            onEnd: function(evt) {
                $(evt.item).removeClass('dragging');
                const targetColumn = $(evt.to).closest('.kanban-column');
                const columnId = targetColumn.data('column-id');
                const cardIds = Array.from(evt.to.querySelectorAll('.kanban-card'))
                    .map(el => $(el).data('card-id'));
                $.post('<?= base_url("cards/move") ?>', {
                    card_id: $(evt.item).data('card-id'),
                    column_id: columnId,
                    card_ids: JSON.stringify(cardIds)
                }).fail(function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to move card.';
                    showAlert(msg, 'danger');
                    if (evt.to !== evt.from) {
                        const originalPosition = evt.oldIndex;
                        if (originalPosition >= 0 && originalPosition < evt.from.children.length) {
                            evt.from.insertBefore(evt.item, evt.from.children[originalPosition]);
                        } else {
                            evt.from.appendChild(evt.item);
                        }
                    }
                });
            }
        });
    });

    $('.kanban-board').on('click', '.add-card-btn', function() {
        const columnId = $(this).data('column-id');
        showCardForm(columnId);
    });

    // Click on card to edit
    $('.kanban-board').on('click', '.kanban-card', function(e) {
        // Don't trigger if clicking on the complete toggle button
        if ($(e.target).closest('[data-action="toggle-complete"]').length) {
            return;
        }
        const cardId = $(this).data('card-id');
        // Fetch card data for editing
        $.ajax({
            url: `<?= base_url('cards') ?>/${cardId}`,
            method: 'GET',
            success: function(data) {
                const cardData = {
                    id: data.id,
                    title: data.title,
                    description: data.description,
                    priority: data.priority,
                    due_date: data.due_date ? data.due_date.replace(' ', 'T').slice(0, 16) : ''
                };
                showCardForm(data.column_id, cardData);
            },
            error: function() {
                showAlert('Failed to load card data.', 'danger');
            }
        });
    });

    // Toggle complete button
    $('.kanban-board').on('click', '[data-action="toggle-complete"]', function(e) {
        e.stopPropagation();
        const card = $(this).closest('.kanban-card');
        const cardId = card.data('card-id');
        const icon = $(this).find('i');
        const isCompleted = icon.hasClass('bi-check-circle-fill');

        $.ajax({
            url: `<?= base_url('cards') ?>/${cardId}`,
            method: 'PUT',
            data: { is_completed: !isCompleted },
            success: function() {
                location.reload();
            },
            error: function() {
                showAlert('Failed to update card.', 'danger');
            }
        });
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

        if (window.tiptapEditorInstance) {
            window.tiptapEditorInstance.destroy();
            window.tiptapEditorInstance = null;
        }

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
                    <input type="hidden" name="description" id="cardDescription">
                    <div id="editorToolbar"></div>
                    <div id="editorContent"></div>
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

        setTimeout(() => {
            window.tiptapEditorInstance = TiptapEditor.init('#editorContent', {
                content: cardData?.description || '',
                onUpdate: () => {
                    const markdown = TiptapEditor.getContent();
                    $('#cardDescription').val(markdown);
                }
            });

            TiptapEditor.createToolbar(document.getElementById('editorToolbar'));

            const markdown = TiptapEditor.getContent();
            $('#cardDescription').val(markdown);
        }, 100);

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
                    success: () => {
                        if (window.tiptapEditorInstance) {
                            window.tiptapEditorInstance.destroy();
                            window.tiptapEditorInstance = null;
                        }
                        location.reload();
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Failed to update card.';
                        showAlert(msg, 'danger');
                    }
                });
            } else {
                $.post('<?= base_url('cards') ?>', data, () => {
                    if (window.tiptapEditorInstance) {
                        window.tiptapEditorInstance.destroy();
                        window.tiptapEditorInstance = null;
                    }
                    location.reload();
                }).fail(function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to create card.';
                    showAlert(msg, 'danger');
                });
            }
        });

        document.getElementById('cardModal').addEventListener('hidden.bs.modal', function() {
            if (window.tiptapEditorInstance) {
                window.tiptapEditorInstance.destroy();
                window.tiptapEditorInstance = null;
            }
        }, { once: true });
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