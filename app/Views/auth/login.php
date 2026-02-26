<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container auth-container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card bg-dark border-secondary">
                <div class="card-header bg-dark border-secondary text-center">
                    <h4><i class="bi bi-kanban"></i> Kanban Task Manager</h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                    <?php endif; ?>

                    <form action="<?= base_url('auth/login') ?>" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="email" name="email" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="password" name="password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="<?= base_url('auth/register') ?>" class="text-decoration-none">Create an account</a>
                        <br>
                        <a href="<?= base_url('auth/forgot-password') ?>" class="text-decoration-none text-muted small">
                            Forgot password?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>