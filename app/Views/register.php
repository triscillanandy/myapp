<h3>Register</h3>
<hr>

<?= validation_list_errors() ?>
<form action="<?= base_url('register')?>" method="post">
   
    <div>
        <label for="firstname">First Name</label>
        <input type="text" name="firstname" id="firstname" value="<?= set_value('firstname') ?>">
      
    </div>
    <div>
        <label for="lastname">Last Name</label>
        <input type="text" name="lastname" id="lastname" value="<?= set_value('lastname') ?>">
       
    </div>
    <div>
        <label for="email">Email address</label>
        <input type="text" name="email" id="email" value="<?= set_value('email') ?>">
   
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
      
    </div>
    <div>
        <label for="password_confirm">Confirm Password</label>
        <input type="password" name="password_confirm" id="password_confirm">
   
    </div>
    <div>
        <button type="submit" value="submit">Register</button>
        <a href="<?= base_url('login')?>">Already have an account</a>
    </div>
</form>
