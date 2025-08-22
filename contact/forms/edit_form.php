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
 * Edit_form is called by edit.php when a creator adds a new instance of contact enrolment method.
 *
 * @package    enrol_contact
 * @copyright  2025 MARY KATSAMANI <marykatsamani@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Form for adding/editing an instance of the enrolment method.

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * The enrol_contact_instance_form is used to replace moodle's default.
 */
class enrol_contact_instance_form extends moodleform {

    /**
     * Definition.
     */
    public function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata['instance'];
        $plugin = enrol_get_plugin('contact');
        $mform->addElement('hidden', 'courseid', $instance->courseid);

        $mform->setType('courseid', PARAM_INT);

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
        $mform->setDefault('roleid', $plugin->get_config('roleid'));

        // Enrolment period.
        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod', 'enrol_contact'), ['optional' => true]);
        $mform->setDefault('enrolperiod', $plugin->get_config('enrolperiod'));

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}
