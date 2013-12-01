<?php

    class goals_controller extends base_controller {

    public function __construct() {
        parent::__construct();

        # Make sure user is logged in if they want to use anything in this controller
        if(!$this->user) {
           die("Members only. <a href='/users/home'>Home</a>");
        }
    }  

    // for goals index page, to get all goals, sort by goal_id
    // the last one is the active one.
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

    // to creat a new goal via ajax
    // it return E: ...  --> error  
    //           S: ...  --> Successful
    public function createNewGoalViaAjax() {
        # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
        $_POST = DB::instance(DB_NAME)->sanitize($_POST);        

        $start_date =$_POST['start_date'];      

        $goal_days =$_POST['goal_days'];
        $start_value =$_POST['start_value'];
        $target_value =$_POST['target_value'];
        
        $_POST['created']  = Time::now();
        $_POST['modified'] = Time::now();
        $_POST['active_flag'] = 'Y';      
        $_POST['user_id']  = $this->user->user_id;
        
        // validate the data
        // upate previous goals to inactive
        $inactiveUpdate = "update goals set active_flag = 'N' where user_id =".$_POST['user_id'];
        DB::instance(DB_NAME)->query($inactiveUpdate); 

        // save current goal
        $goal_Id = DB::instance(DB_NAME)->insert('goals',$_POST); 

        // update the start date to the date format
        $updateStartDate = "update goals set start_date = str_to_date('".$start_date."','%Y-%m-%d') where goal_id = ".$goal_Id;
        DB::instance(DB_NAME)->query($updateStartDate);   
        echo 'S:new goal saved successfully!';
    } 

    // for displaying the active goal page
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
                date_format(ADDDATE(goals.start_date,INTERVAL progress_day DAY),'%Y-%m-%d') as progress_date,
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

    // to creat a new progress via ajax
    // it return E: ...  --> error  
    //           S: ...  --> Successful
    public function saveNewProgressViaAjax() {
        # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
        $_POST = DB::instance(DB_NAME)->sanitize($_POST); 

        $_POST['created']  = Time::now();
        $_POST['modified'] = Time::now();

        $progress_Id = DB::instance(DB_NAME)->insert('progress',$_POST);       
  
        echo "S: Save new progressSuccessfully".$progress_Id;    
   } 
}