<?php if (count($goals)==0) {
   echo  " <section>".$user->email.", curerntly there are no goals created </section>";
   } else {
    echo  "<section>".$user->email.", here are the goals you created previously: </section>";
   }   
?>

<br/>

<?php  $goalIndex =1;  $hasActiveGoal = FALSE; ?>

<table id="goalsTableId">
    <?php if (count($goals) >0) { ?>
      <tr> 
      <th>Goal# </th>
      <th>Status</th>
      <th>Start Date</th>
      <th># of days</th>
      <th>Starting Weight(lb) </th>
      <th>Target Weight(lb) </th>    
      </tr>
  <?php } ?>  

<?php foreach($goals as $goal): 
  if ($goal['active_flag']=='Y') {
    $goal_status= 'Active'; 
    $hasActiveGoal = TRUE;
  } else {
    $goal_status='Inactive';
  }
?>
      <?='<tr title="goal created on '.Time::display($goal['created'],"Y/m/d :ia").'">' ?> 
      <?='<td>'.$goalIndex.'</td>' ?>
      <?='<td>'.$goal_status.'</td>' ?>      
      <?='<td>'.$goal['start_date'].'</td>' ?>
      <?='<td>'.$goal['goal_days'].'</td>' ?> 
      <?='<td>'.$goal['start_value'].'</td>' ?>
      <?='<td>'.$goal['target_value'].'</td>' ?>             
   <?='</tr>' ?>  
  
  <?php $goalIndex +=1;?>

<?php endforeach; ?>

</table >

<br>
<section>To set up a new goal, click <a href="javascript:startNewGoalDialog();">here</a>, 
  which will make all previously goals inactive.  
  <?php if ($hasActiveGoal ) { echo "<br>To monitor your current active goal, click <a href='/goals/active'>here</a>";} ?>
</section>
<div id="newGoalDiv" title="New Goal Setup"> </div>
