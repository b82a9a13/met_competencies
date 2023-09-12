<?php
/**
 * @package     local_met_competencies
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */
namespace local_met_competencies;
use stdClass;

class lib{
    //Get the user id of the current user
    private function get_current_userid(): int{
        global $USER;
        return $USER->id;
    }

    //Check user exists
    private function check_user_exists($id): bool{
        global $DB;
        return $DB->record_exists('user', [$DB->sql_compare_text('id') => $id]);
    }

    //Check current user is a coach for the user provided
    private function check_is_coach($id): array{
        global $USER;
        global $DB;
        $records = $DB->get_records_sql('SELECT ra.id as id, eu.userid as userid, eu.courseid as courseid, ra.roleid as roleid FROM {course} c
        INNER JOIN {context} ctx ON c.id = ctx.instanceid
        INNER JOIN {role_assignments} ra ON ra.contextid = ctx.id AND (ra.roleid = 4 OR ra.roleid = 3 OR ra.roleid = 5)
        INNER JOIN (
            SELECT e.courseid, ue.userid, u.firstname, u.lastname FROM {enrol} e
            INNER JOIN {user_enrolments} ue ON ue.enrolid = e.id AND ue.status != 1
            INNER JOIN {user} u ON u.id = ue.userid
        ) eu ON c.id = eu.courseid AND ra.userid = eu.userid AND (eu.userid = ? OR eu.userid = ?)',[$id, $this->get_current_userid()]);
        $array = [];
        foreach($records as $record){
            if(!array_key_exists($record->courseid, $array)){
                $array[$record->courseid] = [];
            }
            if(!in_array([$record->userid, $record->roleid], $array[$record->courseid])){
                array_push($array[$record->courseid], [$record->userid, $record->roleid]);
            }
            if(count($array[$record->courseid]) == 2){
                return [true, $record->courseid];
            }
        }
        return false;
    }

    //Check if the user id provided is valid for the operation
    public function check_userid_validation($id): array{
        $array = $this->check_is_coach($id);
        if($this->check_user_exists($id) === true && $array[0] === true){
            return [true, $array[1]];
        } else {
            return false;
        }
    }

    //Set all competencies for a specific user id and competency ids and set them to met if they aren't already
    public function met_all_competencies($array): bool{
        global $DB;
        for($i = 0; $i < count($array[1]); $i++){
            //Check if the competency exists for the user
            if(!$DB->record_exists('competency_usercomp', [$DB->sql_compare_text('userid') => $array[0], $DB->sql_compare_text('competencyid') => $array[1][$i]])){
                return false;
            } else {
                //Detemine if the competency is already set to met and if not create a class with the relevant data to update a record in the database
                $tmpRecord = $DB->get_record_sql('SELECT id, grade, proficiency, status FROM {competency_usercomp} WHERE userid = ? AND competencyid = ?',[$array[0], $array[1][$i]]);
                if($tmpRecord->grade != 3 || $tmpRecord->proficiency != 1 || $tmpRecord->status != 0){
                    $record = new stdClass();
                    $record->id = $tmpRecord->id;
                    $record->status = 0;
                    $record->reviewerid = $this->get_current_userid();
                    $record->proficiency = 1;
                    $record->grade = 3;
                    $record->timemodified = time();
                    if(!$DB->update_record('competency_usercomp', $record)){
                        return false;
                    }
                }
                //Determine if the competency is already set to met and if not set it to met by creating a record in the database
                $tempRecord = $DB->get_record_sql('SELECT grade, action FROM {competency_evidence} WHERE usercompetencyid = ? ORDER BY timemodified DESC LIMIT 1', [$tmpRecord->id]);
                if($tempRecord->grade != 3 || $tempRecord->action != 3){
                    $insert = new stdClass();
                    $insert->usercompetencyid = $tmpRecord->id;
                    $insert->contextid = 5;
                    $insert->action = 3;
                    $insert->actionuserid = $this->get_current_userid();
                    $insert->descidentifier = 'evidence_manualoverrideinplan';
                    $insert->desccomponent = 'core_competency';
                    $insert->desca = '"'.$DB->get_record_sql('SELECT c.name as name FROM {competency_plancomp} cp LEFT JOIN {competency_plan} c ON c.id = cp.planid WHERE cp.competencyid = ? AND c.userid = ?',[$array[1][$i], $array[0]])->name.'"';
                    $insert->url = null;
                    $insert->grade = 3;
                    $insert->note = '';
                    $insert->timecreated = time();
                    $insert->timemodified = time();
                    $insert->usermodified = $insert->actionuserid;
                    $DB->insert_record('competency_evidence', $insert, false);
                }
            }
        }
        return true;
    }

    //Check if a record exists in the met_competencies table
    public function check_met_competencies_record_exists(): bool{
        global $DB;
        return (count($DB->get_records_sql('SELECT id FROM {met_competencies}')) > 0) ? true : false;
    }
}