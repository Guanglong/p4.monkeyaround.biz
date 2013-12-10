
<!-- display the header based on the count of active-goal and progress count -->
<?php 
    echo '<section>Monitor Progress for the Active Goal</section><br>';
    if (count($activeGoal)>=1) { 
        if (count($progress)==0) { 
          echo '<section>You have not made any progress yet!</section>';  
        }   
        else {
          echo '<section>The progress you made for the active goal:</section>';
        }
    }  
?>
<br/>
<?php 
    $rowsData=""; // init 
    if (count($activeGoal)>=1) {
        // create some hidden fields, which will be used for displaying processDialog
        echo "<input type='hidden' id='start_date' name='start_date' value='".$activeGoal[0]['start_date']."' >";
        echo "<input type='hidden' id='start_value' name='start_value' value='".$activeGoal[0]['start_value']."' >";
        echo "<input type='hidden' id='target_value' name='target_value' value='".$activeGoal[0]['target_value']."' >";
        echo "<input type='hidden' id='goal_days' name='goal_days' value='".$activeGoal[0]['goal_days']."' >";
        echo "<input type='hidden' id='goal_id' name='goal_id' value='".$activeGoal[0]['goal_id']."' >";
         // add the day 0's row for display purpose!
        $rowsData='[0,"'.$activeGoal[0]["start_date"].'",'.$activeGoal[0]['start_value'].'],';
    }

    if (count($userData)>=1) {
        echo "<input type='hidden' id='ip_address' name='ip_address' value='".$userData[0]['signup_ip_address']."' >";
        echo "<input type='hidden' id='last_name' name='last_name' value='".$userData[0]['last_name']."' >";  
    }
?>
<?php $maxProgressDayEntered =0;  ?>
<?php foreach($progress as $progres): ?>  
<?php 
    // get the rows, which is used for displaying google chart/table
    $rowData = "[". $progres['progress_day'].",\"".
                    $progres['progress_date']."\",".
                    $progres['progress_value']."]";

    $rowsData =$rowsData.$rowData.",";

    if ($progres['progress_day']>$maxProgressDayEntered) {
        $maxProgressDayEntered =$progres['progress_day'];
    }
?>
<?php endforeach; ?>
<?php echo "<input type='hidden' id='maxProgressDayEntered' name='maxProgressDayEntered' value='".$maxProgressDayEntered."' >"; ?>
<?php echo "<input type='hidden' id='rowsData' name='rowsData' value='".rtrim($rowsData,',')."' >"; ?>

<div id="chart_div" ></div>
<div id="table_div"></div>
<section>

  <?php 

    if (count($activeGoal)==0) { 
        echo 'You cannot log your progress, because you do not have an active goal!'.
        '<br> You can create one <a href="/goals"> here </a>';}
    else { 
        echo '<br>';
        if ($maxProgressDayEntered >=$activeGoal[0]['goal_days']) {
          echo 'The number of days entered has reached goal limit. No new progress can be made to this goal,'.
          ' but you can start a new goal <a href="/goals">here <a>'; 
        } else {
          echo 'To record your progress, click <a href="javascript:startNewProgressDialog();">here</a>'; 
        }  
        echo '<br>To see How Google Prediction API uses your data? click <a href="javascript:promptForPrediction(true);">here</a>';
    }  

  ?>
</section>
<div id="newProgressDiv" title="Record new Progress"> </div>
<div id="predictionDiv" title="Google Prediction API"> 
    <p id="one"></p>    
    <p id="two"></p>
    <p id="three"></p> 
    <p id="four"><img id="predictedImageId"> </p>
</div>
