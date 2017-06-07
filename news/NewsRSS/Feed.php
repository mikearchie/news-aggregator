<?php
//Answer.php
namespace NewsRSS;

class Feed
{
	 public $FeedID = 0;
	 public $Text = "";
	 public $Description = "";
	/**
	 * Constructor for Answer class.
	 *
	 * @param integer $AnswerID ID number of answer
	 * @param string $Text The text of the answer
	 * @param string $Description Additional description info
	 * @return void
	 * @todo none
	 */
    function __construct($FeedID,$Feed,$description)
	{#constructor sets stage by adding data to an instance of the object
		$this->FeedID = (int)$FeedID;
		$this->Text = $Feed;
		$this->Description = $description;
	}#end Answer() constructor
}#end Answer class
