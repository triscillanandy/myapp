<!-- email_form.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Form</title>
</head>
<body>
    <h1>Email Form</h1>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('/email/enqueueEmails') ?>"  method="post">
        <label for="subject">Subject:</label><br>
        <input type="text" id="subject" name="subject"><br>

        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4" cols="50"></textarea><br>

        <button type="submit">Send Emails</button>
    </form>
</body>
</html>
