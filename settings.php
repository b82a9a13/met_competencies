<?php
/**
* 
* @package     local_met_competencies
* @author      Robert Tyrone Cullen
*/

defined('MOODLE_INTERNAL') || die();

if($hassiteconfig){
    //Adds a new category to local_plugins
    $ADMIN->add('localplugins', new admin_category('local_met_competencies', get_string('pluginname', 'local_met_competencies')));
    //Adds a hyperlink to the administrate page
    $ADMIN->add('local_met_competencies', new admin_externalpage('local_met_competencies_admin', get_string('administrate', 'local_met_competencies'), $CFG->wwwroot.'/local/met_competencies/admin.php'));
}