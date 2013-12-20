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
      <th>Goal </th>
      <th>Status</th>
      <th>Start On</th>
      <th>Duration</th>
      <th>Starting Weight </th>
      <th>Target Weight </th>    
      <th>Goal Avg </th> 
      </tr>
      <?='<tr></tr>' ?> 

      <?='<tr></tr>' ?>        
  <?php } ?>  

<?php foreach($goals as $goal): 
  if ($goal['active_flag']=='Y') {
    $goal_status= 'Active'; 
    $hasActiveGoal = TRUE;
  } else {
    $goal_status='Inactive';
  }
?>
    <?='<tr title="goal created on '.Time::display($goal['created'],"m/d/Y h:ia").'">' ?> 
    <?='<td>'.$goalIndex.'</td>' ?>
    <?='<td>'.$goal_status.'</td>' ?>      
    <?='<td>'.$goal['start_date'].'</td>' ?>
    <?='<td>'.$goal['goal_days'].' days</td>' ?> 
    <?='<td>'.$goal['start_value'].' lbs</td>' ?>
    <?='<td>'.$goal['target_value'].' lbs</td>' ?>             
    <?='<td>'.round(($goal['start_value']-$goal['target_value'])/$goal['goal_days'],1).' lbs/day</td>' ?> 
  <?='</tr>' ?>  
  
  <?php $goalIndex +=1;?>

<?php endforeach; ?>

</table >

<br>
<section> 
    <button id="createNewGoal" type="button" title="Start New Goal,which will make all previous goal inactive">Start New Goal</button>
   
    <?php if ($hasActiveGoal ) {     
      echo '<button id="monitorActiveGoal" type="button" title="monitor current active goal">Monitor Active Goal</button>';
    } ?>

</section>
<div id="newGoalDiv" title="New Goal Setup"> </div>