<?php if (count($goals)==0) {
   echo  " <section>".$user->email.", curerntly there are no goals created </section>";
   } else {
    echo  "<section>".$user->email.", here are the goals you created previously: </section>";
   }   
?>

<br/>
<?php  $goalIndex =1;  $hasActiveGoal = FALSE; ?>

<?php foreach($goals as $goal): 
  if ($goal['active_flag']=='Y') {
    $goal_status= 'Active'; 
    $hasActiveGoal = TRUE;
  } else {
    $goal_status='Inactive';
  }
?>
 <fieldset title="<?php echo $goal_status ?> Goal">

    Goal <?= $goalIndex ?> -- <?php echo $goal_status ?> :  
    Created on
         <time datetime="<?=Time::display($goal['created'],'Y-m-d G:i')?>">
          <?=Time::display($goal['created'])?>
        </time> <br>        
    Details: You plan to start your goal on <?=$goal['start_date'] ?> from <?=$goal['start_value'] ?> lbs. to  
    <?=$goal['target_value'] ?> lbs.
   <br>
  </fieldset>
  
  <?php $goalIndex +=1;?>

<?php endforeach; ?>
<br>
<section>To set up a new goal, click <a href="javascript:startNewGoalDialog();">here</a>, 
  which will make all previously goals inactive.  
  <?php if ($hasActiveGoal ) { echo "<br>monitor your active goal <a href='/goals/active'>here</a>";} ?>
</section>
<div id="newGoalDiv" title="New Goal Setup"> </div>
