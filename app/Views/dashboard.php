<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <div class="row d-flex align-items-center justify-content-center" style="min-height: 600px;">
            <div class="card">
                <div class="card-content">
                    <div class="card-body shadow">
                      
                        <div class="card-title">Hello <?= session()->get('firstname') ?></div>
                        <div class="card-title">email:  <?= session()->get('email') ?></div>
                        <a href="<?= base_url('logout')?>">Logout</a>
                       <div class="container">

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>