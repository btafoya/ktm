<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-3">
    <h2>Settings</h2>

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

    <div class="row">
        <div class="col-md-6">
            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0">Profile</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('settings/update-profile') ?>" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control bg-dark-subtle text-light border-secondary"
                                       id="full_name" name="full_name" value="<?= esc($user['full_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control bg-dark-subtle text-light border-secondary"
                                       id="email" name="email" value="<?= esc($user['email']) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select bg-dark-subtle text-light border-secondary"
                                    id="timezone" name="timezone">
                                <option value="UTC" <?= ($user['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                <option value="America/New_York" <?= ($user['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>Eastern Time</option>
                                <option value="America/Chicago" <?= ($user['timezone'] ?? '') === 'America/Chicago' ? 'selected' : '' ?>>Central Time</option>
                                <option value="America/Denver" <?= ($user['timezone'] ?? '') === 'America/Denver' ? 'selected' : '' ?>>Mountain Time</option>
                                <option value="America/Los_Angeles" <?= ($user['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' ?>>Pacific Time</option>
                                <option value="Europe/London" <?= ($user['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>London</option>
                                <option value="Europe/Paris" <?= ($user['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : '' ?>>Paris</option>
                                <option value="Asia/Tokyo" <?= ($user['timezone'] ?? '') === 'Asia/Tokyo' ? 'selected' : '' ?>>Tokyo</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                    </form>
                </div>
            </div>

            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <?php $validation = \Config\Services::validation(); ?>
                    <?php if ($validation->getErrors()): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($validation->getErrors() as $error): ?>
                            <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <form action="<?= base_url('settings/update-password') ?>" method="post">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="current_password" name="current_password" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control bg-dark-subtle text-light border-secondary"
                                       id="new_password" name="new_password" required minlength="8">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control bg-dark-subtle text-light border-secondary"
                                       id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Google Integration</h5>
                    <?php if ($googleToken): ?>
                    <span class="badge bg-success">Connected</span>
                    <?php else: ?>
                    <span class="badge bg-secondary">Disconnected</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if ($googleToken): ?>
                    <div class="alert alert-success mb-3">
                        <i class="bi bi-check-circle-fill"></i> Google account connected
                    </div>

                    <ul class="nav nav-tabs border-secondary mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active text-light border-secondary border-bottom-0 bg-transparent"
                                    data-bs-toggle="tab" data-bs-target="#calendars" type="button">Calendars</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link text-light border-secondary border-bottom-0 bg-transparent"
                                    data-bs-toggle="tab" data-bs-target="#gmail" type="button">Gmail</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="calendars">
                            <div class="mb-3">
                                <button class="btn btn-sm btn-outline-primary" id="loadCalendarsBtn">
                                    <i class="bi bi-arrow-repeat"></i> Refresh List
                                </button>
                                <button class="btn btn-sm btn-outline-success" id="addCalendarBtn" disabled>
                                    <i class="bi bi-plus-lg"></i> Add Calendar
                                </button>
                            </div>

                            <div id="availableCalendars" class="mb-3" style="display: none;">
                                <h6 class="text-muted">Available Calendars</h6>
                                <div class="list-group bg-dark-subtle" id="calendarsList"></div>
                            </div>

                            <div id="syncedCalendars">
                                <h6 class="text-muted">Synced Calendars</h6>
                                <div id="syncedCalendarsList" class="list-group">
                                    <?php if ($calendars): ?>
                                        <?php foreach ($calendars as $cal): ?>
                                        <div class="list-group-item bg-transparent border-secondary d-flex justify-content-between align-items-center" data-id="<?= $cal['id'] ?>">
                                            <div>
                                                <div><?= esc($cal['name']) ?></div>
                                                <small class="text-muted"><?= $cal['sync_enabled'] ? 'Syncing' : 'Paused' ?></small>
                                            </div>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-primary refresh-calendar" title="Refresh">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-<?= $cal['sync_enabled'] ? 'warning' : 'success' ?> toggle-calendar" title="<?= $cal['sync_enabled'] ? 'Pause' : 'Resume' ?>">
                                                    <i class="bi bi-<?= $cal['sync_enabled'] ? 'pause' : 'play' ?>"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-calendar" title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted text-center py-3">No calendars synced yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="gmail">
                            <div class="mb-3">
                                <button class="btn btn-sm btn-outline-primary" id="syncGmailBtn">
                                    <i class="bi bi-arrow-repeat"></i> Sync Emails
                                </button>
                                <button class="btn btn-sm btn-outline-success" id="addSenderBtn">
                                    <i class="bi bi-plus-lg"></i> Add Sender Rule
                                </button>
                            </div>

                            <div id="senderRules">
                                <h6 class="text-muted">Email Sender Rules</h6>
                                <div id="senderRulesList" class="list-group">
                                    <p class="text-muted text-center py-3">Loading...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-secondary">

                    <button class="btn btn-outline-danger w-100" id="disconnectGoogleBtn">
                        <i class="bi bi-x-circle"></i> Disconnect Google Account
                    </button>
                    <?php else: ?>
                    <p class="text-muted mb-3">
                        Connect your Google account to sync tasks with Calendar and manage emails via Gmail.
                    </p>
                    <a href="<?= base_url('google/auth') ?>" class="btn btn-primary w-100">
                        <i class="bi bi-google"></i> Connect Google Account
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const userId = <?= session()->get('user_id') ?>;

    $('#disconnectGoogleBtn').on('click', function() {
        if (confirm('Are you sure you want to disconnect your Google account?')) {
            $.post('<?= base_url('google/disconnect') ?>', () => location.reload());
        }
    });

    // Load available calendars
    $('#loadCalendarsBtn').on('click', function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');

        $.get('<?= base_url('google/calendars') ?>', function(data) {
            $('#availableCalendars').show();
            const $list = $('#calendarsList').empty();
            $('#addCalendarBtn').prop('disabled', data.calendars.length === 0);

            data.calendars.forEach(cal => {
                const synced = <?= json_encode(array_column($calendars ?? [], 'google_calendar_id')) ?>;
                if (!synced.includes(cal.id)) {
                    $list.append(`
                        <div class="list-group-item bg-transparent border-secondary d-flex justify-content-between align-items-center" data-id="${cal.id}">
                            <div>
                                <div>${esc(cal.summary)}</div>
                                <small class="text-muted">${cal.primary ? 'Primary' : ''}</small>
                            </div>
                            <button class="btn btn-sm btn-success sync-calendar" data-id="${cal.id}" data-name="${esc(cal.summary)}" data-primary="${cal.primary}">
                                <i class="bi bi-plus"></i> Sync
                            </button>
                        </div>
                    `);
                }
            });

            if ($list.children().length === 0) {
                $list.append('<div class="p-2 text-muted text-center">All calendars synced</div>');
            }

            $btn.prop('disabled', false).html('<i class="bi bi-arrow-repeat"></i> Refresh List');
        });
    });

    // Sync calendar
    $(document).on('click', '.sync-calendar', function() {
        const $btn = $(this);
        const id = $btn.data('id');
        const name = $btn.data('name');
        const primary = $btn.data('primary');

        $.post('<?= base_url('google/sync-calendar') ?>', JSON.stringify({
            google_calendar_id: id,
            name: name,
            is_primary: primary
        }), function(data) {
            if (data.success) {
                $btn.closest('.list-group-item').remove();
                loadSyncedCalendars();
            } else {
                alert(data.message || 'Failed to sync calendar');
            }
        }, 'json');
    });

    // Toggle calendar sync
    $(document).on('click', '.toggle-calendar', function() {
        const $item = $(this).closest('.list-group-item');
        const id = $item.data('id');

        $.post('<?= base_url('google/toggle-sync/' + id) ?>', function(data) {
            if (data.success) {
                loadSyncedCalendars();
            }
        });
    });

    // Refresh calendar
    $(document).on('click', '.refresh-calendar', function() {
        const $item = $(this).closest('.list-group-item');
        const id = $item.data('id');
        const $btn = $(this);

        $btn.prop('disabled', true);

        $.post('<?= base_url('google/refresh-calendar/' + id) ?>', function(data) {
            $btn.prop('disabled', false);
            showToast(data.message || 'Calendar refreshed', data.success ? 'success' : 'danger');
        });
    });

    // Delete calendar
    $(document).on('click', '.delete-calendar', function() {
        const $item = $(this).closest('.list-group-item');
        const id = $item.data('id');

        if (confirm('Remove this calendar and its events?')) {
            $.post('<?= base_url('google/delete-calendar/' + id) ?>', function(data) {
                if (data.success) {
                    loadSyncedCalendars();
                }
            });
        }
    });

    function loadSyncedCalendars() {
        $.get('<?= base_url('google/get-connected-calendars') ?>', function(data) {
            const $list = $('#syncedCalendarsList');
            $list.empty();

            if (data.calendars.length === 0) {
                $list.append('<p class="text-muted text-center py-3">No calendars synced yet</p>');
                return;
            }

            data.calendars.forEach(cal => {
                $list.append(`
                    <div class="list-group-item bg-transparent border-secondary d-flex justify-content-between align-items-center" data-id="${cal.id}">
                        <div>
                            <div>${esc(cal.name)}</div>
                            <small class="text-muted">${cal.sync_enabled ? 'Syncing' : 'Paused'}</small>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary refresh-calendar" title="Refresh">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-${cal.sync_enabled ? 'warning' : 'success'} toggle-calendar" title="${cal.sync_enabled ? 'Pause' : 'Resume'}">
                                <i class="bi bi-${cal.sync_enabled ? 'pause' : 'play'}"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-calendar" title="Remove">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `);
            });
        });
    }

    // Gmail sync
    $('#syncGmailBtn').on('click', function() {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Syncing...');

        $.post('<?= base_url('gmail/sync') ?>', function(data) {
            $btn.prop('disabled', false).html('<i class="bi bi-arrow-repeat"></i> Sync Emails');
            showToast(data.message || 'Sync complete', data.success ? 'success' : 'danger');
            loadSenderRules();
        });
    });

    // Load sender rules
    function loadSenderRules() {
        $.get('<?= base_url('gmail/senders') ?>', function(data) {
            const $list = $('#senderRulesList');
            $list.empty();

            if (data.senders.length === 0) {
                $list.append('<p class="text-muted text-center py-3">No sender rules configured</p>');
                return;
            }

            data.senders.forEach(sender => {
                $list.append(`
                    <div class="list-group-item bg-transparent border-secondary d-flex justify-content-between align-items-center" data-id="${sender.id}">
                        <div>
                            <div><strong>${esc(sender.email)}</strong></div>
                            <small class="text-muted">${sender.name ? esc(sender.name) + ' • ' : ''}${sender.keyword ? 'Keyword: ' + esc(sender.keyword) : 'All emails'}</small>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-${sender.is_active ? 'warning' : 'success'} toggle-sender" title="${sender.is_active ? 'Disable' : 'Enable'}">
                                <i class="bi bi-${sender.is_active ? 'pause' : 'play'}"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-sender" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `);
            });
        });
    }

    // Add sender rule
    $('#addSenderBtn').on('click', function() {
        const email = prompt('Enter email address (or use * for wildcard):');
        if (!email) return;

        const name = prompt('Sender name (optional):') || '';
        const keyword = prompt('Filter by keyword in subject (optional):') || '';

        $.post('<?= base_url('gmail/create-sender') ?>', {
            email: email,
            name: name,
            keyword: keyword
        }, function(data) {
            if (data.success) {
                loadSenderRules();
                showToast('Sender rule added', 'success');
            } else {
                showToast(data.message || 'Failed to add sender', 'danger');
            }
        });
    });

    // Toggle sender
    $(document).on('click', '.toggle-sender', function() {
        const $item = $(this).closest('.list-group-item');
        const id = $item.data('id');
        const $btn = $(this);
        const isActive = $btn.hasClass('toggle-sender') && $btn.find('i').hasClass('bi-pause');

        $.post('<?= base_url('gmail/update-sender/') ?>' + id, {
            is_active: !isActive
        }, function(data) {
            if (data.success) {
                loadSenderRules();
            }
        });
    });

    // Delete sender
    $(document).on('click', '.delete-sender', function() {
        const $item = $(this).closest('.list-group-item');
        const id = $item.data('id');

        if (confirm('Delete this sender rule?')) {
            $.post('<?= base_url('gmail/delete-sender/') ?>' + id, function(data) {
                if (data.success) {
                    loadSenderRules();
                }
            });
        }
    });

    // Load sender rules on init
    loadSenderRules();

    // Helper functions
    function esc(str) {
        if (!str) return '';
        return $('<div/>').text(str).html();
    }

    function showToast(message, type = 'info') {
        const toast = $(`
            <div class="toast align-items-center text-bg-${type} border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${esc(message)}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        $('.toast-container').append(toast);
        setTimeout(() => toast.remove(), 3000);
    }
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>