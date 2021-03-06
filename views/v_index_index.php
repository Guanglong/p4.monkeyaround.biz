<section>Welcome to <?=APP_NAME?>&reg;<?php if($user) echo $user->first_name."!"; ?></section>
<div id="signUpDiv" title="Sign Up"></div>
<div id="signInDiv" title="Sign In"></div>
<div id="mainContent"> </div>
<section>
	We all know that healthy eating and exercise is important to keep weight under control.
	But people often do not realize that a tool to monitor the progress is vital as well.
	"Weight and See"&reg; now provides a platform for people to monitor weight loss progress.
	<br>To use 'Weight and See' is as easy as 1-2-3:
	<ol>
		<li> Sign Up / Sign In Using Email</li>
		<li> Set a Goal </li>
		<li> Record Your Weight and See Your Progress </li>
    </ol>		
	<?php if(!$user) {
			echo '<button id="signUp" type="button" >Sign Up </button>';
			echo '&nbsp;';
			echo '<button id="signIn" type="button">Sign In</button>';
		}	
	?>
	
</section>