<?php


namespace Stanford\ProjBiomarkerMigrator;

use REDCap;


class Mapper
{

    const LAST_ROW = 17600; //5000 if not testing
    public const MAP_ROWS = 2000; //5000 if not testing

    private $data_dict;
    private $to_data_dict;
    private $map;
    private $repeating_forms;
    private $header;

    /**
     * [treatment_other_ep1] => Array (
     *      [from_field] => treatment_other_ep1
            [to_repeat_event] =>
            [to_form_instance] => episode:1
            [to_field] => ep_treatment_other
            [custom] =>
            [form_name] =>
            [notes] =>
            [from_fieldtype] => notes
            [from_form] => pans_patient_questionnaire
     */


    /**
     *
     * Mapper constructor.
     * @param $origin_pid
     * @param $file
     *
     */
    public function __construct($origin_pid, $file) {
        global $module;
        //$this->data_dict = REDCap::getDataDictionary($origin_pid, 'array', false );
        $this->data_dict = $module->getMetadata($origin_pid);

        //add current sequence of data_dict

        //$module->emDebug($this->data_dict); exit;

        $this->to_data_dict = $module->getMetadata($module->getProjectId());
        //$module->emDebug($module->getProjectId(), $this->data_dict);
        //$module->emDebug($origin_pid, $this->to_data_dict); exit;

        $this->setCurrentSequence();

        //$module->emDebug($this->data_dict);

        $this->map = $this->createMap($file);

        $this->repeating_forms = $this->getUniqueRepeatingForms();

        //$module->emDebug($this->repeating_forms);
    }


    private function setCurrentSequence() {
        global $module;
        $i = 1;
        foreach ($this->to_data_dict as $key => $val) {

            $this->to_data_dict[$key]['new_sequence'] = $i++;
            //$module->emDebug($key, $this->data_dict[$key], $i);
        }
    }
    /**0
     * Look through the to_form_instance column of the map and make a list of all the repeating forms (unique
     * with instance number removed)
     */
    function getUniqueRepeatingForms() {

        $all_forms = array_unique(array_column($this->map, 'to_form_instance'));
        foreach($all_forms as $form) {
            $form_parts = explode(":", $form);
            $repeating_forms[] = $form_parts[0];
        }
        return array_filter($repeating_forms);
    }

    /**
     *
     * @param $file
     * @return array
     */
    function createMap($file) {
        global $module;

        $data = array();
        $formName = '';
        $pointer = 0;
        $this->header = array();


        if ($file) {
            while (($line = fgetcsv($file, self::MAP_ROWS, ",")) !== false) {
                //if ($pointer == 88) $module->emDebug("LINE $pointer: ", $line);

                if ($pointer == 0) {
                    $this->header =  $line;

                    //add the extra column headers
                    array_push($this->header,'dd_notes','dd_from_fieldtype','dd_to_fieldtype','dd_from_choice',
                               'dd_to_choice','dd_from_label','dd_to_label','dd_from_form','dd_to_form', 'new_sequence');

                    $pointer++;
                    continue;
                }

                $ptr = 0;
                $from_field = trim($line[$ptr]);
                if (empty($from_field)) {
                    $from_field = "TEMP_";
                }

                foreach ($this->header as $col_title) {
                    //$module->emDebug($ptr. " : " . $from_field. " : " .  $col_title . " : " . $line[$ptr]);

                    $data[$from_field][$col_title] = $line[$ptr++];
                }


               //add some extra meta-data from data-dictionary
/**
                * Data Dictionary example
                *     [arthritis] => Array
                (
                    [field_name] => arthritis
                    [form_name] => physical_neurological_exam_findings_d2cab9
                [section_header] => Joints
                [field_type] => checkbox
                [field_label] => Arthritis
                [select_choices_or_calculations] => 0, No|1, Yes|99, Not done
                [field_note] =>
            [text_validation_type_or_show_slider_number] =>
            [text_validation_min] =>
            [text_validation_max] =>
            [identifier] =>
            [branching_logic] =>
            [required_field] =>
            [custom_alignment] =>
            [question_number] =>
            [matrix_group_name] =>
            [matrix_ranking] =>
            [field_annotation] =>
        )
*/

                $to_field = trim($data[$from_field]['to_field']);
                    //if ($to_field == 88) $module->emDebug("LINE TO FIELD $pointer: ", $to_field);
                //if to_Field is missing in the data dictionary then report it as missing
                $to_field_dd    = $this->to_data_dict[$to_field];
                $from_fieldtype = $this->data_dict[$from_field]['field_type'];
                $to_fieldtype   = $this->to_data_dict[$to_field]['field_type'];

                if ($to_field_dd === null and $to_field !== '') {
                    $data[$from_field]['dd_notes'] = "MISSING TO FIELD; ";
                    //$module->emDebug("to field dd is ", $to_field_dd, $data);
                }

                if (($from_fieldtype !== $to_fieldtype) && $to_field !== '') {
                    $data[$from_field]['dd_notes'] .= "FIELDTYPES MISMATCH; ";
                    //$module->emDebug("to field dd is ", $to_field_dd, $data);
                }
                $data[$from_field]['dd_from_fieldtype'] = $from_fieldtype;
                $data[$from_field]['dd_to_fieldtype']   = $to_fieldtype;
                $data[$from_field]['dd_from_form']      = $this->data_dict[$from_field]['form_name'];
                $data[$from_field]['dd_to_form']        = $this->to_data_dict[$to_field]['form_name'];
                $data[$from_field]['dd_from_label']     = $this->data_dict[$to_field]['field_label'];
                $data[$from_field]['dd_to_label']       = $this->to_data_dict[$to_field]['field_label'];
                $data[$from_field]['new_sequence']      = $pointer;



                //if field_type is radio/checkbox/dropdwon highlight if the choices are different.
              if (0 == strcmp('checkbox', $from_fieldtype) ||
                  0 == strcmp('radio', $from_fieldtype) ||
                  0 == strcmp('dropdown', $from_fieldtype) ||
                  0 == strcmp('checkbox', $to_fieldtype) ||
                  0 == strcmp('radio', $to_fieldtype) ||
                  0 == strcmp('dropdown', $to_fieldtype)) {


                    //trim white space
                    $from_str_orig = $this->data_dict[$from_field]['select_choices_or_calculations'];
                    $to_str_orig   = $this->to_data_dict[$to_field]['select_choices_or_calculations'];
                    $from_str = preg_replace('/\s+/', '', trim(strtolower($from_str_orig)));
                    $to_str = preg_replace('/\s+/', '', trim(strtolower($to_str_orig)));

                    //if field choides are different, then report
                    if ((0 !== strcmp($from_str,$to_str)) && ($to_field !== '')) {
                        $data[$from_field]['dd_notes'] .= "CHOICE MISMATCH; ";
                        $data[$from_field]['dd_from_choice'] = "'".$from_str_orig."'";
                        $data[$from_field]['dd_to_choice'] = "'".$to_str_orig."'";
                    }
                }

                //if the from_field was empty rename it to TEMP_to_Field
                if ($from_field == "TEMP_") {
                    $data[$from_field.$to_field] = $data[$from_field];
                    unset($data[$from_field]);
                }

                $pointer++;

                if ($pointer == self::LAST_ROW) break;
            }
        }
        fclose($file);

        return $data;


    }



    public function downloadCSVFile($filename='mapper.csv')
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $fp = fopen('php://output', 'wb');
        fputcsv($fp, $this->header);
        foreach ($this->map as $row) {
            fputcsv($fp, $row);//, "\t", '"' );
        }

        fclose($fp);
    }


    private function downloadCSVFile2($filename, $data)
    {
        $data = implode("\n", $data);
        // Download file and then delete it from the server
        header('Pragma: anytextexeptno-cache', true);
        header('Content-Type: application/octet-stream"');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $data;
        exit();
    }

    /**
     * GETTER / SETTER
     */

    function getMap() {
        return $this->map;
    }

    function getRepeatingForms() {
        return $this->repeating_forms;
    }

}