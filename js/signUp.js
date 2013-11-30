
// prepare the dialog to sign user up
$("#signUpDiv").dialog({
  autoOpen: false,
  modal: true,
  width:650,
  buttons:{ 
    'Sign me Up': function() {
                    signUserUp();                     
                  } ,
    'Cancel': function() {
          $(this).dialog('close');                     
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

// sign user up:
// first do javascript validation, and then do ajax signup
// upon successfully sign-up, forward to profile page
function signUserUp(){
  // javascript validation 
  var email = $('#email').val();
  var password = $('#password').val();  

  if ($.trim(email).length ==0) {
    $("#status").html('Email is empty');  
  } else if ($.trim(password).length==0) {
    $("#status").html('Password is empty'); 
    } else if(!validEmailFormat(email)) { 
    $("#status").html('Invalid Email address'); 
  } else {
    signUserUpViaAjax();
  }
}

// peform email format validation
function validEmailFormat(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

// sign user up with email, password, via ajax
function signUserUpViaAjax() {
  $("#status").html("Please wait...");
    var urlToSend = "/users/signUpViaAjax";  
    $.ajax(   
       {type:"POST",
        url:urlToSend,
        data:{
              email:$('#email').val(),
              password:$('#password').val()
            },
       cache: false
       }).done( function(msg) {
        signUpDone(msg);
       }).fail(function(msg) {      
        signUpFail(msg);
       });
}

// what about when signup fails
function signUpFail(msg){
  $("#status").html("sign up failed"+msg);  
  alert('Oops, there are some unexpected problems while signing up user\n'+msg);
} 

// when user clicks signUp link on the index page,
// this function gets called, it prepares the signup form and display a dialog to user 
function signUp(){
  // if no email field found, then create email field
  if ($('#email').length ==0){    
    $("#signUpDiv").append("Email:<input id='email' type='text' maxlength='50' name='email' > <br>");
  } else {
    $('#email').val('');
  }
  
  // if no password found then create password field  
  if ($('#password').length ==0){ 
    $("#signUpDiv").append("Password:<input id='password' type='password' maxlength='50' name='password' >");
  } else{
    $('#password').val('');
  }

  if ($('#status').length ==0){ 
    $("#signUpDiv").append("<div id='status' class='error'> </div>");
  } else {
    // clear the mesage if any
    $("#status").html('');
  }

  // display the dialog for user to enter
  $("#signUpDiv").dialog("open");   
 }

// what happens when signup ajax call returns
 function signUpDone(msg) {
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
      $("#status").html("Sign up succesfully! Please wait for forwarding...");      
      forwardPageAfterSignUp(); 
        break;
    case 'E':
    default:        
        $("#status").html(statusMessage);
        break;    
  } 
}

// delay about 1 sec, and forward to profile page
function forwardPageAfterSignUp(){
  setTimeout("window.location ='/goals'",1000);
}
