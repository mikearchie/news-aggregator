<?php
# '../' works for a sub-folder.  use './' for the root
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials

// check variable of item passed in - if invalid data, forcibly redirect back to survey-list.php page
if (isset($_GET['id']) && (int)$_GET['id'] > 0) { // proper data must be on querystring
	 $myID = (int)$_GET['id']; // Convert to integer, will equate to zero if fails
} else {
//	myRedirect(VIRTUAL_PATH . "surveys/survey-list.php");
	header('Location:index.php');
}

# SQL statement
$sql = "select * from ".PREFIX."NewsFeeds";

#Fills <title> tag. If left empty will default to $PageTitle in config_inc.php
$config->titleTag = 'News RSS';

#Fills <meta> tags.  Currently we're adding to the existing meta tags in config_inc.php
// $config->metaDescription = 'Seattle Central\'s ITC250 Class Surveys are made with pure PHP! ' . $config->metaDescription;
// $config->metaKeywords = 'Surveys,PHP,Fun,Bran,Regular,Regular Expressions,'. $config->metaKeywords;
# END CONFIG AREA ----------------------------------------------------------

get_header(); #defaults to theme header or header_inc.php
?>

<h3 align="center"><?=smartTitle();?></h3>

<?php
#reference images for pager
$prev = '<img src="' . VIRTUAL_PATH . 'images/arrow_prev.gif" border="0" />';
$next = '<img src="' . VIRTUAL_PATH . 'images/arrow_next.gif" border="0" />';

# Create instance of new 'pager' class
$myPager = new Pager(10,'',$prev,$next,'');
$sql = $myPager->loadSQL($sql);  #load SQL, add offset

# connection comes first in mysqli (improved) function
$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

if (mysqli_num_rows($result) > 0) { // if records exist
	if ($myPager->showTotal()==1){$itemz = "feed";}else{$itemz = "feeds";}  //deal with plural
	while($row = mysqli_fetch_assoc($result)) {# process each row
		if ((int)$row['CategoryID'] == $myID) {
			 echo '<div align="center"><a href="' . VIRTUAL_PATH . 'news/feed_view.php?id=' . (int)$row['FeedID'] . '">' . dbOut($row['FeedTitle']) . '</a>';
			 echo '</div>';
		}
	}
	echo $myPager->showNAV(); # show paging nav, only if enough records
} else { // no records
    echo "<div align=center>There are currently no news.</div>";	
}

@mysqli_free_result($result);
get_footer(); #defaults to theme footer or footer_inc.php