<?php
    class goals_controller extends base_controller {

    public function __construct() {
        parent::__construct();

        # Make sure user is logged in if they want to use anything in this controller
        if(!$this->user) {
           die("Members only. <a href='/users/home'>Home</a>");
        }
    }

    public function add($status=NULL) {

        # Setup view
        $this->template->content = View::instance('v_posts_add');
        $this->template->title   = "New Post";
        $this->template->content->status = $status;
        # Render template
        echo $this->template;

    }

    public function p_add() {

        # sanitize the parameters
        $_POST = DB::instance(DB_NAME)->sanitize($_POST);
        
        # Associate this post with this user
        $_POST['user_id']  = $this->user->user_id;

        # Unix timestamp of when this post was created / modified
        $_POST['created']  = Time::now();
        $_POST['modified'] = Time::now();

        # Insert
        # Note we didn't have to sanitize any of the $_POST data because we're using the insert method which does it for us
        DB::instance(DB_NAME)->insert('posts', $_POST);

        # Quick and dirty feedback
        #echo "Your post has been added. <a href='/posts/add'>Add another</a>";
        # forward to posts/add to render the add page
        Router::redirect("/posts/add/status");
      }

    public function index() {

        # Set up the View
        $this->template->content = View::instance('v_goals_index');
        $this->template->title   = "Add/View Goals";

        # Query
        $q = "SELECT 
                goals.goal_id,
                date_format(goals.start_date,'%m/%d/%Y') as start_date,
                goals.goal_days ,
                goals.start_value,
                goals.target_value,
                goals.created,
                goals.active_flag
            FROM goals            
            INNER JOIN users 
                ON goals.user_id = users.user_id
            WHERE users.deleted_ind ='N'
               AND users.user_id = ".$this->user->user_id.
            " ORDER BY goals.goal_id asc ";

        # Run the query, store the results in the variable $posts
        $goals = DB::instance(DB_NAME)->select_rows($q);

        # Pass data to the View
        $this->template->content->goals = $goals;

        // include at the end of the html        
        $client_files_trailer = Array(            
            '/js/goals.js'          
         );

        $this->template->client_files_trailer = Utils::load_client_files($client_files_trailer);

        # Render the View
        echo $this->template;
    }

    public function createNewGoalViaAjax() {
      # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
      $_REQUEST = DB::instance(DB_NAME)->sanitize($_REQUEST);        

      $start_date =$_REQUEST['start_date'];      

      $goal_days =$_REQUEST['goal_days'];
      $start_value =$_REQUEST['start_value'];
      $target_value =$_REQUEST['target_value'];
      
      $_REQUEST['created']  = Time::now();
      $_REQUEST['modified'] = Time::now();
      $_REQUEST['active_flag'] = 'Y';      
      $_REQUEST['user_id']  = $this->user->user_id;
      
      // validate the data
      // upate previous goals to inactive
      $inactiveUpdate = "update goals set active_flag = 'N' where user_id =".$_REQUEST['user_id'];
      DB::instance(DB_NAME)->query($inactiveUpdate); 

      // save current goal
      $goal_Id = DB::instance(DB_NAME)->insert('goals',$_REQUEST); 

      // update the start date to the date format
      $updateStartDate = "update goals set start_date = str_to_date('".$start_date."','%Y-%m-%d') where goal_id = ".$goal_Id;
      DB::instance(DB_NAME)->query($updateStartDate);   
      echo 'S:new goal saved successfully!';
    } 

    public function active() {

        # Set up the View
        $this->template->content = View::instance('v_goals_active');
        $this->template->title   = "Active Goal";

        $client_files_head = Array( 
             "https://www.google.com/jsapi",
             "/css/active.js"
        );

        $this->template->client_files_head = Utils::load_client_files($client_files_head);

        # Query active goal
        $activeGoalQuery = " SELECT goals.*
                               FROM goals 
                         INNER JOIN users 
                                 ON goals.user_id = users.user_id 
                              WHERE  users.deleted_ind ='N' 
                                AND  goals.active_flag= 'Y'
                                AND users.user_id = ".$this->user->user_id.
                            " LIMIT 0,1";
        $activeGoal = DB::instance(DB_NAME)->select_rows($activeGoalQuery);

        # Pass data to the View
        $this->template->content->activeGoal = $activeGoal;

        $progressQuery = "SELECT 
                goals.goal_id,
                date_format(goals.start_date,'%m/%d/%Y') as start_date,
                progress.progress_id,
                progress.progress_day,
                date_format(ADDDATE(goals.start_date,INTERVAL progress_day DAY),'%m/%d/%Y') as progress_date,
                goals.goal_days ,
                progress.progress_value                
            FROM progress   
            INNER JOIN goals 
               ON goals.goal_id = progress.goal_id                        
            INNER JOIN users 
                ON goals.user_id = users.user_id
            WHERE 1=1
               AND users.deleted_ind ='N'
               AND goals.active_flag= 'Y'
               AND users.user_id = ".$this->user->user_id.
            " ORDER BY progress.progress_day asc ";

        # Run the query, store the results in the variable $posts
        $progress = DB::instance(DB_NAME)->select_rows($progressQuery);

        # Pass data to the View
        $this->template->content->progress = $progress;

        // include at the end of the html        
        $client_files_trailer = Array(             
            '/js/active.js'          
         );

        $this->template->client_files_trailer = Utils::load_client_files($client_files_trailer);

        # Render the View
        echo $this->template;
    }

    public function saveNewProgressViaAjax() {
        # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
        $_REQUEST = DB::instance(DB_NAME)->sanitize($_REQUEST); 

        $_REQUEST['created']  = Time::now();
        $_REQUEST['modified'] = Time::now();

        $progress_Id = DB::instance(DB_NAME)->insert('progress',$_REQUEST);       
  
        echo "S: Save new progressSuccessfully".$progress_Id;    
   } 
}