<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="mb-0">
            <i class="bi bi-kanban me-2 text-primary"></i>
            <?= esc($board['name']) ?>
        </h1>
    </div>
    <div class="dropdown">
        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-three-dots"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="/boards/<?= $board['id'] ?>/edit"><i class="bi bi-pencil me-2"></i>Edit</a></li>
            <li><a class="dropdown-item" href="#" data-action="archive" data-board-id="<?= $board['id'] ?>"><i class="bi bi-archive me-2"></i>Archive</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#" data-action="delete" data-board-id="<?= $board['id'] ?>"><i class="bi bi-trash me-2"></i>Delete</a></li>
        </ul>
    </div>
</div>

<!-- Mobile Column Indicator -->
<div class="d-lg-none mb-3">
    <div class="d-flex align-items-center justify-content-between">
        <span class="text-muted small">Column 1 of <?= count($columns) ?></span>
        <div>
            <button class="btn btn-sm btn-outline-light" id="prevColumnBtn" disabled>
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="btn btn-sm btn-outline-light" id="nextColumnBtn">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- Kanban Board -->
<div class="kanban-board">
    <div class="columns-container" id="columnsContainer">
        <?php foreach ($columns as $index => $column): ?>
            <div class="kanban-column-wrapper" data-column-id="<?= $column['id'] ?>" data-index="<?= $index ?>">
                <div class="kanban-column">
                    <div class="column-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><?= esc($column['name']) ?></h6>
                            <small class="text-muted"><?= count($column['cards']) ?> cards</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted p-1" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-action="edit-column" data-column-id="<?= $column['id'] ?>">
                                    <i class="bi bi-pencil me-2"></i>Rename
                                </a></li>
                                <?php if (count($columns) > 1): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" data-action="delete-column" data-column-id="<?= $column['id'] ?>">
                                    <i class="bi bi-trash me-2"></i>Delete
                                </a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="card-list" id="cardList-<?= $column['id'] ?>">
                        <?php if (empty($column['cards'])): ?>
                            <div class="empty-column text-center text-muted py-4">
                                <small>No cards</small>
                            </div>
                        <?php else: ?>
                            <?php foreach ($column['cards'] as $card): ?>
                                <?= view('cards/_card', ['card' => $card]) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="add-card">
                        <button class="btn btn-sm btn-link text-muted w-100 text-start" data-bs-toggle="collapse"
                                data-bs-target="#newCard-<?= $column['id'] ?>">
                            <i class="bi bi-plus me-1"></i>Add card
                        </button>
                        <div class="collapse" id="newCard-<?= $column['id'] ?>">
                            <form class="new-card-form mt-2" data-column-id="<?= $column['id'] ?>">
                                <input type="text" class="form-control form-control-sm mb-2" placeholder="Card title..."
                                       name="title" required>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">Add</button>
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="collapse"
                                            data-bs-target="#newCard-<?= $column['id'] ?>">Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Card Detail Modal -->
<div class="modal fade" id="cardModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cardModalTitle">Card Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cardModalBody">
                <!-- Card content loaded here -->
            </div>
        </div>
    </div>
</div>

<?php $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const KTM = {
    currentColumnIndex: 0,
    columns: <?= json_encode(array_values($columns)) ?>,

    init() {
        this.initDragAndDrop();
        this.initMobileSwipe();
        this.initNewCardForms();
        this.initColumnActions();
        this.initBoardActions();
    },

    initDragAndDrop() {
        // Card drag-drop
        document.querySelectorAll('.card-list').forEach(list => {
            new Sortable(list, {
                group: 'cards',
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'dragging',
                delay: 100,
                delayOnTouchOnly: true,
                onEnd: (evt) => this.handleCardDrop(evt),
            });
        });

        // Column drag-drop (desktop only)
        if (window.innerWidth >= 992) {
            new Sortable(document.getElementById('columnsContainer'), {
                animation: 150,
                handle: '.column-header',
                onEnd: (evt) => this.handleColumnDrop(evt),
            });
        }
    },

    initMobileSwipe() {
        if (window.innerWidth >= 992) return;

        const container = document.getElementById('columnsContainer');
        let startX = 0;

        container.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });

        container.addEventListener('touchend', (e) => {
            const diff = startX - e.changedTouches[0].clientX;

            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    this.showNextColumn();
                } else {
                    this.showPrevColumn();
                }
            }
        });

        this.updateColumnNavigation();
    },

    initNewCardForms() {
        document.querySelectorAll('.new-card-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.createCard(form);
            });
        });
    },

    initColumnActions() {
        document.querySelectorAll('[data-action="edit-column"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.editColumn(btn.dataset.columnId);
            });
        });

        document.querySelectorAll('[data-action="delete-column"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this column?')) {
                    this.deleteColumn(btn.dataset.columnId);
                }
            });
        });
    },

    initBoardActions() {
        document.querySelector('[data-action="archive"]')?.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Are you sure you want to archive this board?')) {
                this.archiveBoard(e.target.dataset.boardId);
            }
        });

        document.querySelector('[data-action="delete"]')?.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this board? This cannot be undone.')) {
                this.deleteBoard(e.target.dataset.boardId);
            }
        });
    },

    handleCardDrop(evt) {
        const cardId = evt.item.dataset.cardId;
        const newColumnId = evt.to.closest('.kanban-column-wrapper').dataset.columnId;
        const newIndex = evt.newIndex;

        fetch(`/cards/${cardId}/move`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                target_column_id: newColumnId,
                position: newIndex,
            }),
        }).then(response => response.json())
          .then(data => {
              if (data.status !== 'success') {
                  evt.from.appendChild(evt.item);
                  alert('Failed to move card');
              }
          });
    },

    handleColumnDrop(evt) {
        const columnIds = Array.from(document.querySelectorAll('.kanban-column-wrapper'))
            .map(el => el.dataset.columnId);

        fetch('/columns/reorder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ column_ids: columnIds }),
        });
    },

    createCard(form) {
        const columnId = form.dataset.columnId;
        const title = form.querySelector('input[name="title"]').value;

        fetch('/cards', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: new URLSearchParams({
                column_id: columnId,
                board_id: <?= $board['id'] ?>,
                title: title,
            }),
        }).then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  location.reload();
              } else {
                  alert('Failed to create card: ' + (data.message || 'Unknown error'));
              }
          });
    },

    showPrevColumn() {
        if (this.currentColumnIndex > 0) {
            this.currentColumnIndex--;
            this.updateColumnView();
        }
    },

    showNextColumn() {
        if (this.currentColumnIndex < this.columns.length - 1) {
            this.currentColumnIndex++;
            this.updateColumnView();
        }
    },

    updateColumnView() {
        document.querySelectorAll('.kanban-column-wrapper').forEach((el, index) => {
            el.style.display = index === this.currentColumnIndex ? 'block' : 'none';
        });
        this.updateColumnNavigation();
    },

    updateColumnNavigation() {
        const prevBtn = document.getElementById('prevColumnBtn');
        const nextBtn = document.getElementById('nextColumnBtn');

        if (prevBtn) prevBtn.disabled = this.currentColumnIndex === 0;
        if (nextBtn) nextBtn.disabled = this.currentColumnIndex === this.columns.length - 1;
    },

    editColumn(columnId) {
        const newName = prompt('Enter new column name:');
        if (newName) {
            fetch(`/columns/${columnId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ name: newName }),
            }).then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      location.reload();
                  } else {
                      alert('Failed to update column');
                  }
              });
        }
    },

    deleteColumn(columnId) {
        fetch(`/columns/${columnId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        }).then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  location.reload();
              } else {
                  alert('Failed to delete column: ' + (data.message || 'Unknown error'));
              }
          });
    },

    archiveBoard(boardId) {
        fetch(`/boards/${boardId}/archive`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        }).then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  location.href = '/';
              } else {
                  alert('Failed to archive board');
              }
          });
    },

    deleteBoard(boardId) {
        fetch(`/boards/${boardId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        }).then(() => location.href = '/');
    },
};

document.addEventListener('DOMContentLoaded', () => KTM.init());
</script>
<?php $this->endSection() ?>