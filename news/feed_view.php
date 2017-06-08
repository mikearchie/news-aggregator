<?php
# '../' works for a sub-folder.  use './' for the root
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials

// check variable of item passed in - if invalid data, forcibly redirect back to survey-list.php page
if (isset($_GET['id']) && (int)$_GET['id'] > 0) { // proper data must be on querystring
	 $myID = (int)$_GET['id']; // Convert to integer, will equate to zero if fails
} else {
//	myRedirect(VIRTUAL_PATH . "surveys/survey-list.php");
	header('Location:category_view.php');
}

$myNews = new News($myID);

$sql = "select * FROM ".PREFIX."NewsFeeds";
$config->titleTag = 'News RSS';
# END CONFIG AREA ----------------------------------------------------------
get_header(); #defaults to theme header or header_inc.php
?>
<h3 align="center"><?=smartTitle();?></h3>

<?php
if ($myNews->isValid) {
	$response = file_get_contents($myNews->request);
	$xml = simplexml_load_string($response);
	echo '<h2>' . $xml->channel->title . '</h2>';
	foreach ($xml->channel->item as $story) {
		echo '<a href="' . $story->link . '">' . $story->title . '</a><br />'; 
		echo '<p>' . $story->description . '</p><br /><br />';
	}
} else {#no records
    echo "<div align=center>There are currently no News</div>";
}

get_footer(); #defaults to theme footer or footer_inc.php


class News
{
	public $feedID = 0;
	public $request = '';
	public $isValid = false;
	
	public function __construct($feedID)
	{
		$this->feedID = $feedID;	
		$sql = "select FeedID, URL from ".PREFIX."NewsFeeds where FeedID = ".$this->feedID;
		$result = mysqli_query(IDB::conn(), $sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
		
		if (mysqli_num_rows($result) > 0) { // if records exist
			while ($row = mysqli_fetch_assoc($result)) {
				$this->isValid = true;
				$this->request = dbOut($row['URL']);
			}
		}
		
		@mysqli_free_result($result); // done with the data
	}
}