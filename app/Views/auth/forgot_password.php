<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container auth-container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card bg-dark border-secondary">
                <div class="card-header bg-dark border-secondary">
                    <h5>Forgot Password</h5>
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

                    <form action="<?= base_url('auth/forgot-password') ?>" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="email" name="email" required autofocus>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Send Reset Link</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="<?= base_url('auth/login') ?>" class="text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>