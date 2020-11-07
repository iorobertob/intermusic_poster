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
 * Displays list of all mposters in the course.
 *
 * @package     mod_mposter
 * @copyright   Original Poster by 2015 David Mudrak <david@moodle.com>, modified by Roberto Becerra, 2020 <roberto.becerra@lmta.lt>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course, true);

$PAGE->set_pagelayout('incourse');

$PAGE->set_url('/mod/mposter/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.get_string('modulenameplural', 'mod_mposter'));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('modulenameplural', 'mod_mposter'));

// Trigger instances list viewed event.
$event = \mod_mposter\event\course_module_instance_list_viewed::create(array(
    'context' => context_course::instance($course->id)
));
$event->add_record_snapshot('course', $course);
$event->trigger();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_mposter'));

if (!$mposters = get_all_instances_in_course('mposter', $course)) {
    notice(get_string('thereareno', 'core', get_string('modulenameplural', 'mod_mposter')),
        new moodle_url('/course/view.php', array('id' => $course->id)));
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';
// Header stuff ! 
if ($usesections) {
    $table->head = array(
        get_string('sectionname', 'format_'.$course->format),
        get_string('mpostername', 'mod_mposter'),
        get_string('moduleintro', 'core')
    );
    $table->align = array('center', 'left', 'left');

} else {
    $table->head = array(
        get_string('lastmodified', 'core'),
        get_string('mpostername', 'mod_mposter'),
        get_string('moduleintro', 'core')
    );
    $table->align = array('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
// PROCESS INSTANCES!!
foreach ($mposters as $mposter) {
    $cm = $modinfo->cms[$mposter->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($mposter->section !== $currentsection) {
            if ($mposter->section) {
                $printsection = get_section_name($course, $mposter->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $mposter->section;
        }
    } else {
        $printsection = html_writer::span(userdate($mposter->timemodified), 'smallinfo');
    }

    $table->data[] = array(
        $printsection,
        html_writer::link(
            new moodle_url('view.php', array('id' => $cm->id)),
            format_string($mposter->name),
            array('class' => $mposter->visible ? '' : 'dimmed')
        ),
        format_module_intro('mposter', $mposter, $cm->id)
    );
}

echo html_writer::table($table);

echo $OUTPUT->footer();
