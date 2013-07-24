<?php

require_once('/webdev/webservice/include/api.conf.php');

$out = '';
$out .= format_fields('all-fields', $all_fields);


$outfile = dirname(__FILE__) . '/api_conf.txt';


if(file_exists($outfile))
{
  unlink($outfile);
}

$hdl = fopen($outfile, 'w');
fwrite($hdl, $out);



function format_label($label)
{
  return "\n-----------------------------------------------\n$label\n-----------------------------------------------\n";
}

function format_fields($label, $fields)
{
  
  $out = format_label($label);
  
  ksort($fields);
  foreach($fields as $key => $value)
  {
    $out .= format_field($key, $value);
  }
  
  return $out;

}

function format_field($key, $value, $delim = "\t")
{
  return $key . $delim . $value . "\n";
}
