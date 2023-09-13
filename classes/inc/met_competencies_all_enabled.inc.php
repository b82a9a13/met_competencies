<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_met_competencies\lib;
$lib = new lib;
$p = 'local_met_competencies';

$returnText = new stdClass();
if(!isset($_SESSION['met_competencies'])){
    $returnText->error = get_string('missing_rv', $p);
} else {
    if(!isset($_POST['t'])){
        $returnText->error = get_string('no_typ', $p);
    } else {
        $type = $_POST['t'];
        if(!in_array($type, ['d', 'e'])){
            $returnText->error = get_string('invalid_typ', $p);
        } else {
            $returnText->return = $lib->manage_met_competencies_record($type);
        }
    }
}
echo(json_encode($returnText));