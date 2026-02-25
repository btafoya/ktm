<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<form action="/auth/login" method="post">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required
               value="<?= old('email') ?>" autofocus>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">Remember me</label>
    </div>
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">Sign In</button>
    </div>
    <div class="text-center mt-3">
        <a href="/auth/forgot-password" class="text-decoration-none">Forgot password?</a>
    </div>
</form>

<?php $this->endSection() ?>