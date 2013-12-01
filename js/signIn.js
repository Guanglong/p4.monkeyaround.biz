$('#home_li').css('background','white');  

// prepare the dialog to sign user up
$("#signInDiv").dialog({
  autoOpen: false,
  modal: true,
  width:650,
  buttons:{ 
    'Sign In': function() {
                    signUserIn();                     
                  },
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
function signUserIn(){
  // javascript validation 
  var email = $('#signIn_email').val();
  var password = $('#signIn_password').val();  

  if ($.trim(email).length ==0) {
    $("#signIn_status").html('Email is empty');  
  } else if ($.trim(password).length==0) {
    $("#signIn_status").html('Password is empty'); 
    } else if(!validEmailFormat(email)) { 
    $("#signIn_status").html('Invalid Email address'); 
  } else {
    signUserInViaAjax();
  }
}

// peform email format validation
function validEmailFormat(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

// sign user up with email, password, via ajax
function signUserInViaAjax() {
  $("#signIn_status").html("Please wait...");
    var urlToSend = "/users/signInViaAjax";  
    $.ajax(   
       {type:"POST",
        url:urlToSend,
        data:{
            email:$('#signIn_email').val(),
            password:$('#signIn_password').val()
            },
        cache: false
       }).done( function(msg) {
        signInDone(msg);
       }).fail(function(msg) {      
        signInFail(msg);
       });
}

// what about when signup fails
function signInFail(msg){
  $("#signIn_status").html("sign in failed"+msg);  
  alert('Oops, there are some unexpected problems while signing in user\n'+msg);
} 

// when user clicks signUp link on the index page,
// this function gets called, it prepares the signup form and display a dialog to user 
function signIn(){

  // if no email field found, then create email field
  if ($('#signIn_email').length ==0){    
    $("#signInDiv").append("Email:<input id='signIn_email' type='text' maxlength='50' name='signIn_email' > <br>");
  } else {
    $('#signIn_email').val('');
  }
  
  // if no password found then create password field  
  if ($('#signIn_password').length ==0){ 
    $("#signInDiv").append("Password:<input id='signIn_password' type='password' maxlength='50' name='signIn_password' >");
  } else {
    $('#signIn_password').val('');
  }

  if ($('#signIn_status').length ==0){ 
    $("#signInDiv").append("<div id='signIn_status' class='error'> </div>");
  } else {
    // clear the mesage if any
    $("#signIn_status").html('');
  }

  // display the dialog for user to enter
  $("#signInDiv").dialog("open");   
 }

// what happens when signup ajax call returns
 function signInDone(msg) {
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
      $("#signIn_status").html("Sign In succesfully! Please wait for forwarding...");      
      forwardPageAfterSignUp(); 
        break;
    case 'E':
    default:        
        $("#signIn_status").html(statusMessage);
        break;    
  } 
}

// delay about 1 sec, and forward to profile page
function forwardPageAfterSignUp(){
  setTimeout("window.location ='/goals'",1000);
}
