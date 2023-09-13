<?php
/**
 * @package     local_met_competencies
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

require_once(__DIR__.'/../../config.php');
require_login();
$context = context_system::instance();
require_capability('local/met_competencies:admin', $context);
use local_met_competencies\lib;
$lib = new lib;
$p = 'local_met_competencies';
$title = get_string('admin_title', $p);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/met_competencies/admin.php'));
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

$template = (Object)[
    'title' => $title,
    'met_ac' => get_string('met_ac', $p),
    'type' => ($lib->check_met_competencies_record()) ? 'Disable' : 'Enable'
];
echo $OUTPUT->render_from_template("$p/admin", $template);

echo $OUTPUT->footer();
$_SESSION['met_competencies'] = true;