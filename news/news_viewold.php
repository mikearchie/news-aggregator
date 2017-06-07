<?php
//news_view.php


require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials
$sql = "select * FROM sp17_Feed";

spl_autoload_register('MyAutoLoader::NamespaceLoader');//required to load SurveySez namespace objects
$config->metaRobots = 'no index, no follow';#never index survey pages

# check variable of item passed in - if invalid data, forcibly redirect back to demo_list.php page
if(isset($_GET['id']) && (int)$_GET['id'] > 0){#proper data must be on querystring
	 $myID = (int)$_GET['id']; #Convert to integer, will equate to zero if fails
}else{
	myRedirect(VIRTUAL_PATH . "news/index.php");
}

// $myNews = new NewsRSS\News($myID); //MY_News extends survey class so methods can be added
// if($myNews->isValid)
// {
// 	$config->titleTag = "'" . $myNews->Title . "' News!";
// }else{
// 	$config->titleTag = smartTitle(); //use constant
// }
#END CONFIG AREA ----------------------------------------------------------

get_header(); #defaults to theme header or header_inc.php
?>
<h3 align="center"><?=smartTitle();?></h3>

<?php
 echo '<p> <li><a href="' . VIRTUAL_PATH . 'news/sports.php?">Sports</a></li></p>';
 // echo '<div align="center"><a href="' . VIRTUAL_PATH . 'news/sports.php?id=' . (int)$row['FeedID'] . '">
 // </a>';
 // echo '</div>';

 echo ' <p><li><a href="' . VIRTUAL_PATH . 'news/entertainment.php?">Entertainment News </a></li></p>';

 echo '<p><li><a href="' . VIRTUAL_PATH . 'news/world.php?"> World News</a></li></p>';

?>
<?php


// if($myNews->isValid)
// { #check to see if we have a valid SurveyID
// 	echo '<p>' . $myNews->Description . '</p>';
// 	echo $myNews->showCatogries();
// }else{
// 	echo "Sorry, no News!";
// }

// echo "Sorry";
get_footer(); #defaults to theme footer or footer_inc.php
