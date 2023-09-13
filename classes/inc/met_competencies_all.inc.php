<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_met_competencies\lib;
$lib = new lib;
$p = 'local_met_competencies';

$returnText = new stdClass();
if(!isset($_POST['u'])){
    $returnText->error = get_string('no_uip', $p);
} else {
    $uid = $_POST['u'];
    if(!preg_match("/^[0-9]*$/", $uid) || empty($uid)){
        $returnText->error = get_string('invalid_uip', $p);
    } else {
        $array = $lib->check_userid_validation($uid);
        if($array[0] != true){
            $returnText->error = get_string('invalid', $p);
        } else {
            $context = context_course::instance($array[1]);
            if(!has_capability('local/met_competencies:coach', $context)){
                $returnText->error = get_string('invalid_r', $p);
            } else {
                require_capability('local/met_competencies:coach', $context);
                if($lib->check_met_competencies_record()){
                    if(!isset($_POST['t'])){
                        $returnText->error = get_string('no_tp', $p);
                    } else {
                        $total = $_POST['t'];
                        if(!preg_match("/^[0-9]*$/", $total) || empty($total)){
                            $returnText->error = get_string('invalid_tp', $p);
                        } else {
                            $comp = [];
                            for($i = 0; $i < $total; $i++){
                                if(!isset($_POST["c$i"])){
                                    $returnText->error = get_string('no_cp', $p);
                                } else {
                                    $c = $_POST["c$i"];
                                    if(!preg_match("/^[0-9]*$/", $c) || empty($c)){
                                        $returnText->error = get_string('invalid_cp', $p);
                                    } else {
                                        array_push($comp, $c);
                                    }
                                }
                            }
                            if(!isset($returnText->error)){
                                if($comp == []){
                                    $returnText->error = get_string('no_cc', $p);
                                } else{
                                    $returnText->return = $lib->met_all_competencies([$uid, $comp]);
                                }
                            }
                        }
                    }   
                } else {
                    $returnText->error = get_string('feature_isd', $p);
                }
            }
        }
    }
}

echo(json_encode($returnText));