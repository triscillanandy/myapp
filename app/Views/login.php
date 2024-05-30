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
                        <div class="card-title">Login</div>
                        <form id="loginForm" method="POST">

                            <div class="form-group">
                                <input type="email" placeholder="Enter email..." class="form-control" name="email" required>
                            </div>
                            <div class="form-group">
                                <input type="password" placeholder="Enter password..." class="form-control" name="password" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-secondary shadow-sm">Login</button>
                                <a href="<?= base_url('register') ?>">Do not have an account?</a><br>
                                <a href="<?= base_url('forgotpassword') ?>">Forgot password</a>
                            </div>
                        </form>

                        <hr>

                        <div>
                            <button id="googleLoginButton" class="btn btn-primary">Login with Google</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('/users/login') ?>',
                    data: formData,
                    success: function(response) {
                        localStorage.setItem('token', response.token);
                        window.location.href = '<?= base_url('dashboard') ?>';
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            });

            $('#googleLoginButton').click(function() {
                // Redirect to Google login page
             
            });
        });
    </script>
</body>

</html>
