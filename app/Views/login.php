<h3>Register</h3>
<hr>

<?= validation_list_errors() ?>
<form action="<?= base_url('login')?>" method="post">
   
   
    <div>
        <label for="email">Email address</label>
        <input type="text" name="email" id="email" value="<?= set_value('email') ?>">
   
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
      
    </div>
 
    <div>
        <button type="submit" value="submit">login</button>
        <a href="<?= base_url('register')?>">Dont have an  account</a>
    </div>
</form>
