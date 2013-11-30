
setInterval('rotateBackgroundImage()',2000);

// change the background image sequentially 
// for example: 
// if currently image is 0.jpg -->1.jpg
// if currently image is 1.jpg -->2.jpg
// ...l
// if currently image is 4.jpg -->0.jpg
function rotateBackgroundImage(){
  var currentImageUrl = $('#mainContent').css("background-image");
  
  var tempPathArray =currentImageUrl.split("/");
  
  var currentImageIndex = parseInt(tempPathArray[tempPathArray.length-1].replace('.jpg")',''));
  
  var nextImageIndex = (currentImageIndex+1)%7;
  var nextImageUrl = currentImageUrl.replace(currentImageIndex+".jpg",nextImageIndex+".jpg" );  
  $('#mainContent').css("background-image", nextImageUrl); 
}
 
