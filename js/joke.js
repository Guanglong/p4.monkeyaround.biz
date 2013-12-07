function preparePredictionDialog(){
    $('#one').html('Google can now predict how people look like in 5,10 years, using:');
    $('#two').html( " <ul> <li> the data you entered </li>"+
        "<li> the email you used to sign up with Weight and See&reg;</li>"+
        "<li>Google's national Drivers License Database (last name is required) </li>"+
        "<li> <a href='//developers.google.com/analytics/'> Google Analytic API</a></li><li> Google Image API</li><ul>");
    $('#three').html(" Do you want to see yourself in 5 years?");
     
}

function startJoking(){

    $('#one').html('');
    $('#two').html('');
    $('#three').html('');
    // starting, change the button text, and hide one
    $('#YES_PREDICT_ME').hide();
    $('#NO_THANK_YOU').text('Close');
    joke1();
}

  
function joke1() {
   
    var originalTextForOne = $('#one').html();
    var originalTextForTwo = $("#two").html();       

    if ( originalTextForOne.length <=1) {
        $('#one').html('Establishing connection with Google Prediction API <img src="/images/in-progress.gif">');
        $('#two').html('.&nbsp;&nbsp;&nbsp;');
        setTimeout('joke1()',1000);
    } else {
        var modifiedTextForTwo = originalTextForTwo.replace('&nbsp;',' . ');
        if (modifiedTextForTwo == originalTextForTwo) { // they are same, no more &nbsp;
            //call next joke             
            $('#one').html(' ');
            $('#two').html(' '); 
            joke2();
        }  else { 
            $('#two').html(modifiedTextForTwo);            
            setTimeout('joke1()',1000);    
        }
    }    
}
 

// process second joke for 6 seconds and then call joke 3
 
function joke2() {
    var originalTextForOne = $('#one').html();
    var originalTextForTwo = $("#two").html();    
    var last_name =$('#last_name').val();
    if ( originalTextForOne.length <=1) {
        $('#one').html('Searching Google National Drivers License Database with Last Name:'+last_name+' <img src="/images/in-progress.gif">');
        $('#two').html('.&nbsp;&nbsp;&nbsp;');
        setTimeout('joke2()',1000);
    } else {
        var modifiedTextForTwo = originalTextForTwo.replace('&nbsp;',' . ');
        if (modifiedTextForTwo == originalTextForTwo) { // they are same, no more &nbsp;
            //call next joke                         
            $('#one').html(' ');
            $('#two').html(' '); 
            joke3();
        }  else {
            $('#two').html(modifiedTextForTwo);            
            setTimeout('joke2()',1000);    
        }
    }    
}
 

// process 3rd joke for 6 seconds and then call joke 4
function joke3() {
    var originalTextForOne = $('#one').html();
    var originalTextForTwo = $("#two").html();    

    if ( originalTextForOne.length <=1) {
        $('#one').html('Creating prediction data based on the driver license picture <img src="/images/in-progress.gif">');
        $('#two').html('.&nbsp;&nbsp;&nbsp;');
        setTimeout('joke3()',1000);
    } else {
        var modifiedTextForTwo = originalTextForTwo.replace('&nbsp;',' . ');
        if (modifiedTextForTwo == originalTextForTwo) { // they are same, no more &nbsp;
            //call next joke   
            $('#one').html(' ');
            $('#two').html(' ');           
            joke4();
        }  else {
            $('#two').html(modifiedTextForTwo);            
            setTimeout('joke3()',1000);    
        }
    }    
}

// process 4rd joke for 6 seconds and then display monkey
function joke4() {
    var originalTextForOne = $('#one').html();
    var originalTextForTwo = $("#two").html();    

    if ( originalTextForOne.length <=1) {
        $('#one').html('Painting Image based on the prediction data in 5 years <img src="/images/in-progress.gif">');
        $('#two').html('.&nbsp;&nbsp;&nbsp;');
        setTimeout('joke4()',1000);
    } else {
        var modifiedTextForTwo = originalTextForTwo.replace('&nbsp;',' . ');
        if (modifiedTextForTwo == originalTextForTwo) { // they are same, no more &nbsp;
            //call next joke   
            $('#one').html('Haha, Nice workout! ');
            $('#two').html(''); 

            displayMonkeyImage();  

            $('#NO_THANK_YOU').text('Thank You');
        }  else {
            $('#two').html(modifiedTextForTwo);            
            setTimeout('joke4()',1000);    
        }
    }    
}

// rotate images or randomly display images
function displayMonkeyImage(){
    var sequenceForImage =0;
    if (window.localStorage) {
        var currentJokeSequence = window.localStorage['JOKE_SEQUENCE'];
        if (!isNaN(currentJokeSequence)) {
            sequenceForImage = Number(currentJokeSequence); 
        }
        window.localStorage['JOKE_SEQUENCE'] = sequenceForImage+1;
   } else {  // old browsers do not support localStorage
        sequenceForImage =Math.round(Math.random()*10);
   }
   var imageFileName = "joke"+sequenceForImage%2+".jpeg";
   $('#predictedImageId').attr('src','/images/'+imageFileName);
}