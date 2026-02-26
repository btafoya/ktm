<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container auth-container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card bg-dark border-secondary">
                <div class="card-header bg-dark border-secondary">
                    <h5>Reset Password</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                    <?php endif; ?>

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

                    <form action="<?= base_url('auth/reset-password?token=' . urlencode($token ?? '')) ?>" method="post">
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="password" name="password" required minlength="8">
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="password_confirm" name="password_confirm" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>