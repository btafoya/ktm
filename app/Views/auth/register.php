<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<form action="/auth/register" method="post">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required
               value="<?= old('email') ?>" autofocus>
    </div>
    <div class="mb-3">
        <label for="display_name" class="form-label">Display Name (optional)</label>
        <input type="text" class="form-control" id="display_name" name="display_name"
               value="<?= old('display_name') ?>" placeholder="How should we call you?">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required
               minlength="8">
        <div class="form-text">Must be at least 8 characters</div>
    </div>
    <div class="mb-3">
        <label for="password_confirm" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
    </div>
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">Create Account</button>
    </div>
</form>

<?php $this->endSection() ?>