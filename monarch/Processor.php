<?php


  // Processor.php - Written by Andrew Spencer, 2008
  // -----------------------------------------------------------------------------
  // Defines the Processor interface. Any class that implements this interface may
  // be used as a callback for a level of pages in a StructuredCrawl instance.


interface Processor {
  public function process($html);
}

?>

