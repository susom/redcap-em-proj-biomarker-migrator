<?php


namespace Stanford\ProjBiomarkerMigrator;



class DataCheck
{

    //TODO: this should be exposed in config so that it can be done in fly.
    private static $checker = array(
        //June: converted to text so that Jaynelle can convert later
        //"missed_school"=>'/(?<find>\b([0-9]|1[0-9]|20)\b)$/',  //only allow numbers (since it's an integer field
        //'sympsib_v2'      => '/(?<find>\b([01]\b))/',
        //'gi_new'          => '/(?<find>^([0-9]|[1-9]\d|100)$)/',
        //'day_abscess'             => '/(?<find>\b([2|3|98|99]\b))/',
        'sft_ra'                  => '/(?<find>\b(\d+\b))/',
        'c1_sft_waist'            => '/(?<find>\b(\d+\b))/',
        'age_yr_pg'               => '/(?<find>\b(\d+\b))/',
        'weight'                  => '/(?<find>\b(^[0-9]*\.?[0-9]*$\b))/', //numbers only, decimals okay
        'sft_waist'               => '/(?<find>\b(^[0-9]*\.?[0-9]*$\b))/', //numbers only, decimals okay
        'circum_waist'            => '/(?<find>\b(^[0-9]*\.?[0-9]*$\b))/', //numbers only, decimals okay
        'moyr_c1'                 =>'/(?<find>[01]?[0-9][\/][0-9][0-9]$)/',
        'hours_workstudy_pg'      => '/(?<find>\b(^[0-9]*\.?[0-9]*$\b))/', //numbers only, decimals okay
        'growth'                 => '/(?<find>\b(^[0-9]*\.?[0-9]*$\b))/', //numbers only, decimals okay
        'num_adults_family'          => '/(?<find>\b(\d+\b))/' //number only
    );

    public static function valueValid($field, $val) {
        global $module;

        if (array_key_exists($field, self::$checker)) {
            //$module->emDebug("this key is about to be checked:  $field with regex: " . self::$checker[$field]);

            $reg = self::$checker[$field];
            preg_match_all($reg, trim($val), $matches, PREG_SET_ORDER, 0);

            $found = ($matches[0])['find'];
            if ($found != '') {
                return true;
            } else {
                $module->emDebug("DATA CHECKER FAIL: this value for field $field is not valid. NOT ENTERED:  <$val>");
                //$module->emDebug(isset($matches[0]['find']), $matches);
                return false;
            }
        }
        return true;
    }
}