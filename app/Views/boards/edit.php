<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('boards') ?>">Boards</a></li>
            <li class="breadcrumb-item active">Edit Board</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark border-secondary">
                <div class="card-header bg-dark border-secondary">
                    <h5>Edit Board</h5>
                </div>
                <div class="card-body">
                    <?= validation_list_errors('alert alert-danger') ?>

                    <form action="<?= base_url("boards/{$board['id']}/edit") ?>" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Board Name</label>
                            <input type="text" class="form-control bg-dark-subtle text-light border-secondary"
                                   id="name" name="name" value="<?= old('name', $board['name']) ?>" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control bg-dark-subtle text-light border-secondary"
                                      id="description" name="description" rows="3"
                                      maxlength="500"><?= old('description', $board['description'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="background_color" class="form-label">Background Color</label>
                            <input type="color" class="form-control form-control-color bg-dark-subtle text-light border-secondary"
                                   id="background_color" name="background_color" value="<?= old('background_color', $board['background_color'] ?? '#212529') ?>">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="<?= base_url("boards/{$board['id']}") ?>" class="btn btn-outline-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>