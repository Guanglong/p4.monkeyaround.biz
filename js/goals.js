// prepare the dialog to enter new goal
$("#newGoalDiv").dialog({
  autoOpen: false,
  modal: true,
  width:650,
  buttons:{ 
    'Save': function() {
                 saveNewGoal();                     
                  },
    'Cancel': function() {
                $(this).dialog( "close" );                     
                }      
          },
  show: {
        effect: "blind",   
        duration: 1000
        },
  hide: {
        effect: "explode",
        duration: 1000
        } 
});

// when user clicks link on the goals page,
// this function gets called, it prepares the form and display a dialog to user 
function startNewGoalDialog(){ 
  // if no start_date field found, then create start_date field
  if ($('#start_date').length ==0){    
    $("#newGoalDiv").append("<label title='Starting Date for the goal'>Start Date(yyyy-mm-dd):</label>"+
        "<input id='start_date' type='text' maxlength='50' name='start_date' > <br>");
  } else {
    $('#start_date').val('');
  }
  
  // if no goal_days found then create goal_days field  
  if ($('#goal_days').length ==0){ 
    $("#newGoalDiv").append("<label title='Number of days plan to reach the goal'>Goal Days(1~120):</label>"+
        "<input id='goal_days' type='text' maxlength='50' name='goal_days'   > <br>");
  } else{
    $('#goal_days').val('');
  }

  // if no start_value found then create start_value field  
  if ($('#start_value').length ==0){ 
    $("#newGoalDiv").append("<label title='Starting Weight'>Starting Weight (lbs):</label>"+
        "<input id='start_value' type='text' maxlength='50' name='start_value'   >  <br>");
  } else{
    $('#start_value').val('');
  }

    // if no target_value found then create target_value field  
  if ($('#target_value').length ==0){ 
    $("#newGoalDiv").append("<label title='Goal you want to reach'>Target Weight (lbs):</label>"+
        "<input id='target_value' type='text' maxlength='50' name='target_value'  >  <br>");
  } else{
    $('#target_value').val('');
  }

  if ($('#status').length ==0){ 
    $("#newGoalDiv").append("<div id='status' class='error'> </div> <br>");
  } else {
    // clear the mesage if any
    $("#status").html('');
  }

  // display the dialog for user to enter
  $("#newGoalDiv").dialog("open");   
 }

// save new goal
// first do javascript validation, and then do ajax saving
// upon successfully saving, forward to active page
function saveNewGoal(){ 
  if (validateGoalData()) { // is data valid, then do save     
    createNewGoalViaAjax();    
  }   
}

// return true if date entered are good.
// otherwise,  return false and display the error
function validateGoalData(){
  $("#status").html('');
  // javascript validation 
  var start_date = $.trim($('#start_date').val());
  var goal_days = $.trim($('#goal_days').val());  
  var start_value = $.trim($('#start_value').val());  
  var target_value = $.trim($('#target_value').val());  
   
  var valid = false;
  
  // javascript validation
  if (start_date.length ==0) {
    $("#status").html('Start Date is empty');     
  } else if (!isValidDate(start_date)) {
    $("#status").html('Start Date is not in valid format:yyyy-mm-dd'); 
  } else if (goal_days.length==0 ) {
    $("#status").html('Goal Days is empty'); 
  } else if(isNaN(goal_days)||parseInt(goal_days) >120 ||parseInt(goal_days) <1) {
    $("#status").html('Goal Days is invalid, it has to be betwen 1 and 120'); 
  } else if (start_value.length==0) {
    $("#status").html('starting Weight is empty'); 
  } else if(isNaN(start_value)) {
    $("#status").html('Start Weight is invalid, it has to be a number'); 
  } else if (target_value.length==0) {
    $("#status").html('Target Weight is empty');     
  } else if (isNaN(target_value)) {
    $("#status").html('Target Weight is invalid, it has to be a number'); 
  } else {
    valid = true; 
  }  
  
   return valid; 
}

// test if a value is a valid date in the format:yyyy--mm-dd 
function isValidDate(value_){
  var valid = false;
  if (($.trim(value_)).length >0){
    var valueArray = ($.trim(value_)).split('-');
    if (valueArray.length ==3) {
       var yyyy= valueArray[0];
       var mm= valueArray[1];
       var dd= valueArray[2];

       if (mm.length==1) mm=''+'0'+mm; //left pad 0
       if (dd.length==1) dd=''+'0'+dd; //left pad 0
       var paddedValue_ = ""+yyyy+'-'+mm+"-"+dd;
       
       if ("Invalid Date" == new Date(paddedValue_)){
          valid = false;
       } else {
          valid = true;
       }

    } 
  }
  return valid;
} 

// create new goal for user via ajax
function createNewGoalViaAjax() {
  $("#status").html("Please wait...");
  var urlToSend = "/goals/createNewGoalViaAjax?";

  $.ajax({
      type:"POST",
      url:urlToSend,
      data:{
        start_date: $('#start_date').val(),
        goal_days: $('#goal_days').val(),
        start_value:$('#start_value').val(),
        target_value:$('#target_value').val()
      },
      cache: false
     }).done( function(msg) {
      createNewGoalDone(msg);
     }).fail(function(msg) {      
      createNewGoalFail(msg);
     });
}
   
function createNewGoalFail(msg){
  alert('Oops, there is a problem while saving the new goal...\n'+msg);
}

function createNewGoalDone(msg){
  var status ='E';
  var statusMessage ='';
  if ($.trim(msg).length !=0) {
    status = msg.substr(0,1);
    if ($.trim(msg).length >=2) {
      statusMessage = msg.substr(2);
    }
  }
  
  switch (status) {
    case 'S':
        $("#status").html("New goal created succesfully! Please wait for forwarding...");      
        forwardPageAfterCreateNewGoal(); 
        break;
    case 'E':
    default:        
        $("#status").html(statusMessage);
        break;    
  } 
}

// give user a bittle time delay, and then forward to active goal page
function forwardPageAfterCreateNewGoal(){
  setTimeout("window.location ='/goals/active'",500);
}