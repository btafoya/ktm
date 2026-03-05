$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    // ============================================
    // Toast Notifications
    // ============================================
    function showToast(message, type = 'success') {
        const toastContainer = $('#toastContainer');
        if (!toastContainer.length) {
            $('body').append('<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>');
        }

        const bgClass = type === 'success' ? 'text-bg-success' :
                        type === 'danger' ? 'text-bg-danger' :
                        type === 'warning' ? 'text-bg-warning' : 'text-bg-info';

        const toastHtml = `
            <div class="toast align-items-center ${bgClass} border-0" role="alert" aria-live="polite" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        const toastElement = $(toastHtml);
        $('#toastContainer').append(toastElement);
        const toast = new bootstrap.Toast(toastElement[0], { delay: 5000 });
        toast.show();

        toastElement.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    function showAlert(message, type = 'success') {
        showToast(message, type);
    }

    // ============================================
    // Confirm Dialogs
    // ============================================
    function showConfirm(options) {
        const {
            title = 'Confirm',
            message = 'Are you sure you want to proceed?',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            confirmClass = 'btn-primary',
            onConfirm = null,
            onCancel = null
        } = options;

        // Remove any existing confirm modal
        $('#confirmModal').remove();

        const modalHtml = `
            <div class="modal fade confirm-dialog" id="confirmModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-dark text-light border-secondary">
                        <div class="modal-header border-secondary">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-secondary" data-dismiss="cancel">${cancelText}</button>
                            <button type="button" class="btn ${confirmClass}" data-dismiss="confirm">${confirmText}</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modalHtml);
        const modalElement = $('#confirmModal');
        const modal = new bootstrap.Modal(modalElement[0]);

        modalElement.on('click', '[data-dismiss="confirm"]', function() {
            modal.hide();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });

        modalElement.on('click', '[data-dismiss="cancel"]', function() {
            modal.hide();
            if (typeof onCancel === 'function') {
                onCancel();
            }
        });

        modalElement.on('hidden.bs.modal', function() {
            $(this).remove();
        });

        modal.show();
    }

    // ============================================
    // Loading States
    // ============================================
    function showLoading(message = 'Loading...') {
        const overlay = $('#loadingOverlay');
        if (!overlay.length) {
            const loadingHtml = `
                <div id="loadingOverlay" class="loading-overlay" role="status" aria-live="polite" aria-busy="true">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">${message}</span>
                    </div>
                </div>
            `;
            $('body').append(loadingHtml);
        }
        $('#loadingOverlay').show();
    }

    function hideLoading() {
        $('#loadingOverlay').hide();
    }

    // ============================================
    // Keyboard Navigation for Kanban Board
    // ============================================
    function initKeyboardNavigation() {
        const cards = document.querySelectorAll('.kanban-card[tabindex="0"]');

        cards.forEach(card => {
            card.addEventListener('keydown', function(e) {
                const currentCard = this;
                const column = currentCard.closest('.kanban-column');
                const columnCards = Array.from(column.querySelectorAll('.kanban-card[tabindex="0"]'));
                const currentIndex = columnCards.indexOf(currentCard);

                let targetCard = null;

                switch(e.key) {
                    case 'ArrowUp':
                        e.preventDefault();
                        if (currentIndex > 0) {
                            targetCard = columnCards[currentIndex - 1];
                        }
                        break;
                    case 'ArrowDown':
                        e.preventDefault();
                        if (currentIndex < columnCards.length - 1) {
                            targetCard = columnCards[currentIndex + 1];
                        }
                        break;
                    case 'ArrowLeft':
                        e.preventDefault();
                        const prevColumn = column.previousElementSibling?.closest('.kanban-column');
                        if (prevColumn) {
                            const prevColumnCards = Array.from(prevColumn.querySelectorAll('.kanban-card[tabindex="0"]'));
                            targetCard = prevColumnCards[Math.min(currentIndex, prevColumnCards.length - 1)];
                        }
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        const nextColumn = column.nextElementSibling?.closest('.kanban-column');
                        if (nextColumn) {
                            const nextColumnCards = Array.from(nextColumn.querySelectorAll('.kanban-card[tabindex="0"]'));
                            targetCard = nextColumnCards[Math.min(currentIndex, nextColumnCards.length - 1)];
                        }
                        break;
                    case 'Enter':
                    case ' ':
                        e.preventDefault();
                        currentCard.click();
                        break;
                    case 'c':
                    case 'C':
                        e.preventDefault();
                        const completeBtn = currentCard.querySelector('[data-action="toggle-complete"]');
                        if (completeBtn) {
                            completeBtn.click();
                        }
                        break;
                }

                if (targetCard) {
                    targetCard.focus();
                }
            });
        });
    }

    // ============================================
    // Focus Management in Modals
    // ============================================
    function initModalFocusManagement() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('shown.bs.modal', function() {
                const focusableElements = this.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                if (focusableElements.length > 0) {
                    focusableElements[0].focus();
                }
            });

            modal.addEventListener('keydown', function(e) {
                if (e.key !== 'Tab') return;

                const focusableElements = Array.from(this.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                ));

                if (focusableElements.length === 0) return;

                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];

                if (e.shiftKey && document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                } else if (!e.shiftKey && document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            });
        });
    }

    // ============================================
    // Empty States
    // ============================================
    function initEmptyStates() {
        // Check for empty columns and show empty state
        document.querySelectorAll('.column-cards').forEach(columnCards => {
            const cards = columnCards.querySelectorAll('.kanban-card');
            if (cards.length === 0) {
                const emptyStateHtml = `
                    <div class="empty-state d-flex flex-column align-items-center justify-content-center py-4">
                        <i class="bi bi-inbox text-muted mb-2"></i>
                        <p class="text-muted small mb-0">No cards yet</p>
                    </div>
                `;
                if (!columnCards.querySelector('.empty-state')) {
                    $(columnCards).append(emptyStateHtml);
                }
            } else {
                columnCards.querySelector('.empty-state')?.remove();
            }
        });

        // Check for empty board (no columns)
        const kanbanColumns = document.querySelector('.kanban-columns');
        if (kanbanColumns && kanbanColumns.querySelectorAll('.kanban-column').length === 0) {
            const emptyBoardHtml = `
                <div class="empty-state d-flex flex-column align-items-center justify-content-center flex-grow-1">
                    <i class="bi bi-grid-3x3-gap text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                    <h4 class="text-muted mb-2">No columns yet</h4>
                    <p class="text-muted">Add your first column to get started</p>
                </div>
            `;
            if (!kanbanColumns.querySelector('.empty-state')) {
                $(kanbanColumns).append(emptyBoardHtml);
            }
        }
    }

    // ============================================
    // Touch Optimization
    // ============================================
    function initTouchOptimizations() {
        // Prevent accidental double-tap zoom on buttons
        if ('ontouchstart' in window) {
            document.addEventListener('dblclick', function(e) {
                if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                    e.preventDefault();
                }
            }, { passive: false });
        }

        // Add long-press indicator for draggable elements
        let longPressTimer;
        const LONG_PRESS_DURATION = 500;

        document.querySelectorAll('.kanban-card').forEach(card => {
            card.addEventListener('touchstart', function() {
                clearTimeout(longPressTimer);
                longPressTimer = setTimeout(() => {
                    $(this).addClass('long-press-active');
                }, LONG_PRESS_DURATION);
            }, { passive: true });

            card.addEventListener('touchend', function() {
                clearTimeout(longPressTimer);
                $(this).removeClass('long-press-active');
            });

            card.addEventListener('touchmove', function() {
                clearTimeout(longPressTimer);
                $(this).removeClass('long-press-active');
            }, { passive: true });
        });
    }

    // ============================================
    // ARIA Live Announcements
    // ============================================
    function announce(message) {
        const liveRegion = document.getElementById('aria-live-region');
        if (liveRegion) {
            liveRegion.textContent = '';
            setTimeout(() => {
                liveRegion.textContent = message;
            }, 100);
        }
    }

    // ============================================
    // Initialize all features
    // ============================================
    $(document).on('kanban:update', function() {
        initKeyboardNavigation();
        initEmptyStates();
    });

    // Run on initial load
    if (document.querySelector('.kanban-board')) {
        initKeyboardNavigation();
        initEmptyStates();
        initTouchOptimizations();
    }

    initModalFocusManagement();

    // Make functions globally available
    window.showAlert = showAlert;
    window.showToast = showToast;
    window.showConfirm = showConfirm;
    window.showLoading = showLoading;
    window.hideLoading = hideLoading;
    window.announce = announce;
});