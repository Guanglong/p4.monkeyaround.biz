<section>Password Reset</section>
<br/>
<form method='POST' action='/users/p_reset_password'>
    <div>Email:    <input type='text' maxlength="50" name='email' required=''>   </div>
    <br/><br/>
    <div>Password:
    <input type='password' maxlength="50" name='temp_password' required=''>
    </div>

    <br/><br/>

    <?php if(isset($error)): ?>
        <div class='error'>
            Password could not be reset:<?= $error ?>.
        </div>
        <br>
    <?php endif; ?>
    <?php if(isset($emailSent)): ?>
        <div class='error'>
            A confirmation email has been set to your email account, you need to get the email before you can continue to reset your password.
            Please ensure your email filter does not block emails from monkeyaround.biz 
        </div>
        <br>
    <?php endif; ?>

    <div><button >Reset</button></div>
</form>