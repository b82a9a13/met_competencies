<?php
require_once(__DIR__.'/../../../../config.php');
require_login();
use local_met_competencies\lib;
$lib = new lib;
$p = 'local_met_competencies';

$returnText = new stdClass();
if(!isset($_SESSION['met_competencies'])){
    $returnText->error = 'Missing required value';
} else {
    
}
echo(json_encode($returnText));