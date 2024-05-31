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
    <div class="container">
        <div class="row d-flex align-items-center justify-content-center" style="min-height: 600px;">
            <div class="card">
                <div class="card-content">
                    <div class="card-body shadow">
                        <div class="card-title">
                        <?php 
                       if (session()->has('logged_user')) {
                        $user = session()->get('logged_user');
                       }
                            ?>
                          <h1>Welcome, <?= $user['firstname'] ?> <?= $user['lastname'] ?></h1>
                     
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            <?php if (isset($contacts) && !empty($contacts)): ?>
                                <?php foreach ($contacts as $contact): ?>
                                    <tr>
                                        <td><?= $contact['id'] ?></td>
                                        <td><?= $contact['name'] ?></td>
                                        <td><?= $contact['email'] ?></td>
                                        <td><a href="/edit/<?= $contact['id'] ?>" class="btn btn-primary">Edit</a></td>
                                        <td><a href="/delete/<?= $contact['id'] ?>" class="btn btn-danger">Delete</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No contacts found</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    <div>
                        <a href="/logout" class="btn btn-secondary">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
