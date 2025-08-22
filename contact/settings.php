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
 * Settings of Contact Enrolment Method.
 *
 * @package    enrol_contact
 * @copyright  2025 MARY KATSAMANI <marykatsamani@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading('enrol_contact_settings', '', get_string('pluginname_desc', 'enrol_contact')));
}

if ($ADMIN->fulltree) {

    // Define plugin name.
    $settings->add(new admin_setting_configtext('enrol_contact/pluginname',
        get_string('pluginname', 'enrol_contact'), // Setting title.
        '', // Setting description.
        get_string('pluginname_default', 'enrol_contact'), // Default value.
        PARAM_TEXT));

    // Enable/disable by default for new instances.
    $settings->add(new admin_setting_configcheckbox(
        'enrol_contact/status',
        get_string('status', 'enrol_contact'),
        get_string('status_desc', 'enrol_contact'),
        1
    ));

    // Default role for new enrolments.
    $studentroles = get_archetype_roles('student');
    $defaultrole = key($studentroles);
    $settings->add(new admin_setting_configselect(
        'enrol_contact/roleid',
        get_string('defaultrole', 'enrol_contact'),
        '',
        $defaultrole,
        get_default_enrol_roles(context_system::instance())
    ));

    // Add Max Enrolled Users setting.
    $settings->add(new admin_setting_configtext(
        'enrol_contact/maxusers',            // Setting name.
        get_string('maxusers', 'enrol_contact'),  // Label.
        get_string('maxusers_desc', 'enrol_contact'),  // Description.
        100, // Default value.
        PARAM_INT  // Validate as integer.
    ));

    // Default enrolment period.
    $settings->add(new admin_setting_configduration(
        'enrol_contact/enrolperiod',
        get_string('enrolperiod', 'enrol_contact'),
        get_string('enrolperiod_desc', 'enrol_contact'),
        0
    ));
}
