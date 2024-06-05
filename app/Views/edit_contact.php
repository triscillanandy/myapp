<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>

<?php 
    if (session()->has('logged_user')) {
        $user = session()->get('logged_user');
    }
?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Edit Contact</div>
                    <div class="card-body">
                        <form action="<?= base_url('contacts/update/' . $contact['id']) ?>" method="post">
                        <?= csrf_field(); ?>
                         <!-- Display Validation Errors -->
                   
                         <?php if (session()->has('error')) : ?>
    <div class="alert alert-danger" role="alert">
        <?= session()->get('error') ?>
    </div>
<?php endif; ?>

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= $contact['name'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= $contact['email'] ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
