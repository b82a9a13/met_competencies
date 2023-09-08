<?php
/**
 * @package     local_met_competencies
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */
namespace local_met_competencies;

class lib{
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
        ) eu ON c.id = eu.courseid AND ra.userid = eu.userid AND (eu.userid = ? OR eu.userid = ?)',[$id, $USER->id]);
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
}