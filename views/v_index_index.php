<section>Welcome to <?=APP_NAME?><?php if($user) echo '.'.$user->first_name."!"; ?></section>
<div id="signUpDiv" title="Sign Up"></div>
<div id="signInDiv" title="Sign In"></div>
<div id="mainContent"> </div>
<div>
	We all know that healthy eating and exercises are important to keep weight under controll.
	But people often do not realize that a tool to monitor the progress is vital as well.
	"monkeyaround.biz" now provides a platform for people to monitor weight progress.
	You set a goal, and log your progress, 'Weight and See' will be here to keep you accompanied
	<?php if(!$user) echo "<br>To Start, you will need an email address to <a href='javascript:signUp();'>Sign Up</a>"; ?>
	<?php if(!$user) echo "<br> If You have signed up, you can  <a href='javascript:signIn();'>Sign In</a> here"; ?>
</div>