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
                 
                    <div class="card-title">
                    
                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Recipient</th>
                                        <th>Subject</th>
                                        <th>Body</th>
                                      
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($emails) && !empty($emails)): ?>
                                        <?php foreach ($emails as $email): ?>
                                            <tr>
                                                <td><?= $email['id'] ?></td>
                                                <td><?= $email['recipient'] ?></td>
                                                <td><?= $email['subject'] ?></td>
                                                <td><?= $email['body'] ?></td>
                                               
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No contacts found</td>
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
  


</body>
</html>
