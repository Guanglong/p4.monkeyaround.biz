<?php
   class users_controller extends base_controller {

   public function __construct() {
      parent::__construct();          
      //$this->template->set_global('client_files_head', '<script type="text/javascript" src="/js/users.js"></script>');
   } 

   public function index() {
      echo "Welcome to Weight and See";
   }
  
   public function signInViaAjax() {
      # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
      $_POST = DB::instance(DB_NAME)->sanitize($_POST);

      # Hash submitted password so we can compare it against one in the db
      $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

      $email =$_POST['email'];
      $password = $_POST['password'];

      if (strlen($email) ==0) {
          echo 'E:email requried for sign in';
      } else if(strlen($password)==0) {
          echo 'E:password required for sign in';
      } else {

        # Search the db for this email and password
        # Retrieve the token if it's available
        $q = "SELECT token
              FROM users 
              WHERE email = '".$email."' 
              AND password = '".$password."'";

        $token = DB::instance(DB_NAME)->select_field($q);
        
        # If we didn't find a matching token in the database, it means login failed
        if(!$token) {        
            # Send them back to the login page
           ##Router::redirect("/users/login/error");
          echo 'E:Invalid email/password';

        # But if we did, login succeeded! 
        } else {
           # get the count for the update 
           $loginCountQuery = "SELECT login_Count
                               FROM users 
                               WHERE email = '".$email."' 
                               AND password = '".$password."'";

            $loginCount= DB::instance(DB_NAME)->select_field($loginCountQuery);        
          
            if (is_null($loginCount)) {
               $loginCount = 1;
            } else {
               $loginCount +=1;
            }

            ## +1 feature: update the login count, and last login, ip address login
            $last_login_ip_address = $_SERVER['REMOTE_ADDR'];
            $last_login_machine_name = gethostbyaddr($_SERVER['REMOTE_ADDR']); 

            $updateQuery = "update users 
                               set login_Count = ".$loginCount.
                            ",last_login=".Time::now().
                            ",last_login_ip_address ='".$last_login_ip_address."'".
                            ",last_login_machine_name ='".$last_login_machine_name."'".
                            " where email = '".$email.
                            "'  AND password = '".$password."'";     

            # Do the update
            DB::instance(DB_NAME)->query($updateQuery);        
             /*  
              Store this token in a cookie using setcookie()
              Important Note: *Nothing* else can echo to the page before setcookie is called
              Not even one single white space.
              param 1 = name of the cookie
              param 2 = the value of the cookie
              param 3 = when to expire
              param 4 = the path of the cooke (a single forward slash sets it for the entire domain)        
              */
          
            setcookie("token", $token, strtotime('+1 year'), '/');     
            # Send them to the main page - or whever you want them to go
            echo "S:login Successfully";

            //Router::redirect("/");
          }
      }

   }

   public function signUpViaAjax(){
       # sanitize the parameters
      $_POST = DB::instance(DB_NAME)->sanitize($_POST);

      # get the email address 
      $email = DB::instance(DB_NAME)->sanitize($_POST['email']);      
      $password = DB::instance(DB_NAME)->sanitize($_POST['password']);      
      if (strlen($email) ==0) {
          echo 'E:email empty';
      } else if(strlen($password)==0) {
          echo 'E:password empty';
      } else {
        $q ="select count(email) from users where lower(email)= lower('".$email."')";
        $email_count = DB::instance(DB_NAME)->select_field($q);

       if ($email_count !=0) {
          echo 'E:email is already taken by this site';
       } else {
        $_POST['created']  = Time::now();
        $_POST['modified'] = Time::now();
        $_POST['signup_ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_POST['signup_machine_name'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $_POST['last_login_ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_POST['last_login_machine_name'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);         

        # Encrypt the password  
        $_POST['password'] = sha1(PASSWORD_SALT.$_REQUEST['password']);      
        # Create an encrypted token via their email address and a random string
        $_POST['token'] = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());
        $user_Id = DB::instance(DB_NAME)->insert('users',$_POST);     

        # send a welcome email
        //$this->send_welcome_email($_POST['email'],'Dear Sir or Madam',' ');              
        setcookie("token", $_POST['token'] , strtotime('+1 year'), '/');
        echo 'S:userId:'.$user_Id;  
      }
    }
      
   } 

   public function send_welcome_email($email, $first_name,$last_name) {
      $to[] = Array("name" =>$first_name.' '.$last_name, "email" => $email);           
      # Build a single-dimension array of who this email is coming from
      # note it's using the constants we set in the configuration above)
      $from = Array("name" => APP_NAME, "email" => APP_EMAIL);
      # Subject
      $subject = "Welcome to ".APP_NAME.''.ENV_NAME;
      # You can set the body as just a string of text
      $body = $first_name.", Welcome to Weight N See. This is a message to confirm your registration with ".APP_NAME.
              '. No actions are required and your account has been activated! Please do not reply to this email,'.
              ' as this email address is not actively being monitored.';        
       # Build multi-dimension arrays of name / email pairs for cc / bcc if you want to 
      $cc  = Array("name" => 'Gwong Long', "email" => "gwonglong@fas.harvard.edu");
      $bcc= Array("name" => 'Gwong Long', "email" => "gwonglong2013@gmail.com");

      # With everything set, send the email      
      ##  in local env, use Email feature/gmail to send email out
      if (!IN_PRODUCTION) { 
        $email = Email::send($to, $from, $subject, $body, true, $cc, $bcc);  
      } else {
      
        ## production enviroment, use small orange email account to send emails
        $toProduction = $email.',guang.long@monkeyaround.biz';  

        $headers = 'From: guang.long@monkeyaround.biz'."\r\n".
                   'Reply-To: guang.long@monkeyaround.biz'."\r\n".
                   'X-Mailer: PHP/' . phpversion(); 

         mail($toProduction, $subject, $body, $headers);
        }  
   } 

    public function logout() {
       # Generate and save a new token for next login
       $new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());

       # Create the data array we'll use with the update method
       # In this case, we're only updating one field, so our array only has one entry
       $data = Array("token" => $new_token);

       # Do the update
       DB::instance(DB_NAME)->update("users", $data, "WHERE token = '".$this->user->token."'");

       # Delete their token cookie by setting it to a date in the past - effectively logging them out
       setcookie("token", "", strtotime('-1 year'), '/');

       # Send them back to the main index.
       Router::redirect("/");
    }

   
} # end of the class