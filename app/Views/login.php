<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <div class="row d-flex align-items-center justify-content-center" style="min-height: 600px;">
            <div class="card">
                <div class="card-content">
                    <div class="card-body shadow">
                        <?php if (isset($_SESSION['success'])) { ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $_SESSION['success'];
                                unset($_SESSION['success']);
                                ?>
                            </div>
                        <?php } ?>
                        <div class="card-title">Login</div>
                        <form action="<?= base_url('/users/login') ?>" method="POST">

                            <?= csrf_field(); ?>
                            <?php if (!empty(session()->getFlashdata('fail'))) : ?>
                                <div class="alert alert-danger"><?= session()->getFlashdata('fail'); ?></div>
                            <?php endif ?>
                            <?php if (!empty(session()->getFlashdata('success'))) : ?>
                                <div class="alert alert-success"><?= session()->getFlashdata('success'); ?></div>
                            <?php endif ?>
                            <div class="form-group">
                                <input type="email" placeholder="Enter email..." class="form-control" name="email" value="<?= set_value('email') ?>" required>
                            </div>
                            <div class="form-group">
                                <input type="password" placeholder="Enter password..." class="form-control" name="password" value="<?= set_value('password') ?>" required>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-secondary shadow-sm">Login</button>
                                <a href="<?= base_url('register') ?>">Do not have an account?</a><br>
                                <a href="<?= base_url('forgotpassword') ?>">Forgot password</a>
                            </div>
                        </form>

                        <hr>

                        <div>
                            <?= isset($googleButton) ? $googleButton : '' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>