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
 * A scheduled task for forum cron.
 *
 * @package    mod_mdposter
 * @copyright  2019 Roberto Becerra <roberto.becerra@lmta.lt>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_mdposter\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/mdposter/lib.php');
require_once($CFG->dirroot . '/mod/mdposter/locallib.php');
require_once("$CFG->libdir/resourcelib.php");

/**
 * The main scheduled task for the forum.
 *
 * @package    mod_mdposter
 * @copyright  2019 Roberto Becerra <roberto.becerra@lmta.lt>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_metadata extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('metadata_task', 'mod_mdposter');
    }

    /**
     * Execute the scheduled task.
     */
    public function execute() {

        global $CFG, $DB, $PAGE;

        // require_once($CFG->dirroot . '/lib/accesslib.php');

        $mdposter_instances = $DB->get_records("mdposter", null, $sort='', $fields='*');
        
        foreach($mdposter_instances as $moduleinstance)
        {
            $cmid = $DB->get_field("course_modules", 'id', array('course'=> $moduleinstance->course, 'instance'=>$moduleinstance->id));
            // echo $cmid;
            // $context = context_module::instance($cmid);
            mdposter_get_metadata($cmid, $moduleinstance);
        }
    }
}
