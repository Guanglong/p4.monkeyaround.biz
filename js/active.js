
// make the goal menu white
$('#active_li').css('background','white');

// prepare the dialog to allow user to enter the new progress
$("#newProgressDiv").dialog({
    autoOpen: false,
    modal: true,
    width:650,
    buttons:{ 
      'Save': function() {
                   saveNewProgress();                     
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

$('#predictionDiv').dialog({
    autoOpen: false,
    modal: true,
    width:800,
    buttons:{ 
        "Yes Predict Me":{
              text:'Yes, Predict for me',
                id:'YES_PREDICT_ME',
              click:function(){
                    startJoking();
                   }
          },
        'No Thank You': {
              text:'No, Thank You',
                id:'NO_THANK_YOU',
              click:function(){
                    $(this).dialog( "close" );
                    $('#YES_PREDICT_ME').show();                    
                    $('#NO_THANK_YOU').text('No, Thank You');
                   }
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
 
 // triggered by value change from the field progressDay
function progressDayChanged(){
    $("#status").html('')  ;
    if (validateProgressDay()) { // if valid, continue...
        setProgressDate();
    }
}

// validate if the progress day is valid
function validateProgressDay(){
    var invalid = false;
    var howManyDaysLater = parseInt($.trim($("#progress_day").val()));    
    if (howManyDaysLater < 1)  {
        invalid = true;
        $("#status").html('Please select a valid day');
   }

   return !invalid;
}

// called by progressDayChanged to set progress date
// when a value changed to progress day
function setProgressDate() {    
    // how many days later, need to add 1 to make up the missing time portion
    var howManyDaysLater = parseInt($.trim($("#progress_day").val())); 
    var startDateString = $("#start_date").val();    
    var progressDateAsString = daysLaterInString(startDateString,howManyDaysLater);
    $("#progress_date").val(progressDateAsString);  
}

// given a date in string (yyyy-mm-dd) format, return a string indicating a future date
function daysLaterInString(startDateString, howManyDaysLater){
    var startDateArray = startDateString.split("-");
    if (startDateArray[1].length==1) startDateArray[1] = "" +0+startDateArray[1]; // left pad 0 for mm
    if (startDateArray[2].length==1) startDateArray[2] = "" +0+startDateArray[2]; // left pad 0 for dd
    var originalDate = new Date(""+startDateArray[0]+'-'+startDateArray[1]+"-"+startDateArray[2]);  
    var laterDate = daysLater(originalDate,howManyDaysLater+1);
    var laterDateAsString =  ""+laterDate.getFullYear()+"-"+(laterDate.getMonth()+1)+"-"+laterDate.getDate();
    return laterDateAsString;
}

// for a given date (js date),. return date of a future date 
function daysLater(originalDate, howManyDaysLater) {
    return new Date(originalDate.getTime()+howManyDaysLater*24*60*60*1000);
}

function generateOptionsBetween(beginValue,endValue){
    var optionsString = "<option value='0'> -Select-</option>";
    for (var i=beginValue; i<=endValue; i++){
        var currentOption ="<option value='"+i+"''>"+i+"</option>";
        optionsString +=currentOption;
    }

  return optionsString;
}

// when user clicks signUp link on the index page,
// this function gets called, it prepares the signup form and display a dialog to user 
function startNewProgressDialog(){ 
  
    var goal_start_date = $("#start_date").val();

    // if no goal_days found then create goal_days field  
    if ($('#goal_start_date').length ==0){ 
        $("#newProgressDiv").append("Goal Start Date:<span id='goal_start_date' name='goal_start_date' > "+goal_start_date+" </span><br>");
    } else{
        $('#goal_start_date').html(goal_start_date);
    }

    if($('#goal_average').length==0){
        $("#newProgressDiv").append("<div id='goal_average'>Your goal average:"+$('#goal_avg').val()+" lbs/day</div>");      
    }

     // display last progress day entered
    var maxProgressDayEntered = parseInt($.trim($("#maxProgressDayEntered").val()));


    var last_known_progress_date = daysLaterInString(goal_start_date,maxProgressDayEntered);
      
    if ($('#last_known_progress_date').length ==0){ 
        $("#newProgressDiv").append("<div id='last_known_progress_date'>last entered progress date:"+last_known_progress_date+" </div><br>");
    }  
 
    var total_goal_days = $("#goal_days").val();
    
    // if no goal_days found then create goal_days field  
    if ($('#progress_day').length ==0){ 
        $("#newProgressDiv").append("Day("+(maxProgressDayEntered+1)+"~"+total_goal_days+"):<select id='progress_day' size='1' name='progress_day' onChange='progressDayChanged();'>"+
          generateOptionsBetween(maxProgressDayEntered+1,total_goal_days)+
        " </select>  <br>");
    } else{
        $('#progress_day').val('0');
    }

    // if no start_value found then create start_value field  
    if ($('#progress_date').length ==0){ 
        $("#newProgressDiv").append("Date(yyyy-mm-dd):<input disabled id='progress_date' type='text' maxlength='50' name='progress_date' onChange='progressDateChanged();'>  <br>");
    } else{
        $('#progress_date').val('');
    } 
    // if no start_date field found, then create start_date field
    if ($('#progress_value').length ==0){    
        $("#newProgressDiv").append("Weight (lbs):<input id='progress_value' type='number' maxlength='50' name='progress_value'   > <br>");
    } else {
        $('#progress_value').val('');
    }    

    if ($('#status').length ==0){ 
        $("#newProgressDiv").append("<div id='status' class='error'> </div> <br>");
    } else {
        // clear the mesage if any
        $("#status").html('');
    }

    // display the dialog for user to enter
    $("#newProgressDiv").dialog("open");   
 }

// first do javascript validation, and then do ajax signup
// upon successfully saving of new progress, refresh current page
function saveNewProgress(){ 
   // validate the data, if good, then do ajax save
   if (validateNewProgressBeforeSave()) {    
        saveNewProgressViaAjax();    
    }
}

// validate with javascript before save, 
// display error message and return false if data does not pass the validation
// otherwise, return true
function validateNewProgressBeforeSave(){
    var valid = false;
    $("#status").html('');  

    // javascript validation 
    var progress_value = $('#progress_value').val();
    var progress_day = $('#progress_day').val();   
   
    // javascript validation
    if ($.trim(progress_value).length ==0) {
       $("#status").html('weight value is empty');  
    } else if (isNaN(progress_value) ||Number(progress_value) <=0 ) {
       $("#status").html('weight value is not a valid number for Weight and See');  
    }  else if (progress_day.length==0 || progress_day ==0 ) {
       $("#status").html('progress Day/Date is empty'); 
    } else {
       valid = true; 
    } 

    return  valid; 
}


// sign user up with email, password, via ajax
function saveNewProgressViaAjax() {
    $("#status").html("Please wait...");
    var urlToSend = "/goals/saveNewProgressViaAjax?"; 

    $.ajax({
        type:"POST",
        url:urlToSend,
        data:{
            goal_id: $('#goal_id').val(),
            progress_value: $('#progress_value').val(),
            progress_day:$('#progress_day').val()      
        },
        cache: false
       }).done( function(msg) {
          createNewProgressDone(msg);
       }).fail(function(msg) {      
          createNewProgressFail(msg);
       }); 
}
   
 // what happens when saving fails  
function createNewProgressFail(msg){
    alert('Oops, there is a problem while saving the new progress...\n'+msg);
 }

// what happens when saving done:
// display a quick status message and then fresh the current page
function createNewProgressDone(msg){
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
          var total_goal_days = parseInt($("#goal_days").val());
          var progress_day_just_entered=parseInt($('#progress_day').val());
          var how_many_days_away = total_goal_days - progress_day_just_entered;

          $("#status").html("new progress saved succesfully!");      
          forwardPageAfterCreateNewProgress(how_many_days_away); 
          break;
      case 'E':
      default:        
          $("#status").html(statusMessage);
          break;    
    } 
}

function forwardPageAfterCreateNewProgress(how_many_days_away){
    if (how_many_days_away !=0) {      
      setTimeout("window.location ='/goals/active'",500);
    }  
} 

setTimeout('promptForPrediction(false)',500);

// prompt unless forced to do so or user has nerver been prompted to do so.
function promptForPrediction(forceToDisplay){ 
    if (forceToDisplay ||!hasPredictionPopupDisplayed() ){  
      // check how many times uses entered for this goal.
        var howManyData = $("#rowsData").val().split('],[').length;
        if (!isNaN(howManyData) && parseInt(howManyData)>=3){
            preparePredictionDialog();
            $('#predictionDiv').dialog('open');     
        } else if (forceToDisplay) { // display the message only if forced to displ
            alert('You need to make at least 2 data entries to see it');
        }
    }  
}
  
// value stored in rowsData is in the format like below:
//[[10,"12/05/2016",522],[11,"12/06/2016",345],[15,"12/10/2016",344]]
// here, let us parse the value and return a 2-dimession array
function getRows(){
    var rowsData = $('#rowsData').val();
    var rowsArray = rowsData.split("],["); 
    var rows = new Array();

    for (var i=0; i<rowsArray.length;i++) { 
       var rowData = rowsArray[i].replace('[','').replace(']','');     
       var cols = rowData.split(','); 

       var numberOfDays = parseInt(cols[0]);
       var planed= getPlannedValue(numberOfDays)
       cols.push(""+planed);

       rows.push(cols);    
    }
    return rows;
}

//  drawTable, it gets the data from the hidden field rowsData
// it also create a artificial column: planned
function drawTable() {
    var data = new google.visualization.DataTable();
    //data.addColumn('string', 'Progress');
    data.addColumn('string', 'Day');
    data.addColumn('string', 'Date');
    data.addColumn('string', 'Actual');
    data.addColumn('string', 'Planned');
    var rows = getRows();
    data.addRows(rows.length);        
    for (var i=0; i<rows.length; i++) {
       var columns = rows[i];           
       for (var j=0; j<columns.length; j++) {
         data.setValue(i,j,columns[j].replace('"','').replace('"',''));
       }            
    }

    var table = new google.visualization.Table(document.getElementById('table_div'));
    table.draw(data, {showRowNumber: false});
}  

// load the google table by calling drawTable function
google.load('visualization', '1', {packages:['table']});
google.setOnLoadCallback(drawTable);

function getPlannedValue(numberOfDays){
    var startValue = parseInt($('#start_value').val());
    var targetValue = parseInt($('#target_value').val());
    var totalDays = parseInt($('#goal_days').val());
     
    return Math.round(startValue + numberOfDays*(targetValue - startValue)/totalDays);
}

// get the data specifically for the chart
function getDataForDrawChart(){
    var rows =getRows();
    var chartData = new Array();

    chartData.push("Days,Actual,Plan".split(",")); // get the label first
    // for each day, get the actual weight and the calculated value.
    for (var i=0; i<rows.length; i++){
        var row= rows[i];
        var chartDataRow = new Array();
        chartDataRow.push(row[0]); // day
        chartDataRow.push(Number(row[2])); // actual value

        // plan/calculated value
        var planedValue = Number(getPlannedValue(row[0]));

        chartDataRow.push(planedValue); 

        chartData.push(chartDataRow);
    }

  return chartData;
}

// draw Chart using google chart API
function drawChart() {
    var data = google.visualization.arrayToDataTable( 
      getDataForDrawChart()
    );

    var options = {
      title: 'Progress You Made vs. Planned',
      curveType:'none',
      hAxis:{title: 'Days'},
      vAxis:{title:'Weight'}
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}

google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

$('#recordNewProgress').click(function() {
    startNewProgressDialog();
});

$('#predictMe').click(function(){
    promptForPrediction(true);  // force to predict, otherwise, it will not display if it has been shown already!
});