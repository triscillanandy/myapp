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
                            <h1>Welcome, Users</h1>
                        </div>
                        <div class="container">
                            <!-- User details will be dynamically inserted here -->
                            <div id="userDetails"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var token = localStorage.getItem('token');

            if (token) {
                $.ajaxSetup({
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                $.ajax({
                    type: 'GET',
                    url: '<?= base_url('dashboard') ?>', // Endpoint to fetch user details
                    success: function(response) {
                        // Assuming the response is an array of user objects
                        // Loop through the users and display their details
                        var userDetailsHtml = '';
                        $.each(response, function(index, user) {
                            userDetailsHtml += '<p>' + user.firstname + ' ' + user.lastname + '</p>';
                        });
                        $('#userDetails').html(userDetailsHtml);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                window.location.href = '<?= base_url('login') ?>';
            }
        });
    </script>
</body>

</html>
