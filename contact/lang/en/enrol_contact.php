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
 * English language pack for enrol_contact
 *
 * @package    enrol_contact
 * @category   string
 * @copyright  2025 MARY KATSAMANI <marykatsamani@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Language strings.
$string['invalidinstance'] = 'invalidinstance';
$string['pluginname'] = 'Contact Enrolment Method';
$string['pluginname_default'] = 'Contact Enrolment Method';
$string['pluginname_desc'] = 'Allow users to enrol if they are in teacher\'s contact list.';

// Capabilities.
$string['contact:config'] = 'Configure Contact Enrolment instances';
$string['contact:manage'] = 'Manage users enrolled via Contact Enrolment';
$string['contact:unenrol'] = 'Unenrol users enrolled via Contact Enrolment';
$string['contact:unenrolself'] = 'Unenrol self from a course (Contact Enrolment)';

// Settings.
$string['defaultrole'] = 'Default role assignment';
$string['enrolperiod'] = 'Default enrolment duration';
$string['enrolperiod_desc'] = 'The default length of time that the enrolment is valid (in seconds). 0 means unlimited.';
$string['maxusers'] = 'Maximum Enrolled Users';
$string['maxusers_desc'] = 'Set the maximum number of users that can be enrolled in this course/plugin.';
$string['maxusersreached'] = 'Maximum number of users reached for this plugin.';
$string['status'] = 'Enable plugin by default';
$string['status_desc'] = 'If enabled, new courses can add this enrolment method.';

// Instance form strings.
$string['custominstancename'] = 'Custom instance name';
$string['maxenrolled'] = 'Max enrolled users (0 = unlimited)';
$string['password'] = 'Enrolment key (optional)';

// UI.
$string['enrolme'] = 'Enrol (only for teacher\'s contacts)';
$string['enterkey'] = 'Enter enrolment key';
$string['invalidkey'] = 'Invalid enrolment key.';
$string['maxenrolledreached'] = 'Maximum number of enrolled users reached for this method.';

// Privacy.
$string['privacy:metadata'] = 'The My Custom Enrolment plugin does not store any personal data beyond what Moodle already stores.';
