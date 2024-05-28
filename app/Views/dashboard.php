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
                        <div class="card-title">
                          
                                <?php 
                                $user = null;
                                if (session()->has('logged_user')) {
                                    $user = session()->get('logged_user');
                                } elseif (session()->has('google_user')) {
                                    $user = session()->get('google_user');
                                }
                                ?>
                                <h1>Welcome, <?= $user['firstname'] ?> <?= $user['lastname'] ?></h1>
                       
                           

                        </div>
                        <div class="container">
                            <!-- Additional content can go here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
