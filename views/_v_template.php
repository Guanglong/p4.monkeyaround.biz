<!DOCTYPE html>
<html>
<head>
	<title><?php if(isset($title)) echo $title; ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	

    <!-- Common CSS/JSS -->
    
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" type="text/css">
    <link rel="stylesheet" href="/css/app.css" type="text/css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>    
    <script type="text/javascript" src="/js/app.js"></script>
    
	<!-- Controller Specific JS/CSS within header section-->
	<?php if(isset($client_files_head)) echo $client_files_head; ?>
	
</head>

<body>	
   <header>  Weight and See 
     <div class="subHeader"> a weight monitor program </div>
   </header> <br>
   
   <div id="navmenu">
    <ul>
      <li><a href="/">HOME</a></li>
      <?php if($user): ?>
        <li><a href="/goals/">Manage Goals</a></li>
        <li><a href="/goals/active">Monitor Active Goal</a></li>  
        <li><a href="/users/logout">EXIT</a></li>
      <?php else: ?>
        <li><a href="javascript:signUp();">Sign Up</a></li>
        <li><a href="javascript:signIn();">Sign In </a></li>
      <?php endif; ?>
    </ul>
  </div> 

    <br>    
    <?php if(isset($content)) echo $content; ?>
    <br/>
     <hr/>
 
     <footer   id="disclosure"> 
     'Weight and See' is brought to you by: Guang Long (Harvard DWA15-p4)<br/>    
      made possible by the instructor Susan Buck and <span id='tfs'>the TFs</span>. --Thanks!  <br><br>
      This site uses <a href="http://asmallorange.com/">A Small Orange </a> to send emails.
      If you think you should get, but not getting emails from monkeyaround.biz, please check your spam filter.
     </footer>
    <script>
      $( "#menu" ).menu();
    </script> 

    <!-- Controller Specific JS after the main body-->
    <?php if(isset($client_files_trailer)) echo $client_files_trailer; ?>  
</body>
</html>