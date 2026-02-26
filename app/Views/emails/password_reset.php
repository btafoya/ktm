<p>Hello <?= esc($full_name) ?>,</p>

<p>You recently requested to reset your password for your Kanban Task Manager account.</p>

<p>Click the link below to reset your password:</p>

<p><a href="<?= esc($resetUrl) ?>"><?= esc($resetUrl) ?></a></p>

<p>This link will expire in 1 hour.</p>

<p>If you did not request this password reset, please ignore this email.</p>