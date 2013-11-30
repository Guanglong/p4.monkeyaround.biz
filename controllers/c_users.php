<?php
   class users_controller extends base_controller {

   public function __construct() {
      parent::__construct();          
      //$this->template->set_global('client_files_head', '<script type="text/javascript" src="/js/users.js"></script>');
   } 

   public function index() {
      echo "Welcome to Monkey blog";
   }

   public function signup($error=NULL) {        
      # Setup view
      $this->template->content = View::instance('v_users_signup');
      $this->template->title   = "Sign Up";
      # Create an array of 1 or many client files to be included in the head
      $client_files_head = Array(
        '/css/widgets.css',
        '/css/signup.css',
        '/js/signup.js'
      );

      # Use load_client_files to generate the links from the above array
      $this->template->client_files_head = Utils::load_client_files($client_files_head);
      if (isset($error)) {
         if ($error =='email_password') {
            $error_message = "email and password combination";
         } else if ($error =='email') {
            $error_message =" Email address, It is alreday used by Monkey blog";
         } else {
            $error_message ="";
         }

         $this->template->content->error = $error_message;
      }
      # Render template
      echo $this->template;            
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
        //$this->send_welcome_email($_POST['email'],$_POST['first_name'],$_POST['last_name']);              
        setcookie("token", $_POST['token'] , strtotime('+1 year'), '/');
        echo 'S:userId:'.$user_Id;  
      }
    }
      
   }


   public function p_signup() {    

      # sanitize the parameters
      $_POST = DB::instance(DB_NAME)->sanitize($_POST);

      # get the email address 
      $email = DB::instance(DB_NAME)->sanitize($_POST['email']);
      $q ="select count(email) from users where lower(email)= lower('".$email."')";
      $email_count = DB::instance(DB_NAME)->select_field($q);  
         
      if ($email_count>0) { 
         Router::redirect('/users/signup/email');
      }  else {
         # More data we want stored with the user
         $_POST['created']  = Time::now();
         $_POST['modified'] = Time::now();
         $_POST['signup_ip_address'] = $_SERVER['REMOTE_ADDR'];
         $_POST['signup_machine_name'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
         $_POST['last_login_ip_address'] = $_SERVER['REMOTE_ADDR'];
         $_POST['last_login_machine_name'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);         

         # Encrypt the password  
         $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);      
         # Create an encrypted token via their email address and a random string
         $_POST['token'] = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());
         $user_Id = DB::instance(DB_NAME)->insert('users',$_POST);     
        
         # send a welcome email
         //$this->send_welcome_email($_POST['email'],$_POST['first_name'],$_POST['last_name']);              
         setcookie("token", $_POST['token'] , strtotime('+1 year'), '/');     
          # Send them to the main page - or whever you want them to go
         Router::redirect("/");
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
      $body = "Hi ".$first_name.", Welcome to Monkey blog. This is a message to confirm your registration with ".APP_NAME.
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

   public function reset_password($error=NULL) {
      # Setup view
      $this->template->content = View::instance('v_users_reset_password');      
      $this->template->title   = "Reset Password";
      # Pass data to the view if any      
      if (isset($error)) {
         if ($error == "empty_password") {
           $error_message ="passward is empty";
         } else if($error == "wrong_email") {
           $error_message ="are you sure you signed up with monkey blog?";
         } else {
           $error_message ="The password reset feature is on vacation, please revisit later";
         }
         
         $this->template->content->error=$error_message;
     }    
      # Render template
      echo $this->template;
   } 

   public function p_reset_password() {
      # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
      $_POST = DB::instance(DB_NAME)->sanitize($_POST);   

      # check if passowrd is empty
      if ( strlen($_POST['temp_password'])==0 ) {   
         Router::redirect('/users/reset_password/empty_password');
      }

      #check for email exist in users table
      $q = "select * from users where email ='".$_POST['email']."'";
      $userRow=  DB::instance(DB_NAME)->select_row($q);
      if (!isset($userRow['user_id'])) { // no user found monkey blog db
        Router::redirect('/users/reset_password/wrong_email');
      } else {
         
         $_POST['temp_password'] = sha1(PASSWORD_SALT.$_POST['temp_password']);
         $u =" update users set temp_password = '".$_POST['temp_password']."', modified = ".Time::now()." where email ='".$userRow['email']."'";
         DB::instance(DB_NAME)->query($u);

         ## send email to that email address and sent to myself too
         ## please note that for security reason, only hashed password is set, not the original password
         $this->send_reset_password_email($userRow['email'] ,$userRow['first_name'],$userRow['last_name'],$_POST['temp_password'] );
                  
         # Setup view
         $this->template->content = View::instance('v_users_reset_password');      
         $this->template->title   = "Reset Password".$userRow['email'].$userRow['first_name'].$userRow['last_name'];
         # Pass data to the view if any      
         $this->template->content->emailSent="Y";
         # Render template
         echo $this->template;
      }
   }

   public function send_reset_password_email($email, $first_name,$last_name, $temp_password) {  

      $to[] = Array("name" =>$first_name.' '.$last_name, "email" => $email);           
      # Build a single-dimension array of who this email is coming from
      # note it's using the constants we set in the configuration above)
      $from = Array("name" => APP_NAME, "email" => APP_EMAIL);
      # Subject
      $subject = "Password Reset ".APP_NAME.' '.ENV_NAME;
      # reset_url 
      $reset_url = "http://".DOMAIN_NAME."/users/confirm_password_reset/".$email."/".$temp_password;
      # You can set the body as just a string of text
      $body = "Hi ".$first_name.". This is to confirm that you requested to reset your password for ".APP_NAME.
              '. Your action is required! Copy and paste the link in a browser to complete the password reset: '.$reset_url.
              ". Please do not reply to this email, as this email address is not actively being monitored";        
      # Build multi-dimension arrays of name / email pairs for cc / bcc if you want to 
      #$bcc  = Array("name" => 'Gwong Long', "email" => "gwonglong@fas.harvard.edu");
      $cc= Array("name" => 'Gwong Long', "email" => "gwonglong2013@gmail.com");

      ##  in local env, use Email feature/gmail to send email out
      if (!IN_PRODUCTION) { 
         # With everything set, send the email
         $email = Email::send($to, $from, $subject, $body, true, '', '');  
      } else {
      
      ## production enviroment, use small orange email account to send emails
      $toProduction = $email.',guang.long@monkeyaround.biz';  

      $headers = 'From: guang.long@monkeyaround.biz'."\r\n".
                 'Reply-To: guang.long@monkeyaround.biz'."\r\n".
                 'X-Mailer: PHP/' . phpversion(); 

       mail($toProduction, $subject, $body, $headers);
     }

   }

   public function confirm_password_reset($email, $temp_password) {

      if ( !isset($email) || !isset($temp_password)) {  // wrong email came in, redirect it to signup page, with email error displayed
         Router::redirect('/users/signup/email');  
      } else {
         
         $email =  DB::instance(DB_NAME)->sanitize($email);
         $temp_password =  DB::instance(DB_NAME)->sanitize($temp_password);
         
         ## find out if the given email, temp password combination is valid or not
         $q = " select  * from users where email = '".$email."' and temp_password = '".$temp_password."'";
         $userRow = DB::instance(DB_NAME)->select_row($q);

         ## invalid email address passed in, redirect it to signup page with error
         if (!isset($userRow['user_id'])) { 
            Router::redirect('/users/signup/email_password'); 
         }  

         $new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());
         $u = " update users set password ='".$temp_password."', temp_password =null, modified =".
              Time::now().", token ='".$new_token."' where user_id = ".$userRow['user_id'];

         $userRow = DB::instance(DB_NAME)->query($u);  
         # Delete their token cookie by setting it to a date in the past - effectively logging them out
         setcookie("token", "", strtotime('-1 year'), '/');
         ## forward to login page
         Router::redirect('/users/login/reset_password'); 
      }
   }
   
   public function login($status= NULL) {

      # Setup view
      $this->template->content = View::instance('v_users_login');      
      $this->template->title   = "Login";
      # Pass data to the view
      if (isset($status)) {  // if it has a value
         if  ($status =="reset_password") {$this->template->content->reset_password = 'Y'; }
            else {    $this->template->content->error = $status; }
       }  
       # Render template
       echo $this->template;
   }

   public function p_login() { 
    
      # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
      $_POST = DB::instance(DB_NAME)->sanitize($_POST);

      # Hash submitted password so we can compare it against one in the db
      $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

      # Search the db for this email and password
      # Retrieve the token if it's available
      $q = "SELECT token
            FROM users 
            WHERE email = '".$_POST['email']."' 
            AND password = '".$_POST['password']."'";

      $token = DB::instance(DB_NAME)->select_field($q);
      
      # If we didn't find a matching token in the database, it means login failed
      if(!$token) {        
          # Send them back to the login page
         Router::redirect("/users/login/error");

      # But if we did, login succeeded! 
      } else {
         # get the count for the update 
         $loginCountQuery = "SELECT login_Count
                             FROM users 
                             WHERE email = '".$_POST['email']."' 
                             AND password = '".$_POST['password']."'";

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
                      " where email = '".
                        $_POST['email'].
                      "'  AND password = '".
                        $_POST['password']."'";     

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
      Router::redirect("/");
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

    public function profile($user_name = NULL) {
       # If user is blank, they're not logged in; redirect them to the login page
       if(!$this->user) {
          Router::redirect('/users/login');
       }
   
   
       # Setup view
       $this->template->content = View::instance('v_users_profile');

       # Set page title
       $this->template->title = "Profile";
 
       # Create an array of 1 or many client files to be included in the head
       $client_files_head = Array(
        '/css/widgets.css',
        '/css/profile.css',
        '/js/profile.js'
        );

       # Use load_client_files to generate the links from the above array
       $this->template->client_files_head = Utils::load_client_files($client_files_head);   

       # Pass information to the view fragment
       $this->template->content->user_name = $user_name;

       # Render View
       echo $this->template;
    }
  
   public function switchVisibility($email=NULL) {       
      $token = DB::instance(DB_NAME)->sanitize($_REQUEST['token']);      
      $q = "select deleted_ind from users  where email = '".$email."' and token ='" .$token."'";            
      $original_ind=  DB::instance(DB_NAME)->select_field($q);    
      $newInd ='N';
   
      if (!isset($original_ind )) { $newInd ='Y';}
      else if ($original_ind =='N') { $newInd ='Y';}
      else { $newInd ='N';}        
      
      ## update users table
      $u = "update  users  set deleted_ind  = '".$newInd."' , modified =".Time::now()." where email = '".$email."' and token ='" .$token."'";        
      DB::instance(DB_NAME)->query($u);
      
      echo $newInd;
   }
  
   public function checkEmail($email =NULL) {
      $q = "select count(email) as email_count from users where lower(email) = lower('".$email."')";   
      $emailCount =  DB::instance(DB_NAME)->select_field($q);  
      echo $emailCount;
  }
} # end of the class