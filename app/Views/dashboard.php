<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    
</head>
<body>
<?php 
    if (session()->has('logged_user')) {
        $user = session()->get('logged_user');
    }
?>
<div class="container">
    <div class="row d-flex align-items-center justify-content-center" style="min-height: 600px;">
        <div class="card">
            <div class="card-content">
                <div class="card-body shadow">
                    <!-- Add Contact Button -->
                    <div class="row justify-content-center mb-3">
                        <button class="btn btn-primary" id="addContactBtn">Add Contact</button>
                    </div>

                    <div class="card-title">
                        <h1>Welcome, <?= $user['firstname'] ?> <?= $user['lastname'] ?></h1>
                        <?php if (session()->has('success')): ?>
                        <div class="alert alert-success">
                            <?= session('success') ?>
                        </div>
                    <?php endif; ?>
                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($contacts) && !empty($contacts)): ?>
                                        <?php foreach ($contacts as $contact): ?>
                                            <tr>
                                                <td><?= $contact['name'] ?></td>
                                                <td><?= $contact['email'] ?></td>
                                                <td><a href="<?= base_url('contacts/edit/' . $contact['id']) ?>" class="btn btn-primary">Edit</a></td>
                                                <td><a href="<?= base_url('contacts/delete/' . $contact['id']) ?>" class="btn btn-danger">Delete</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No contacts found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade show" id="addContactModal" tabindex="-1" role="dialog" aria-labelledby="addContactModalLabel" aria-hidden="true" style="display: <?= session()->has('fail') ? 'block' : 'none' ?>;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addContactModalLabel">Add Contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Display Validation Errors -->
                    <?php if (session()->has('fail')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session('fail') as $field => $error): ?>
                                <p><?= $error ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <!-- Form -->
                    <form id="addContactForm" action="<?= base_url('/contacts/create') ?>" method="POST">
                        <?= csrf_field(); ?>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>">
                        </div>
                        <input type="hidden" name="user_id" value="<?= session()->get('logged_user')['id'] ?>">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function () {
    $('#addContactBtn').click(function () {
        $('#addContactModal').modal('show');
    });

    
});
</script>

</body>
</html>
