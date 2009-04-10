<?php

// TITLE:  BinarySearch.php
// TYPE:   Class
// AUTHOR: Ryan Lin
// DATE:   02/26/2009
// ABOUT:  Binary search on an array.  Used to speed up searching through our word
//         lists and the entire English dictionary.
// ================================================================================

class BinarySearch
{

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// CLASS FIELD MEMBERS ........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	private $myArray;
	
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// PUBLIC FUNCTIONS ...........................................................
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	
	// ========================================================================
	// BinarySearch
	//    args:  * a 1-D array whose values have the <, >, and == operators 
	//             overloaded.
	//           * is your array already sorted or not?
	//    ret:   void
	//    about: Constructor.
	// ------------------------------------------------------------------------	
	public function BinarySearch($array, $alreadySorted = true)
	{
		$this->myArray = $array;
		
		if(!$alreadySorted)
		{
			if(!sort($this->myArray))
				die('BinarySearch->BinarySearch(): error sorting array');
		}
	}
	
	// ========================================================================
	// findIndex
	//    args:  mixed - what are you looking for in the array?
	//    ret:   int - the index of the item if it is found, else -1.
	//    about: Note that this is the index after the array has already been 
	//           sorted by the constructor. 
	// ------------------------------------------------------------------------	
	public function findIndex($lookFor)
	{
		$indexLow = 0;
		$indexHigh = count($this->myArray) - 1;
		
		while($indexLow <= $indexHigh)
		{
			$indexMid = ceil(($indexLow + $indexHigh) / 2);
		
			if($this->myArray[$indexMid] > $lookFor)
				$indexHigh = $indexMid - 1;
			else if($this->myArray[$indexMid] < $lookFor)
				$indexLow = $indexMid + 1;
			else 
				return $indexMid;
		}
		
		return -1;
	}	
	
	// ========================================================================
	// inArray
	//    args:  mixed - what are you looking for in the array?
	//    ret:   bool - is your item in the array or not?
	//    about: Determines whether your item is located in the array or not.
	// ------------------------------------------------------------------------	
	public function inArray($lookFor)
	{
		if($this->findIndex($lookFor) != -1)
			return true;
		else 
			return false;
	}
	
}

?>