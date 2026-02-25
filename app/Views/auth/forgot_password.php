<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<p class="text-muted mb-3">Enter your email address and we'll send you a link to reset your password.</p>

<form action="/auth/forgot-password" method="post">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required
               value="<?= old('email') ?>" autofocus>
    </div>
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </div>
</form>

<div class="text-center mt-3">
    <a href="/auth/login" class="text-decoration-none">Back to Login</a>
</div>

<?php $this->endSection() ?>