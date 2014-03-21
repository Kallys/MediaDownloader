<?php 

/*** Config file ***/

//Rename it only if you change index.php to downloader.php for example
$mainPage = "index.php";

// -> with "/" <- at the end. Directory where you videos are downloaded
$folder = "videos/"; 

//Rename it only if you change list.php to myvideos.php for example
$listPage = "list.php";

// Enable password to access the panel
// 1 -> enable 0 -> disable
$security = 1; 

// PHP::md5(); You can use md5.php to generate an other one
// default : root
$secretPassword = "63a9f0ea7bb98050796b649e85481845";

?>
