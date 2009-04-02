<?php
// Url.php - created by Andrew Spencer, March 2009

// URL
//  - class for representing a url within the context of a structured crawl
class URL {
  private $name;    // The string representation of this URL
  private $level;   // The level that this URL appears in
  private $crawled; // Boolean telling whether or not this page has been crawled
  private $parentURL; // TODO: refine definition

  public function URL($n, $l, $p) {
    $this->name = $n;
    $this->level = $l;
    $this->parentURL = $p;
    $this->crawled = false;
  }

  public function getLevel() {
    return $this->level;
  }

  public function getName() {
    return $this->name;
  }

  public function getParentURLName() {
    return $this->parentURL;
  }

  public function setCrawled() {
    $this->crawled = true;
  }

  public function isCrawled() {
    return $this->crawled;
  }

  public function output() {
    echo $this->name . '<br/>' . $this->level . '<br/>';
  }

  
  // ========================================================================
  // translateURLBasedOnCurrent 
  //    args:  string - the raw url to translate
  //           string - the name of the current url to use in translation
  //    ret:   string - the translated url 
  //    about: Class method that translates a url
  // ------------------------------------------------------------------------	
  public static function translateURLBasedOnCurrent($u_name, $curr_url_name) {
    // Breaks down to 3 cases
    // 1) absolute : we can return the link directly
    // 2) relative_special : (e.g. '/next/blah.html') get base url and append u_name
    //      to that
    // 3) relative : (e.g. '../this/that' or 'that/this'); can be appended to 
    //      current url.
    if(preg_match('/http/',$u_name)) { // case 1
      // echo 'Case 1 <br/>';
      return $u_name;
    }
    else if(preg_match('#^/#',$u_name)) { // case 2
      preg_match('#(http://[^/]+)/#',$curr_url_name, $match);
      // echo 'Case 2 <br/>';
      // print_r($match);
      return $match[1] . $u_name;
    }
    else { // case 3
      // echo 'Case 3 <br/>';
      return $curr_url_name . $u_name;
    }
  }
}

// URLType
//  - class for representing a type of url (thread-url, post-url, etc.) within the context of
//     a structured crawl
class URLType {
  private $regex; // The regular expression matching this URLType
  private $level; // The level that this url has in the page hierarchy

  public function URLType($reg_ex, $found_in_level) {
    $this->regex = $reg_ex;
    $this->level = $found_in_level;
  }

  // ========================================================================
  // getMatches 
  //    args:  string - the source string to find matches in
  //    ret:   arr - array of string matches
  //    about: Returns all urls having the url type of this object
  // ------------------------------------------------------------------------	
  public function getMatches($str) {
    // echo 'Getting matches for '.$this->regex;
    preg_match_all($this->regex, $str,$match_arr);
    // print_r($match_arr);
    return $match_arr[1];
  }

  public function getLevel() {
    return $this->level;
  }

  public function output() {
    echo htmlspecialchars($this->regex) . '<br/>' . $this->level . '<br/>';
  }
}
?>
