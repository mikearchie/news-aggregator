<?php
//Categories.php
namespace NewsRSS;

class Categories
{
	 public $CategoriesID = 0;
	 public $Text = "";
	 public $Description = "";
	 public $aFeed = Array();#stores an array of answer objects
	 public $TotalFeed = 0;
	/**
	 * Constructor for Question class.
	 *
	 * @param integer $id ID number of question
	 * @param string $question The text of the question
	 * @param string $description Additional description info
	 * @return void
     * @todo none
	 */
    function __construct($id,$Categories,$description)
	{#constructor sets stage by adding data to an instance of the object
		$this->CategoriesID = (int)$id;
		$this->Text = $Categories;
		$this->Description = $description;
	}# end Question() constructor

	/**
	 * Reveals answers in internal Array of Answer Objects
	 * for each question
	 *
	 * @param none
	 * @return string prints data from Answer Array
	 * @todo none
	 */
	function showFeed()
	{
		if($this->TotalFeed != 1){$s = 's';}else{$s = '';} #add 's' only if NOT one!!
		echo "<em>[" . $this->TotalFeed . " Feed" . $s . "]</em> ";
		foreach($this->aFeed as $Feed)
		{#print data for each
			echo "<em>(" . $Feed->FeedID . ")</em> ";
			echo $Feed->Text . " ";
			if($Feed->Description != "")
			{#only print description if not empty
				echo "<em>(" . $Feed->Description . ")</em>";
			}
		}
		print "<br />";
	}#end showAnswers() method
}# end Question class
