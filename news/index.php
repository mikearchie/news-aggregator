<?php
// '../' works for a sub-folder.  use './' for the root
require '../inc_0700/config_inc.php'; //provides configuration, pathing, error handling, db credentials

// SQL statement
$sql = "select * FROM sp17_NewsCategories";

// Fills <title> tag. If left empty will default to $PageTitle in config_inc.php
$config->titleTag = 'News RSS';

// Fills <meta> tags.  Currently we're adding to the existing meta tags in config_inc.php
// $config->metaDescription = 'Seattle Central\'s ITC250 Class Surveys are made with pure PHP! ' . $config->metaDescription;
// $config->metaKeywords = 'Surveys,PHP,Fun,Bran,Regular,Regular Expressions,'. $config->metaKeywords;

// END CONFIG AREA ----------------------------------------------------------
get_header(); //defaults to theme header or header_inc.php
?>
<h3 align="center"><?=smartTitle();?></h3>
<div class="table-responsive">
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>Category</th>
			</tr>
		</thead>
<?php
// reference images for pager
$prev = '<img src="' . VIRTUAL_PATH . 'images/arrow_prev.gif" border="0" />';
$next = '<img src="' . VIRTUAL_PATH . 'images/arrow_next.gif" border="0" />';
		
// create instance of new 'pager' class
$myPager = new Pager(10, '', $prev, $next, '');
$sql = $myPager->loadSQL($sql);  // load SQL, add offset
		
// connection comes first in mysqli (improved) function
$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
		
if (mysqli_num_rows($result) > 0) { // records exist - process
	if ($myPager->showTotal() == 1) {
		$itemz = "category";
	} else {
		$itemz = "categories";
	} // deal with plural
	
    echo '<div align="center">We have ' . $myPager->showTotal() . ' news ' . $itemz . '!</div>';
	echo '<tbody>';
	while ($row = mysqli_fetch_assoc($result)) { // process each row
        echo '
			<tr> 
				<td><a href="' . VIRTUAL_PATH . 'news/category_view.php?id=' . (int)$row['CategoryID'] . '">' . dbOut($row['Category']) . '</a></td>
			</tr>';
	}
	echo '
			</tbody>
		</table>
	</div>';
	
	echo $myPager->showNAV(); // show paging nav, only if enough records
} else { // no records
    echo "<div align=center>There are currently no news categories available.</div>";
}

@mysqli_free_result($result);
get_footer(); // defaults to theme footer or footer_inc.php