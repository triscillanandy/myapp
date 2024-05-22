<!-- forgotpassword.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <h1>Change Password</h1>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <form action="<?= base_url('/forgotpassword') ?>" method="post">
        <label for="old_password">Old Password:</label><br>
        <input type="password" id="old_password" name="old_password"><br>

        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password"><br>

        <label for="confirm_password">Confirm New Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password"><br>

        <button type="submit">Change Password</button>
    </form>
</body>
</html>