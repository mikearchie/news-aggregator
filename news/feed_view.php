<?php

# '../' works for a sub-folder.  use './' for the root
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials

$sql = "select * FROM sp17_NewsFeeds";

$config->titleTag = 'News RSS';

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

if(mysqli_num_rows($result) > 0)
{#records exist - process
	if($myPager->showTotal()==1){$itemz = "new";}else{$itemz = "news";}  //deal with plural
    echo '<div align="center">We have ' . $myPager->showTotal() . ' ' . $itemz . '!</div>';
	while($row = mysqli_fetch_assoc($result))
	{# process each row
         echo '<div align="center"><a href="' . VIRTUAL_PATH . 'news/NBA.php?"></a>';
				 echo '<div align="center"><a href="' . VIRTUAL_PATH . 'news/NFL.php?"></a>';

          // echo '<div align="center"><a href="' . VIRTUAL_PATH . 'news/entertainment.php?id=' . (int)$row['FeedID'] . '">' . dbOut($row['Feed ']) . '
 				 //  </a>';
          // echo '<div align="center"><a href="' . VIRTUAL_PATH . 'news/world.php?id=' . (int)$row['FeedID'] . '">' . dbOut($row['Feed ']) . '
 				 //  </a>';
				// echo '<div align="center"><a href="' . VIRTUAL_PATH . 'surveys/survey_view.php?id=' . (int)$row['AdminID'] . '">' . dbOut($row['FirstName']) . ' ' . dbOut($row['LastName']) . ' </a>';

         echo '</div>';
	}
	echo $myPager->showNAV(); # show paging nav, only if enough records
}else{#no records
    echo "<div align=center>There are currently no News</div>";
}
@mysqli_free_result($result);

get_footer(); #defaults to theme footer or footer_inc.php
?>
