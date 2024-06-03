<!DOCTYPE html>
<!-- Coding by CodingLab | www.codinglabweb.com-->
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Responsive Signup Form </title>

        
             <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('css/restyle.css'); ?>">
                
        <!-- Boxicons CSS -->
        <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
                        
    </head>
    <body>
        <section class="container forms">
          

            <!-- Signup Form -->

            <div class="form signup">
                <div class="form-content">
                    <header>Signup</header>

                    <?= validation_list_errors() ?>
                    <?php if (isset($_SESSION['success'])) { ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php } ?>

                     <form action="<?= base_url('register') ?>" method="post">
                            <?= csrf_field(); ?>

                        <div class="field input-field">
                           
                            <input type="text" class="input"  placeholder="Firstname"  name="firstname" value="<?= set_value('firstname') ?>" required>
                        </div>

                        <div class="field input-field">
                            
                            <input type="text" class="input"  placeholder="Lastname" name="lastname"  value="<?= set_value('lastname') ?>" required>
                        </div>
                        <div class="field input-field">
                          
                            <input type="email" class="input"  placeholder="Email"   name="email"  value="<?= set_value('email') ?>" required>
                        </div>
                        <div class="field input-field">
                            <input type="password" placeholder="Create password" class="password" name="password"  required>
                        </div>

                        <div class="field input-field">
                            <input type="password" placeholder="Confirm password" class="password"  name="password_confirm"  required>
                            <i class='bx bx-hide eye-icon'></i>
                        </div>

                        <div class="field button-field">
                            <button type="submit">Signup</button>
                        </div>
                    </form>

                    <div class="form-link">
                        <span>Already have an account? <a href="<?= base_url('login') ?>"  class="link login-link">Login</a></span>
                    </div>
                </div>

                <div class="line"></div>


             
                <div class="media-options">
                            <a href="" class="field google">
                                <img src="<?php echo base_url('images/google.png'); ?>" alt="" class="google-img">
                                <span>SignUp with Google</span>
                            </a>
                        </div>

            </div>
        </section>

        <!-- JavaScript -->
        <script>

      pwShowHide = document.querySelectorAll(".eye-icon"),
      links = document.querySelectorAll(".link");

pwShowHide.forEach(eyeIcon => {
    eyeIcon.addEventListener("click", () => {
        let pwFields = eyeIcon.parentElement.parentElement.querySelectorAll(".password");
        
        pwFields.forEach(password => {
            if(password.type === "password"){
                password.type = "text";
                eyeIcon.classList.replace("bx-hide", "bx-show");
                return;
            }
            password.type = "password";
            eyeIcon.classList.replace("bx-show", "bx-hide");
        })
        
    })
})      
    
        </script>
    </body>
</html>