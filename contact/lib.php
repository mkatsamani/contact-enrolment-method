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
 * Enrolment method enrol_contact
 *
 * @package    enrol_contact
 * @copyright  2025 MARY KATSAMANI <marykatsamani@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Adding new enrolmend record in mdl_enrol, with default values.
// Define Buttons in Enrolment methods of a course about adding, editing and deleting an enrlment method.


defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/enrol/contact/forms/edit_form.php');

/**
 * Enrol_contact_plugin logic.
 */
class enrol_contact_plugin extends enrol_plugin {

    /**
     * Displays the enrolment type in the "Add method" selector
     *
     * @return boolean
     */
    public function use_standard_editing_ui() {
        return true;
    }

    /**
     * Users with 'manage' can change roles; otherwise roles are protected.
     *
     * @return boolean
     */
    public function roles_protected() {
        return true;
    }

    /**
     * Allow enrolment via this plugin.
     *
     * @return boolean
     */
    public function allow_enrol(stdClass $instance) {
        return true;
    }

    /**
     * Allow unenrolment via this plugin.
     *
     * @return boolean
     */
    public function allow_unenrol(stdClass $instance) {
        return has_capability('enrol/contact:unenrol', context_course::instance($instance->courseid));
    }

    /**
     * Allow manage via this plugin.
     *
     * @return boolean
     */
    public function allow_manage(stdClass $instance) {
        return has_capability('enrol/contact:manage', context_course::instance($instance->courseid));
    }

    /**
     * Get_instance_name.
     *
     * @return pluginname
     */
    public function get_instance_name($instance) {
        if (!empty($instance->name)) {
            return format_string($instance->name, true, ['context' => context_course::instance($instance->courseid)]);
        }
        return get_string('pluginname', 'enrol_contact');
    }

    /**
     * No default instance auto-added. Teachers can add from Course → Participants → Enrolment methods → Add method.
     *
     * @return null
     */
    public function add_default_instance($course) {
        return null;
    }

    /**
     * Can current user manually add an instance of this enrolment type to the course.
     *
     * @param int $courseid
     * @return bool
     */
    public function can_add_instance($courseid) {
        $context = context_course::instance($courseid);
        return has_capability('enrol/contact:config', $context);
    }

    /**
     * Show the "Add method" link if user can config.
     *
     * @param int $courseid
     * @return edit.php
     */
    public function get_newinstance_link($courseid) {
        if (!$this->can_add_instance($courseid)) {
            return null;
        }
        return new moodle_url('/enrol/contact/edit.php', ['courseid' => $courseid]);
    }

    /**
     * Can current user manually edit an instance of this enrolment type to the course.
     *
     * @param int $instance
     * @return boolean
     */
    public function can_edit_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/contact:config', $context);
    }

    /**
     * I define the fields which I want to be changed by the creator.
     */
    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {

        // Instance name.
        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol_contact'));
        $mform->setType('name', PARAM_TEXT);

        // Max enrolled.
        $mform->addElement('text', 'customint1', get_string('maxenrolled', 'enrol_contact'));
        $mform->setType('customint1', PARAM_INT);
        $mform->setDefault('customint1', 0);

        // Role.
        $roles = get_default_enrol_roles(context_system::instance());
        $mform->addElement('select', 'roleid', get_string('defaultrole', 'enrol_contact'), $roles);

        // Enrolment period.
        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod', 'enrol_contact'), ['optional' => true]);

    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/contact:config', $context);
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/contact:config', $context);
    }

    /**
     * Output shown on the course enrolment page for users not yet enrolled.
     * Provides a simple form with optional key.
     */
    public function enrol_page_hook(stdClass $instance) {
        global $CFG, $OUTPUT, $USER, $DB;

        // If max enrolled limit reached, hide.
        if ($instance->customint1 > 0) {
            $enrolled = $DB->count_records('user_enrolments', ['enrolid' => $instance->id]);
            if ($enrolled >= $instance->customint1) {
                return html_writer::div(get_string('maxenrolledreached', 'enrol_contact'));
            }
        }

        $context = context_course::instance($instance->courseid, MUST_EXIST);
        if (is_enrolled($context, $USER)) {
            return '';
        }

        // Get the course creator from logs.
        $course = $DB->get_record('course', array('id' => $instance->courseid), '*', MUST_EXIST);
        $creator = $DB->get_record_sql("
                                        SELECT u.id AS userid
                                        FROM {logstore_standard_log} l
                                        JOIN {user} u ON u.id = l.userid
                                        WHERE l.eventname = ?
                                        AND l.courseid = ?
                                        ORDER BY l.timecreated ASC
                                        LIMIT 1
                                    ", ['\core\event\course_created', $instance->courseid]);
        if (!$creator) {
            throw new \moodle_exception('No creator found!', '', $CFG->wwwroot . '/');
        }
        // Teacher's Id.
        $teacherid = $creator->userid;
        // Check if teacher is in user's contacts.
        if ($USER->id !== $teacherid) {
            $found = false;
            $contacts = core_message\api::get_user_contacts($USER->id, 0, 0);
            foreach ($contacts as $contact) {
                if ($contact->id == $teacherid) {
                    $found = true;
                    break;
                }
            }
        } else {
            throw new \moodle_exception('You are the teacher!', '', $CFG->wwwroot . '/');
        }
        if ($found == false) {
            \core\notification::error('You can not enrol in "' . $course->fullname . '" because you are not in teacher\'s contact list.');
            redirect("$CFG->wwwroot/");
        }

        $formid = 'enrol_contact_' . $instance->id;
        $url = new moodle_url('/enrol/index.php', ['id' => $instance->courseid]);

        // Handle submission.
        $submitted = optional_param('enrol_contact_submit', 0, PARAM_BOOL);
        if ($submitted) {
            require_sesskey();
            $key = optional_param('enrol_contact_key', '', PARAM_RAW_TRIMMED);
            if (!empty($instance->password) && !hash_equals($instance->password, $key)) {
                // Wrong key.
                return html_writer::div(get_string('invalidkey', 'enrol_contact')) . $this->render_enrol_form($formid, $url, $instance);
            }
            // Enrol now.
            $timestart = time();
            $timeend = 0;
            if (!empty($instance->enrolperiod)) {
                $timeend = $timestart + (int)$instance->enrolperiod;
            }
            $roleid = $this->get_config('roleid');
            if (empty($roleid)) {
                $roleid = $instance->roleid;
            }
            $this->enrol_user($instance, $USER->id, $roleid, $timestart, $timeend);
            redirect(new moodle_url('/course/view.php', ['id' => $instance->courseid]));
        }

        // Initial render.
        return $this->render_enrol_form($formid, $url, $instance);
    }

    private function render_enrol_form(string $formid, moodle_url $url, stdClass $instance): string {
        $o = html_writer::start_tag('form', ['method' => 'post', 'action' => $url, 'id' => $formid]);
        $o .= html_writer::input_hidden_params($url);
        $o .= html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => 'sesskey',
            'value' => sesskey(),
        ]);
        $o .= html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => 'enrol_contact_submit',
            'value' => 1,
        ]);
        if (!empty($instance->password)) {
            $o .= html_writer::tag(
                'div',
                html_writer::tag('label', get_string('enterkey', 'enrol_contact'), ['for' => 'enrol_contact_key']) .
                    html_writer::empty_tag('input', [
                        'type' => 'password',
                        'name' => 'enrol_contact_key',
                        'id' => 'enrol_contact_key',
                    ])
            );
        }
        $o .= html_writer::tag('div', html_writer::empty_tag('input', [
            'type' => 'submit',
            'value' => get_string('enrolme', 'enrol_contact'),
        ]));
        $o .= html_writer::end_tag('form');
        return $o;
    }
    
     /**
     * Perform custom validation of the data used to edit the instance.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param object $instance The instance data loaded from the DB.
     * @param context $context The context of the instance we are editing
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     */
    public function edit_instance_validation($data, $files, $instance, $context) {
        return [];
    }
}
