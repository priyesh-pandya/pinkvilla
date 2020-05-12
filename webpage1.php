<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<!-- Twitter Bootstrap and need to eliminate validation warnings if using Microsoft browsers -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- control the page's dimensions and scaling -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="black"/>
<link rel="manifest" href="manifest.json">
<!-- Favicons -->
<link rel="icon" href="https://www.pinkvilla.com/files/icons/favicon.ico">

<title>Pinkvilla Coding Exercise - By Priyesh Pandya</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
body {
 background: #FFFFFF;
}
#feed-section {
 margin-top: 20px;
 position: relative;
 max-width: 100%;
 width: 100%;
}
.white-panel img {
 width: 100%;
 max-width: 100%;
 height: auto;
 border-radius: 15px !important;
}
.white-panel a {
  color: #555555;
  font-weight:bold; 
}
.white-panel p { 
}
.white-panel {
 position: absolute;
 background: white;
 box-shadow: 0px 0px 0px rgba(0,0,0,0.3);
 padding: 0px;
}
.white-panel h1 {
 font-size: 1em;
}
.white-panel h1 {
 color: #A92733;
}
.white-panel:hover {
 box-shadow: 1px 1px 0px 0px 0px rgba(0,0,0,0.5);
 margin-top: -5px;
 -webkit-transition: all 0.3s ease-in-out;
 -moz-transition: all 0.3s ease-in-out;
 -o-transition: all 0.3s ease-in-out;
 transition: all 0.3s ease-in-out;
}
</style>

</head>
<body> 
 
<!-- Fixed navbar -->
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a alt='Pinkvilla' class="navbar-brand" href="">
      <img alt='Pinkvilla' src='https://www.pinkvilla.com/sites/all/themes/pinkvilla/images/logo-small.png'> 
	  </a>
    </div>
  </div>
</nav>
	
<!-- Pinterest display grid START -->
<div class="container">


<section id="feed-section">
<?php   
/*
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);
*/

try{
	$json_feed_string = file_get_contents('https://cdn.pinkvilla.com/feed/fashion-section.json'); 
}
catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

$jsonFeedDecoded = json_decode($json_feed_string,true);

$i=0;
$sortedFeeds=array();
// Array Sort by its viewcount 
foreach($jsonFeedDecoded as $id=>$feed)
{
	$sortedFeeds[$id] = array('viewCount'=>$feed['viewCount'],'imageUrl'=>$feed['imageUrl'],'title'=>$feed['title'],'path'=>$feed['path']);	
}

//sort($sortedFeeds);
//array_multisort( array_column($sortedFeeds, "viewCount"), SORT_DESC, $sortedFeeds );
array_multisort($sortedFeeds,SORT_DESC);

unset($jsonFeedDecoded,$json_feed_string);

foreach($sortedFeeds as $feed)
{
	if($i<20){// PWA load first 20 objects
		displayFeedPost($feed);// loop all feeds
	}
	$i++;
} 

function displayFeedPost($feed)
{	
	$imageUrl = $feed['imageUrl'];
	$title = implode(array_slice(explode(" ",$feed['title']), 0, 3)," ").'   ...'; 	
	$path = $feed['path'];	
	
	echo "<article class='white-panel'>
			<p>
			<a class='title text-dark' href='https://www.pinkvilla.com/$path'><img class='rounded-circle' src='$imageUrl' alt='$title'>$title
			</a>
			</p></article>";
}
?>	

</section>
</div>


<div align='center' id='loading'>
	<p style='color:pink'>Loading ...<p/><img src='img/Spinner-1s-200px.gif' align='center'>
</div>

<!-- Pinterest display grid END -->


<?php 
// JS string of sorted JSON
echo "<div style='display:block'><script> var jsonstring = ".json_encode($sortedFeeds)." </script></div>";
?>

<!-- Bootstrap core JavaScript
================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js">
</script>
<script src="js/pinterest_grid.js"></script> 
<script>
	$(document).ready(function() {

		$('#feed-section').pinterest_grid({
			no_columns: getViewPortCol(),
			padding_x: 10,
			padding_y: 10,
			margin_bottom: 50,
			single_column_breakpoint: 700
		}); 
	});		
	
	// serviceWorker
	window.onload = () => {  
	'use strict';     
	if ('serviceWorker' in navigator) {     
	navigator.serviceWorker  
	.register('js/service_worker.js'); 
	} 
	}
	
 	setTimeout(function(){loadFeed('off');$('#loading').hide();},3000);// PWA page load
	
	// Fetch & display  Feeds 		
function loadFeed(pwaMode){
  var pwpCount = 20;
  var k= 0; // start
  
  jQuery.each(jsonstring,function(i) {
	   k++;
	   if(k>pwpCount){
  	 	var title = (jsonstring[i].title).split(' '); 
		var feed = "<article class='white-panel'><p><a class='title text-dark' href='https://www.pinkvilla.com/"+jsonstring[i].path+"'><img class='rounded-circle' src='"+jsonstring[i].imageUrl+"'>"+ title[0]+' '+title[1]+' '+title[2] +'  ...' +"</a></p></article>";	
		$('#feed-section').append(feed);
	   }		
	});
		
};	
</script>
</body>
</html>