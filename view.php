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
 * View the mediaposter instance
 *
 * @package     mod_mediaposter
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/completionlib.php');

$cmid = required_param('id', PARAM_INT);
$edit = optional_param('edit', null, PARAM_BOOL);

$cm = get_coursemodule_from_id('mediaposter', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$mediaposter = $DB->get_record('mediaposter', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
require_capability('mod/mediaposter:view', $PAGE->context);

$PAGE->set_url('/mod/mediaposter/view.php', array('id' => $cm->id));
$PAGE->set_title($course->shortname.': '.$mediaposter->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($mediaposter);

if ($edit !== null and confirm_sesskey() and $PAGE->user_allowed_editing()) {
	$USER->editing = $edit;
    	redirect($PAGE->url);
}

// Trigger module viewed event.
$event = \mod_mediaposter\event\course_module_viewed::create(array(
   'objectid' => $mediaposter->id,
   'context' => $PAGE->context,
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('mediaposter', $mediaposter);
$event->trigger();

// Mark the module instance as viewed by the current user.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Define the custom block regions we want to use at the mediaposter view page.
// Region names are limited to 16 characters.
$PAGE->blocks->add_region('mod_mediaposter-pre', true);
$PAGE->blocks->add_region('mod_mediaposter-post', true);

$output = $PAGE->get_renderer('mod_mediaposter');

echo $output->view_page($mediaposter);


