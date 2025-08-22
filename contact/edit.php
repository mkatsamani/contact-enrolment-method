<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edit.php is called when a creator adds a new instance of contact enrolment method.
 *
 * @package    enrol_contact
 * @copyright  2025 MARY KATSAMANI <marykatsamani@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot . '/enrol/contact/lib.php');
require_once($CFG->dirroot.'/enrol/contact/forms/edit_form.php');


$courseid = required_param('courseid', PARAM_INT);
$context = context_course::instance($courseid);


require_login(null, false);
require_capability('enrol/contact:config', $context);

$plugin = enrol_get_plugin('contact');
if (!$plugin) {
    throw new moodle_exception('Plugin not found');
}

$PAGE->set_url(new moodle_url('/enrol/contact/edit.php', ['courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'enrol_contact'));
$PAGE->set_heading(get_string('pluginname', 'enrol_contact'));

// Prepare a new instance with sensible defaults.
$instance = new stdClass();
$instance->id = 0;
$instance->courseid = $courseid;
$instance->status = ENROL_INSTANCE_ENABLED; // Make the method active by default.
$instance->enrolperiod = $plugin->get_config('enrolperiod');
$instance->roleid = $plugin->get_config('roleid');
$instance->customint1 = 0; // Maxenrolled.

$mform = new enrol_contact_instance_form(null, ['instance' => $instance]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/enrol/instances.php', ['id' => $courseid]));
}

if ($data = $mform->get_data()) {
    global $DB;

    // Create the enrol instance.
    $fields = new stdClass();
    $fields->status = ENROL_INSTANCE_ENABLED;
    $fields->name = $data->name ?? '';
    $fields->password = $data->password ?? '';
    $fields->roleid = (int)$data->roleid;
    $fields->enrolperiod = (int)$data->enrolperiod;
    $fields->customint1 = (int)$data->customint1; // Maxenrolled.

    $plugin->add_instance($course, (array)$fields);

    redirect(new moodle_url('/enrol/instances.php', ['id' => $courseid]));
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
