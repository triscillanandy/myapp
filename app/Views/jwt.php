<!-- app/Views/jwt_view.php -->

<!DOCTYPE html>
<html>
<head>
    <title>JWT Token</title>
</head>
<body>
    <h1>Generated JWT Token</h1>
    <p><?php echo $jwt; ?></p>

    <h1>Decoded JWT</h1>
    <pre><?php print_r($decoded); ?></pre>
</body>
</html>
