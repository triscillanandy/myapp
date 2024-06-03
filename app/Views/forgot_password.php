<!-- app/Views/forgot_password.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body>
    <h1>Forgot Password</h1>
    <?php if (session()->has('message')): ?>
        <div class="alert">
            <?= session('message') ?>
        </div>
    <?php endif; ?>
    <form action="<?= base_url('passwordreset/request') ?>" method="post">
        <?= csrf_field() ?>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
