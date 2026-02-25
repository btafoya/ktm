<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="/" class="text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Back to Boards
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Create New Board</h5>
    </div>
    <div class="card-body">
        <form action="/boards" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Board Name</label>
                <input type="text" class="form-control" id="name" name="name" required
                       value="<?= old('name') ?>" autofocus>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create Board</button>
                <a href="/" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php $this->endSection() ?>