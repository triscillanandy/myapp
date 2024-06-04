<!DOCTYPE html>
<html>
<head>
  <title>Verify OTP</title>
</head>
<body>
  <h2>Verify OTP</h2>

  <?php if(session()->get('error')): ?>
    <div style="color: red;">
      <?= session()->get('error') ?>
    </div>
  <?php endif; ?>

  <form action="<?= base_url('/verifyotp') ?>" method="post">
    <label for="otp">Enter OTP:</label>
    <input type="text" id="otp" name="otp" required>
    <button type="submit">Verify</button>
  </form>
</body>
</html>
