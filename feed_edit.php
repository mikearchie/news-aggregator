<?php
/**
 * demo_edit.php is a single page web application that allows us to select a customer
 * and edit their data
 *
 * This page is based on demo_postback.php as well as first_crud.php, which is part of the
 * nmPreload package
 *
 * Any number of additional steps or processes can be added by adding keywords to the switch
 * statement and identifying a hidden form field in the previous step's form:
 *
 *<code>
 * <input type="hidden" name="act" value="next" />
 *</code>
 *
 * The above code shows the parameter "act" being loaded with the value "next" which would be the
 * unique identifier for the next step of a multi-step process
 *
 * @package nmCommon
 * @author Bill Newman <williamnewman@gmail.com>
 * @version 1.12 2012/02/27
 * @link http://www.newmanix.com/
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License ("OSL") v. 3.0
 * @todo add more complicated checkbox & radio button examples
 */

# '../' works for a sub-folder.  use './' for the root
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials
require_once INCLUDE_PATH . 'admin_only_inc.php';

/*
$config->metaDescription = 'Web Database ITC281 class website.'; #Fills <meta> tags.
$config->metaKeywords = 'SCCC,Seattle Central,ITC281,database,mysql,php';
$config->metaRobots = 'no index, no follow';
$config->loadhead = ''; #load page specific JS
$config->banner = ''; #goes inside header
$config->copyright = ''; #goes inside footer
$config->sidebar1 = ''; #goes inside left side of page
$config->sidebar2 = ''; #goes inside right side of page
$config->nav1["page.php"] = "New Page!"; #add a new page to end of nav1 (viewable this page only)!!
$config->nav1 = array("page.php"=>"New Page!") + $config->nav1; #add a new page to beginning of nav1 (viewable this page only)!!
*/

//END CONFIG AREA ----------------------------------------------------------

# Read the value of 'action' whether it is passed via $_POST or $_GET with $_REQUEST
if(isset($_REQUEST['act'])){$myAction = (trim($_REQUEST['act']));}else{$myAction = "";}

switch ($myAction)
{//check 'act' for type of process
	case "edit": //2) show first name change form
	 	editDisplay();
	 	break;
	case "update": //3) Change customer's first name
		updateExecute();
		break;
	default: //1)Select Customer from list
	 	selectFirst();
}

function selectFirst()
{//Select Customer
	global $config;
	$config->loadhead .= '<script type="text/javascript" src="' . VIRTUAL_PATH . 'include/util.js"></script>
	<script type="text/javascript">
			function checkForm(thisForm)
			{//check form data for valid info
        if(empty(thisForm.FeedTitle,"Please Enter FeedTitle")){return false;}
  			if(empty(thisForm.CategoryID,"Please Enter CategoryID")){return false;}
  			if(empty(thisForm.Description ,"Please Enter Description ")){return false;}
  			if(empty(thisForm.URL,"Please Enter URL ")){return false;}
				return true;//if all is passed, submit!
			}
	</script>
	';
	get_header();
	echo '<h3 align="center">' . smartTitle() . '</h3>';

	$sql = "select FeedID,FeedTitle,CategoryID,Description,URL from sp17_NewsFeeds";
	$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0)//at least one record!
	{//show results
		echo '<form action="' . THIS_PAGE . '" method="post" onsubmit="return checkForm(this);">';  //TWO COPIES OF THIS LINE IN ORIG!!
		echo '<table align="center" border="1" style="border-collapse:collapse" cellpadding="3" cellspacing="3">';
		echo '<tr>
        <th>FeedID</th>
        <th>FeedTitle</th>
        <th>CategoryID</th>
        <th>Description</th>
        <th>URL </th>
			</tr>
			';
		while ($row = mysqli_fetch_assoc($result))
		{//dbOut() function is a 'wrapper' designed to strip slashes, etc. of data leaving db
			echo '<tr>
					<td>
				 	<input type="radio" name="FeedID" value="' . (int)$row['FeedID'] . '">'
          . (int)$row['FeedID'] . '</td>
        <td>' . dbOut($row['FeedTitle']) . '</td>
        <td>' . dbOut($row['CategoryID']) . '</td>
        <td>' . dbOut($row['Description']) . '</td>
        <td>' . dbOut($row['URL']) . '</td>
				</tr>
				';
		}
		echo '<input type="hidden" name="act" value="edit" />';
		echo '<tr>
				<td align="center" colspan="4">
					<input type="submit" value="Choose Feed!"></em>
				</td>
			  </tr>
			  </table>
			  </form>
			  ';
	}else{//no records
      echo '<div align="center"><h3>Currently No News in Database.</h3></div>';
	}
	@mysqli_free_result($result); //free resources
	get_footer();
}

function editDisplay()
{# shows details from a single customer, and preloads their first name in a form.
	global $config;
	if(!is_numeric($_POST['FeedID']))
	{//data must be alphanumeric only
		feedback("id passed was not a number. (error code #" . createErrorCode(THIS_PAGE,__LINE__) . ")","error");
		myRedirect(THIS_PAGE);
	}


	$myID = (int)$_POST['FeedID'];  //forcibly convert to integer

	$sql = sprintf("select FeedID,FeedTitle,CategoryID,Description,URL from sp17_NewsFeeds WHERE FeedID=%d",$myID);

  $result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
	if(mysqli_num_rows($result) > 0)//at least one record!
	{//show results
		while ($row = mysqli_fetch_array($result))
		{//dbOut() function is a 'wrapper' designed to strip slashes, etc. of data leaving db
		     $FeedID = dbOut($row['FeedID']);
		     $FeedTitle = dbOut($row['FeedTitle']);
		     $CategoryID = dbOut($row['CategoryID']);
		     $Description = dbOut($row['Description']);
         $URL = dbOut($row['URL']);

		}
	}else{//no records
      //feedback issue to user/developer
      feedback("No such Feeds. (error code #" . createErrorCode(THIS_PAGE,__LINE__) . ")","error");
	  myRedirect(THIS_PAGE);
	}

	$config->loadhead .= '
	<script type="text/javascript" src="' . VIRTUAL_PATH . 'include/util.js"></script>
	<script type="text/javascript">
		function checkForm(thisForm)
		{//check form data for valid info
      if(empty(thisForm.FeedTitle,"Please Enter FeedTitle")){return false;}
      if(empty(thisForm.CategoryID,"Please Enter CategoryID")){return false;}
      if(empty(thisForm.Description ,"Please Enter Description ")){return false;}
      if(empty(thisForm.URL,"Please Enter URL ")){return false;}
			return true;//if all is passed, submit!
		}
	</script>';

	get_header();
	echo '<h3 align="center">' . smartTitle() . '</h3>
	<h4 align="center">Update Feed/h4>
	<p align="center">
  Feed: <font color="red"><b>' . $FeedID . '</b>
	FeedTitle: <font color="red"><b>' . $FeedTitle . '</b></font>
	<form action="' . THIS_PAGE . '" method="post" onsubmit="return checkForm(this);">

  <table align="center">
	   <tr><td align="right">Feed Title</td>
		   	<td>
		   		<input type="text" name="FeedTitle" value="' .  $FeedTitle . '">
		   		<font color="red"><b>*</b></font> <em>(alphanumerics & punctuation)</em>
		   	</td>
	   </tr>
	   <tr><td align="right">CategoryID</td>
		   	<td>
		   		<input type="text" name="CategoryID" value="' .  $CategoryID . '">
		   		<font color="red"><b>*</b></font> <em>(number)</em>
		   	</td>
	   </tr>
	   <tr><td align="right">Description</td>
		   	<td>
		   		<input type="text" name="Description" value="' .  $Description . '">
		   		<font color="red"><b>*</b></font> <em>(alphanumerics & punctuation)</em>
		   	</td>
	   </tr>
     <tr><td align="right">URL</td>
		   	<td>
		   		<input type="text" name="URL" value="' .  $URL . '">
		   		<font color="red"><b>*</b></font> <em>(valid URL only)</em>
		   	</td>
	   </tr>
	   <input type="hidden" name="FeedID" value="' . $myID . '" />
	   <input type="hidden" name="act" value="update" />
	   <tr>
	   		<td align="center" colspan="2">
	   			<input type="submit" value="Update Info!"><em>(<font color="red"><b>*</b> required field</font>)</em>
	   		</td>
	   </tr>
	</table>
	</form>
	<div align="center"><a href="' . THIS_PAGE . '">Exit Without Update</a></div>
	';
	@mysqli_free_result($result); //free resources
	get_footer();

}

function updateExecute()
{
	if(!is_numeric($_POST['FeedID']))
	{//data must be alphanumeric only
		feedback("id passed was not a number. (error code #" . createErrorCode(THIS_PAGE,__LINE__) . ")","error");
		myRedirect(THIS_PAGE);
	}




	$iConn = IDB::conn();//must have DB as variable to pass to mysqli_real_escape() via iformReq()


	$redirect = THIS_PAGE; //global var used for following formReq redirection on failure

	$FeedID = iformReq('FeedID',$iConn); //calls mysqli_real_escape() internally, to check form data
  $FeedTitle = strip_tags(iformReq('FeedTitle',$iConn));
	$CategoryID = strip_tags(iformReq('CategoryID',$iConn));
	$Description = strip_tags(iformReq('Description',$iConn));
	$URL = strip_tags(iformReq('URL',$iConn));

	//next check for specific issues with data
	// if(!ctype_graph($_POST['FeedTitle'])|| !ctype_graph($_POST['Description']))
	// {//data must be alphanumeric or punctuation only
	// 	feedback("Title and Discription must contain letters, numbers or punctuation","warning");
	// 	myRedirect(THIS_PAGE);
	// }


	// if(!onlyEmail($_POST['Email']))
	// {//data must be alphanumeric or punctuation only
	// 	feedback("Data entered for email is not valid","warning");
	// 	myRedirect(THIS_PAGE);
	// }

    //build string for SQL insert with replacement vars, %s for string, %d for digits
    // $sql = "UPDATE sp17_NewsFeeds set
    // FeedTitle='%s',
    // CategoryID='%d',
    // Description='%s',
    // URL='%s',
    //  WHERE FeedID=%d"
    //  ;
    //

  $sql=" UPDATE sp17_NewsFeeds SET
FeedTitle = '%s',
CategoryID = '%d',
Description = '%s',
URL = '%s'
WHERE FeedID = '%d' ";


  //  $sql = "UPDATE sp17_NewsFeeds (FeedTitle, CategoryID, Description, URL) VALUES (%s','%d','%s','%s')";




    # sprintf() allows us to filter (parameterize) form data
	$sql = sprintf($sql,$FeedTitle,$CategoryID,$Description,$URL,(int)$FeedID);

	@mysqli_query($iConn,$sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	#feedback success or failure of update
	if (mysqli_affected_rows($iConn) > 0)
	{//success!  provide feedback, chance to change another!
	 feedback("Data Updated Successfully!","success");

	}else{//Problem!  Provide feedback!
	 feedback("Data NOT changed!","warning");
	}
	myRedirect(THIS_PAGE);
}
