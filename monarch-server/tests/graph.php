<?php

if(!$_GET['website'] || !$_GET['keyword'])
	die('usage: graph.php?website=nytimespolitics&keyword=obama');

require_once('../database/Database.php');

$database = new Database($_GET['website']);

$q = 'SELECT s.goodness AS Ysentiment, (s.englishProficiency / s.count) AS YenglishProficiency, s.count AS Ycount, s.time AS Xtime
	FROM keywords AS k, keywordstats AS ks, stats AS s
	WHERE k.word = "' . $_GET['keyword'] . '"
	AND k.id = ks.keyword
	AND ks.stat = s.id ORDER BY
	Xtime ASC';
	
$q = $database->query($q);

$count     = array();
$english   = array();
$sentiment = array();

while($plotPoint = mysql_fetch_array($q))
{
	$count[]     = $plotPoint['Ycount'];
	$sentiment[] = $plotPoint['Ysentiment'];
	$english[]   = $plotPoint['YenglishProficiency'];
}

 // Standard inclusions   
 include("pChart/pChart/pData.class");
 include("pChart/pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint($count    , "Serie1");
 $DataSet->AddPoint($sentiment, "Serie2");
 $DataSet->AddPoint($english  , "Serie3");

 $DataSet->AddAllSeries();
 $DataSet->SetAbsciseLabelSerie();
 $DataSet->SetSerieName("count"    , "Serie1");
 $DataSet->SetSerieName("sentiment", "Serie2");
 $DataSet->SetSerieName("english"  , "Serie3");

	$width = 2400;
	$height = 1000;

 // Initialise the graph
 $Test = new pChart($width, $height); // 230
 $Test->setFontProperties("pChart/Fonts/tahoma.ttf",8);
 $Test->setGraphArea(50,30,$width - 100,$height - 30);
 $Test->drawFilledRoundedRectangle(7,7,$width - 50,$height - 7,5,$height + 10,$height + 10,$height + 10);
 $Test->drawRoundedRectangle(5,5,$width - 48,$height - 5,5,$height,$height,$height);
 $Test->drawGraphArea($height - 5,$height - 5,$height - 5,TRUE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
 $Test->drawGrid(4,TRUE,$height,$height,$height,50);

 // Draw the 0 line
 $Test->setFontProperties("pChart/Fonts/tahoma.ttf",6);
 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

 // Draw the line graph
 $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,$height + 15,$height + 15,$height + 15);

 // Finish the graph
 $Test->setFontProperties("pChart/Fonts/tahoma.ttf",8);
 $Test->drawLegend(100,30,$DataSet->GetDataDescription(),$height + 15,$height + 15,$height + 15);
 $Test->setFontProperties("pChart/Fonts/tahoma.ttf",10);
 $Test->drawTitle(50,22, 'Stat VS Time' ,50,50,50,585);
 $Test->Render("graph.png");
 
 echo '<img src="graph.png">';
 
?>