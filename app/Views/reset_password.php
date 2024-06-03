<!-- app/Views/reset_password.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>
    <?php if (session()->has('message')): ?>
        <div class="alert">
            <?= session('message') ?>
        </div>
    <?php endif; ?>
    <form action="<?= base_url('passwordreset/update') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token) ?>">
        <div>
            <label for="password">New Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </div>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
