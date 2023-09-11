<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_met_competencies\lib;
$lib = new lib;
$p = 'local_met_competencies';

$returnText = new stdClass();
if(!isset($_POST['u'])){
    $returnText->error = 'No user id provided';
} else {
    $uid = $_POST['u'];
    if(!preg_match("/^[0-9]*$/", $uid) || empty($uid)){
        $returnText->error = 'Invalid user id provided';
    } else {
        $array = $lib->check_userid_validation($uid);
        if($array[0] != true){
            $returnText->error = 'Invalid';
        } else {
            $context = context_course::instance($array[1]);
            if(!has_capability('local/met_competencies:coach', $context)){
                $returnText->error = 'Invalid role';
            } else {
                require_capability('local/met_competencies:coach', $context);
                if(!isset($_POST['t'])){
                    $returnText->error = 'No total provided';
                } else {
                    $total = $_POST['t'];
                    if(!preg_match("/^[0-9]*$/", $total) || empty($total)){
                        $returnText->error = 'Invalid total provided';
                    } else {
                        $comp = [];
                        for($i = 0; $i < $total; $i++){
                            if(!isset($_POST["c$i"])){
                                $returnText->error = 'No competency provided';
                            } else {
                                $c = $_POST["c$i"];
                                if(!preg_match("/^[0-9]*$/", $c) || empty($c)){
                                    $returnText->error = 'Invalid competency provided';
                                } else {
                                    array_push($comp, $c);
                                }
                            }
                        }
                        if(!isset($returnText->error)){
                            if($comp == []){
                                $returnText->error = 'No competencies changed';
                            } else{
                                $returnText->return = $lib->met_all_competencies([$uid, $comp]);
                            }
                        }
                    }
                }
            }
        }
    }
}

echo(json_encode($returnText));