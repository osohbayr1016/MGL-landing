<?php
class gridViewClass
{
	public $conn;
	var $tableClass;
	public $gridFieldArr = array();
	
	
	function setFieldValue($indx,$value,$tabIndx,$cssClass, $selectArr="", $fieldTyp="inp", $moreTxt="", $moreLink="" )
    {
		$this->gridFieldArr[$indx]['value']=$value;
		$this->gridFieldArr[$indx]['tabindx']=$tabIndx;
		$this->gridFieldArr[$indx]['class']=$cssClass;
		$this->gridFieldArr[$indx]['exp']=$expTd;
		$this->gridFieldArr[$indx]['moreLink']=$moreLink;
		$this->gridFieldArr[$indx]['moreTxt']=$moreTxt;
		$this->gridFieldArr[$indx]['inpTyp']=$fieldTyp;
		
		if($selectArr!=''){
			$this->gridFieldArr[$indx]['selectArr']=$selectArr;
			if($fieldTyp=="inp")
				$this->gridFieldArr[$indx]['inpTyp']="select";
		}
			
    }
	
}

?>