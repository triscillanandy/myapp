<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Form with File Upload</title>
    <style>
        #uploadStatus {
            margin-top: 10px;
            color: green;
        }
        #fileList {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Send Email with Attachment</h1>
    <form id="emailForm" action="<?= base_url('email/send') ?>" method="post" enctype="multipart/form-data">
        <label for="user_id">Sender ID:</label>
        <input type="text" name="user_id" id="user_id" required><br>

        <label for="recipient">Recipient Email:</label>
        <input type="email" name="recipient" id="recipient" required><br>

        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject" required><br>

        <label for="body">Body:</label>
        <textarea name="body" id="body" required></textarea><br>

        <label for="attachments">Attachments:</label>
        <input type="file" id="attachments" multiple><br>
        <button type="button" onclick="uploadFiles()">Upload Files</button>

        <div id="uploadStatus"></div>
        <ul id="fileList"></ul>

        <button type="submit">Send Email</button>
    </form>

    <script>
        function uploadFiles() {
            const files = document.getElementById('attachments').files;
            if (files.length === 0) {
                alert('Please select files to upload.');
                return;
            }

            const formData = new FormData();
            for (const file of files) {
                formData.append('attachments[]', file);
            }

            fetch("<?= base_url('email/upload') ?>", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('uploadStatus').textContent = 'Files uploaded successfully.';
                    displayUploadedFiles(files);
                } else {
                    document.getElementById('uploadStatus').textContent = 'Failed to upload files.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('uploadStatus').textContent = 'Error uploading files.';
            });
        }

        function displayUploadedFiles(files) {
            const fileList = document.getElementById('fileList');
            fileList.innerHTML = '';
            for (const file of files) {
                const listItem = document.createElement('li');
                listItem.textContent = file.name;
                fileList.appendChild(listItem);
            }
        }
    </script>
</body>
</html>
