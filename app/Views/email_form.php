<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email</title>
</head>
<body>
    <?php if (session()->getFlashdata('success')): ?>
        <p><?= esc(session()->getFlashdata('success')) ?></p>
    <?php endif; ?>

    <?php if (isset($validation)): ?>
        <div>
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>
    
    <form action="<?= base_url('/send2') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= set_value('email') ?>" required>
        <br>
        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" value="<?= set_value('subject') ?>" required>
        <br>
        <label for="message">Message:</label>
        <textarea id="message" name="message" required><?= set_value('message') ?></textarea>
        <br>
        <label for="attachments">Attachments:</label>
        <input type="file" id="attachs" name="attachs[]" multiple>
        <br>
        <button type="submit">Send Email</button>
    </form>
</body>
</html>
