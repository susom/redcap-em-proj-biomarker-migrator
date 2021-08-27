<?php
namespace Stanford\ProjBiomarkerMigrator;
/** @var ProjBiomarkerMigrator $module */

use REDCap;
echo "testing saveData and getdata as array";



$pid = 346;
//$q = REDCap::getData($pid,'array',array("325"),NULL, 2);
//$module->emDebug($q);

/**
 * getData gets as array with this format:
 *
 * [record_id]
 *     [event_id]
 *         'record_id'='1'
 *         'checkbox'=array(1="foo")
 */

//record_id = 2
//event_id = 2287 : parent 1
//event_id = 2288 : parent 2
//event_id = 2281 : child 1


$save_data[2][2287]=array(
    'age_yr_pg1'=>45,
    'martial_status_pg1'=>array("1"=>"0", "2"=>"1","3"=>"0","4"=>"0","5"=>"0","6"=>"0"),
    'student_pg1'=>0
);

//here's the failing save record
$fail_save_data[2][2288]=array(
    'age_yr_pg1'=>45,
    'martial_status_pg1'=>array(1=>0, 2=>1,3=>0,4=>0,5=>0,6=>0),
    'studen_pg1'=>0


);
$return = REDCap::saveData($pid, 'array', $save_data);
$module->emDebug($return);



