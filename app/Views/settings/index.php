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

    <div class="row">
        <div class="col-md-8">
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
                    <?= validation_list_errors('alert alert-danger') ?>
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

        <div class="col-md-4">
            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header bg-dark border-secondary">
                    <h5 class="mb-0">Google Integration</h5>
                </div>
                <div class="card-body">
                    <?php if ($googleToken): ?>
                    <div class="alert alert-success mb-3">
                        <i class="bi bi-check-circle-fill"></i> Google account connected
                    </div>
                    <?php if ($calendars): ?>
                    <h6>Synced Calendars</h6>
                    <ul class="list-group list-group-flush mb-3">
                        <?php foreach ($calendars as $cal): ?>
                        <li class="list-group-item bg-transparent border-secondary d-flex justify-content-between align-items-center">
                            <span><?= esc($cal['name']) ?></span>
                            <?php if ($cal['sync_enabled']): ?>
                            <span class="badge bg-success small">Syncing</span>
                            <?php else: ?>
                            <span class="badge bg-secondary small">Paused</span>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <button class="btn btn-outline-danger w-100" id="disconnectGoogleBtn">
                        <i class="bi bi-x-circle"></i> Disconnect
                    </button>
                    <?php else: ?>
                    <p class="text-muted mb-3">
                        Connect your Google account to sync tasks with Calendar and manage emails via Gmail.
                    </p>
                    <a href="<?= base_url('google/auth') ?>" class="btn btn-primary w-100">
                        <i class="bi bi-google"></i> Connect Google
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
$('#disconnectGoogleBtn').on('click', function() {
    if (confirm('Are you sure you want to disconnect your Google account?')) {
        $.post('<?= base_url('google/disconnect') ?>', () => location.reload());
    }
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>