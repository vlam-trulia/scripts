<?php

require_once('/webdev/webservice/include/shortmap.config.inc');

$out = '';
$out .= format_fields('search-input', $search_input);
$out .= format_fields('search-output', $search_output);
$out .= format_fields('detail-input', $detail_input);
$out .= format_fields('detail-output', $detail_output);
$out .= format_fields('detail-output2', $detail_output_v2);


$outfile = dirname(__FILE__) . '/shortmap_conf.txt';


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
