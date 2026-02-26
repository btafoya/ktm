<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container auth-container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card bg-dark border-secondary">
                <div class="card-header bg-dark border-secondary text-center">
                    <h4>Create Account</h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                    <?php endif; ?>

                    <?= validation_list_errors('alert alert-danger') ?>

                    <form action="<?= base_url('auth/register') ?>" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="email" name="email" value="<?= old('email') ?>" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="full_name" name="full_name" value="<?= old('full_name') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="password" name="password" required minlength="8">
                            <div class="form-text text-muted">Minimum 8 characters</div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="password_confirm" name="password_confirm" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="<?= base_url('auth/login') ?>" class="text-decoration-none">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>