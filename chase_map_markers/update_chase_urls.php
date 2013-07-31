<?php
/**
 * Script for updating `Mobile`.`MapMarkerAd` table data.
 */

define('HTTP_DOCROOT', '/data/home/nroberts/public_html');
define('PHPEXCEL_INCLUDE_PATH', HTTP_DOCROOT.'/PHPExcel');
define('WEBSERVICE_INCLUDE_PATH', HTTP_DOCROOT. '/webservice');
define('SCRIPTS_INCLUDE_PATH', HTTP_DOCROOT.'/scripts');
define('IMPORT_PATH', SCRIPTS_INCLUDE_PATH.'/import');

require_once(WEBSERVICE_INCLUDE_PATH.'/bootstrap.php');
require_once(PHPEXCEL_INCLUDE_PATH.'/PHPExcel.php');
require_once(WEBSERVICE_INCLUDE_PATH.'/include/MapMarkerAd.php');
require_once(COMMON_INCLUDE_PATH.'/Geo/GeocoderAddressUtils.php');

// ticket to use in comments field
$jiraTicket = 'API-891';

// file to output to
$timestamp = date('Ymd');
$outfile = '/tmp/' . sprintf('mapmarkerad_update_%s.%s.sql', $vendor, $timestamp);

// you shouldn't need to modify these lines as they won't change
$vendor = 'chase';
$vendor_label = 'Chase';
$vendor_template = 'chase';
$vendor_image_marker = 'mkr_chase_white.png';

echo("Generating SQL for $vendor...\n");

$input_filename = IMPORT_PATH . '/Trulia_PROD_0604.xlsx';
echo("XLS file to use: $input_filename\n");


$PHPExcelObj = PHPExcel_IOFactory::load($input_filename);
$CurrentSheet = $PHPExcelObj->getActiveSheet();


$sqlScript = "USE `Mobile`;\n";
$sqlScript .= "DELETE FROM `MobileMarkerAd` WHERE LOWER(`label`) = '$vendor';\n";

$insertSqlStatements = array();

foreach ($CurrentSheet->getRowIterator() as $Row)
{
  // Ignore the row with all the column headers
  if ($Row->getRowIndex() !== 1)
  {
    $MapMarker = new MapMarkerAd();
    $MapMarker->setAdId($Row->getRowIndex());
    $MapMarker->setAdvertiserId(1);
    foreach ($Row->getCellIterator() as $Cell)
    {
      switch ($Cell->getColumn())
      {
        case 'B': // Name
          
            $payload['name'] = $Cell->getValue();
            break;
        
        case 'E':// Address
          
          $loc_info = GeocoderAddressUtils::parse_address($Cell->getValue());
          if (!empty($loc_info['latitude_f']))
          {
            $MapMarker->setLatitude($loc_info['latitude_f']);
          }
          if (!empty($loc_info['longitude_f']))
          {
            $MapMarker->setLongitude($loc_info['longitude_f']);
          }
          break;
          
        case 'F': // Phone
          
          $payload['phone'] = $Cell->getValue();
          break;
      
        case 'J': // Website
          
          $cachebuster = '' . mt_rand();
          $redirectUrl = 'http://homeloan.chase.com/' . $Cell->getValue();
          $urlCta = 'http://clk.medialytics.com/href?0.type=i&0.key=MMAdClickthrough&tagID=72280aa11d12535fabca6d77f6a4c509&impunique=[IMP_UNIQUE]&r=' . $cachebuster . '%3f' . $redirectUrl;
          $MapMarker->setUrlCta($urlCta);
          break;
      
        case 'K': // Image
          
          $imageUrl = $Cell->getValue();
          $payload['image'] = $imageUrl;
          $MapMarker->setPrefetch(json_encode(array($imageUrl)));
          break;
      }
      
    }
    
    $MapMarker->setLabel($vendor_label);
    $MapMarker->setOrderValue($Row->getRowIndex());
    $MapMarker->setEnabled(1);
    $MapMarker->setTemplateName($vendor_template);
    $MapMarker->setUrlImageMarker($vendor_image_marker);
    $MapMarker->setPayload(json_encode($payload));
    $MapMarker->setTargetCta('internal');
    
    $cachebuster = '' . mt_rand();
    $urlImpressionMarker = 'http://tag.medialytics.com/tag?tagID=fa2a7bb6779f2f18490236a3c8b99cc2&type=p&impunique=[IMP_UNIQUE]&r=' . $cachebuster;
    $MapMarker->setUrlImpressionMarker($urlImpressionMarker);
    
    $cachebuster = '' . mt_rand();
    $urlImpressionCallout = 'http://tag.medialytics.com/tag?tagID=31b12cb7ccf6fa67649cc8be71e40ad3&type=p&impunique=[IMP_UNIQUE]&r=' . $cachebuster;
    $MapMarker->setUrlImpressionCallout($urlImpressionCallout);
    
    
    // dumping insert statement
    $MapMarker->setDumpSql(true);
    
    $insertSqlStatements[] = $MapMarker->save($MapMarker);
    
  }
  
  
}

echo("Generating SQL file...\n");

$sql = "
SELECT \"Running SQL update for $jiraTicket.\", NOW();

USE `Mobile`;
DELETE FROM `MapMarkerAd` WHERE LOWER(`label`) = 'chase';    
";

$lineDelim = ";\n";
$sql .= implode($lineDelim, $insertSqlStatements) . $lineDelim;


if(file_exists($outfile))
{
  unlink($outfile);
}


$fh = fopen($outfile, 'w');
fwrite($fh, $sql);

echo("Completed.\n");



