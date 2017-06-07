<?php
//news.php
namespace NewsRSS;


class News
{

  public $NewsID = 0;
  public $Title = "";
  public $Description = "";
  public $isValid = FALSE;
  public $TotalNews = 0; #stores number of questions
  protected $aCategories = Array();#stores an array of question objects

function __construct($id)
{
  $this->NewsID = (int)$id;
  if ($this->NewsID == 0){return FALSE;
  }

  $sql = sprintf("select Title,Description from " . PREFIX . "news Where NewsID =%d",$this->NewsID);

  #in mysqli, connection and query are reversed!  connection comes first
  $result = mysqli_query(\IDB::conn(),$sql) or die(trigger_error(mysqli_error(\IDB::conn()), E_USER_ERROR));
  if (mysqli_num_rows($result) > 0)
  {#Must be a valid survey!
    $this->isValid = TRUE;
    while ($row = mysqli_fetch_assoc($result))
    {#dbOut() function is a 'wrapper' designed to strip slashes, etc. of data leaving db
         $this->Title = dbOut($row['Title']);
         $this->Description = dbOut($row['Description']);
    }
  }
  @mysqli_free_result($result); #free resources

  if(!$this->isValid){return;}  #exit, as Survey is not valid

  #attempt to create question objects
  $sql = sprintf("select CategoriesID, Categories, Description from " . PREFIX . "questions where NewsID =%d",$this->NewsID);
  $result = mysqli_query(\IDB::conn(),$sql) or die(trigger_error(mysqli_error(\IDB::conn()), E_USER_ERROR));
  if (mysqli_num_rows($result) > 0)
  {#show results
     while ($row = mysqli_fetch_assoc($result))
     {
      #create question, and push onto stack!
      $this->aCategories[] = new Categories(dbOut($row['CategoriesID']),dbOut($row['Categories']),dbOut($row['Description']));
     }
  }
  $this->TotalNews = count($this->aCategories); //the count of the aQuestion array is the total number of questions
  @mysqli_free_result($result); #free resources


  $sql = "SELECT * FROM news";
  $sql = sprintf($sql,$this->NewsID); #process SQL
  $result = mysqli_query(\IDB::conn(),$sql) or die(trigger_error(mysqli_error(\IDB::conn()), E_USER_ERROR));
  if (mysqli_num_rows($result) > 0)
  {#at least one Feed!
   while ($row = mysqli_fetch_assoc($result))
   {#match Feeds to questions
      $CategoriesID = (int)$row['CategoriesID']; #process db var
    foreach($this->aCategories as $Categories)
    {#Check db questionID against Question Object ID
      if($Categories->CategoriesID == $CategoriesID)
      {
        $Categories->TotalFeed += 1;  #increment total number of Feeds
        #create Feed, and push onto stack!
        $Categories->aFeed[] = new Feed((int)$row['FeedID'],dbOut($row['Feed']),dbOut($row['Description']));
        break;
      }
    }
   }
  }

}



function showCategories()
{
  if($this->TotalNews > 0)
  {#be certain there are questions
echo '<div class="panel panel-success">';
    foreach($this->aCategories as $Categories)
    {#print data for each
      echo '<div class="panel-heading">' . $Categories->Text . '</div>';
      #call showFeeds() method to display array of Feed objects
echo '<div class="panel-body">';
      $Categories->showFeed();
    echo '</div>';
    }
    echo '</div>';
  }else{
    echo "There are currently no Categories.";
  }
}# end showQuestions() method

}

?>
