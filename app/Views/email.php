<!-- email_form.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Form</title>
</head>
<body>
    <h1>Send Email</h1>
    <form action="<?= base_url('email/send') ?>" method="post" enctype="multipart/form-data">
        <label for="user_id">Sender ID:</label>
        <input type="text" name="user_id" id="user_id">
        <br>
        <label for="recipient">Recipient Email:</label>
        <input type="email" name="recipient" id="recipient">
        <br>
        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject">
        <br>
        <label for="body">Body:</label>
        <textarea name="body" id="body" rows="4"></textarea>
        <br>
        <label for="attachments">Attachments:</label>
        <input type="file" name="attachments[]" id="attachments" multiple>
        <br>
        <input type="submit" value="Send Email">
    </form>
</body>
</html>
