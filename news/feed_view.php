<?php
// '../' works for a sub-folder.  use './' for the root
require '../inc_0700/config_inc.php'; //provides configuration, pathing, error handling, db credentials

// check variable of item passed in - if invalid data, forcibly redirect back to survey-list.php page

if (isset($_GET['id']) && (int)$_GET['id'] > 0) { // proper data must be on querystring
	 $myID = (int)$_GET['id']; // Convert to integer, will equate to zero if fails
} else {
	header('Location:category_view.php');
}

//this is the code for caching. Need to work on it some more!
if(!isset($_SESSION)) {
    session_start();
}
//1) check if session exists, if not, start new
if(!isset($_SESSION['NewsFeeds'])) {
    // if session var does not exists, create it.
    $_SESSION['NewsFeeds'] = array();
} else {
    //check cache for unexpired current newsfeed id
    foreach ($_SESSION["NewsFeeds"] as $feedObject) {
        if ($feedObject->feedID == $myID && time() < $feedObject->expireTime) {
            //if feed is in cache and hasn't expired yet, use it
            $myNews = $feedObject;
            $config->titleTag = $myNews->feedTitle . ' (from cache)';
            break;
        }
    }
}

//create new feed object if it wasn't pulled from cache
if (!isset($myNews)) {
    try {
        $myNews = new News($myID);
    } catch (Exception $e) {
        echo 'Unable to retrieve feed. ' + $e->getMessage();
    }
    //add new feed to cache
    if ($myNews->isValid) {
        $_SESSION['NewsFeeds'][] = $myNews;
        $config->titleTag = $myNews->feedTitle;

    }
}


// END CONFIG AREA ----------------------------------------------------------
get_header(); // defaults to theme header or header_inc.php
?>

<h3 align="center"><?=$config->titleTag;?></h3>

<?php
if ($myNews->isValid) {
	//echo '<h2>' . ucwords($myNews->xml->channel->title) . '</h2>';
    echo $myNews->feedContent;
    if (isset($_SERVER['HTTP_REFERER']))
	   echo '<a href="'.htmlspecialchars($_SERVER['HTTP_REFERER']).'">Go Back</a>';
} else {//no records
    echo "<div align=center>There are currently no News feeds available.</div>";
}

get_footer(); // defaults to theme footer or footer_inc.php

class News
{
	public $feedID = 0;
    public $feedTitle = '';
	public $isValid = false;
    public $expireTime;
    public $feedContent = '';

	public function __construct($feedID)
	{
		$this->feedID = $feedID;
		$sql = "select FeedID, FeedTitle, URL from ".PREFIX."NewsFeeds where FeedID = ".$this->feedID;
		$result = mysqli_query(IDB::conn(), $sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

        $this->expireTime = strtotime("+10 minutes");

		if (mysqli_num_rows($result) > 0) { // if records exist
		 	while ($row = mysqli_fetch_assoc($result)) {
                $this->feedTitle = dbOut($row['FeedTitle']);
                $this->request = dbOut($row['URL']);
                $this->response = file_get_contents($this->request);
                $xml = simplexml_load_string($this->response);
                foreach ($xml->channel->item as $story) {
                    $this->feedContent .= '<section>' . $story->description . '</section>';
    		 	}
                if ($this->feedContent != '')
                    $this->isValid = true;
	        }
		@mysqli_free_result($result); // done with the data
        }
    }

    // function showFeed()
    // {
    //     $myReturn = '';
    //     var_dump($this->descriptions);
    //     foreach ($this->descriptions as $description) {
    //         $myReturn .= '<section>' . $description . '</section>';
    //     }
    //     return $myReturn;
    // }
}

?>
