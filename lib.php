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
 * Activity module interface functions are defined here
 *
 * @package     mod_poster
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/mod/poster/io_print.php");
require_once("$CFG->dirroot/mod/poster/locallib.php");
require_once("$CFG->libdir/resourcelib.php");

//moodle 
/**
 * Returns the information if the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool true if the feature is supported, null if unknown
 */
function poster_supports($feature) {

    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        default:
            return null;
    }
}

//moodle
/**
 * Adds a new instance of the poster into the database
 *
 * Given an object containing all the settings form data, this function will
 * save a new instance and return the id of the new instance.
 *
 * @param stdClass $poster An object from the form in mod_form.php
 * @return int The id of the newly inserted poster record
 */
function poster_add_instance(stdClass $poster) {
    global $DB, $PAGE, $CFG;

    $poster->timecreated = time();
    $poster->timemodified = $poster->timecreated;

    $poster->id = $DB->insert_record('poster', $poster);

    /////////////// CUSTOM CODE ////////////////////
    $cmid = $poster->coursemodule;
    poster_print("CMID: " . $cmid, true);
    $context = context_module::instance($cmid);

    $DB->set_field('course_modules', 'instance', $poster->id, array('id'=>$cmid));

    try{
        $url = poster_set_mainfile($poster);

        poster_get_metadata($cmid, $poster);
    }catch (Exception $e){
        poster_print($e);
    }catch (Throwable $e){
        poster_print($e);
    }catch (Error $e){
        poster_print($e);
    }
    
    

    $completiontimeexpected = !empty($poster->completionexpected) ? $poster->completionexpected : null;
    
    \core_completion\api::update_completion_date_event($cmid, 'poster', $poster->id, $completiontimeexpected);

    /////////////////////////////////////////////////


    return $poster->id;
}

/**
 * Updates the existing instance of the poster in the database
 *
 * Given an object containing all the settings form data, this function will
 * update the instance record with the new form data.
 *
 * @param stdClass $poster An object from the form in mod_form.php
 * @return bool true
 */
function poster_update_instance(stdClass $poster) {
    global $DB;

    $poster->timemodified = time();
    $poster->id = $poster->instance;
    $poster->revision++;

    $DB->update_record('poster', $poster);

    /////////////// CUSTOM CODE ////////////////////
    $cmid = $poster->coursemodule;
    $context = context_module::instance($cmid);

    $url = poster_set_mainfile($poster);

    poster_get_metadata($context, $poster);

    $completiontimeexpected = !empty($poster->completionexpected) ? $poster->completionexpected : null;
    \core_completion\api::update_completion_date_event($poster->coursemodule, 'poster', $poster->id, $completiontimeexpected);
    /////////////////////////////////////////////////

    
    return true;
}

/**
 * Deletes the poster instance
 *
 * @param int $id ID of the poster instance
 * @return bool Success indicator
 */
function poster_delete_instance($id) {
    global $DB;

    if (! $poster = $DB->get_record('poster', array('id' => $id))) {
        return false;
    }

    $DB->delete_records('poster', array('id' => $poster->id));

    return true;
}

/**
 * Adds items into the poster administration block
 *
 * @param settings_navigation $settingsnav The settings navigation object
 * @param navigation_node $node The node to add module settings to
 */
function poster_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $node) {
    global $PAGE;

    if ($PAGE->user_allowed_editing()) {
        $url = $PAGE->url;
        $url->param('sesskey', sesskey());

        if ($PAGE->user_is_editing()) {
            $url->param('edit', 'off');
            $editstring = get_string('turneditingoff', 'core');
        } else {
            $url->param('edit', 'on');
            $editstring = get_string('turneditingon', 'core');
        }

        $node->add($editstring, $url, navigation_node::TYPE_SETTING);
    }
}

/**
 * Return the page type patterns that can be used by blocks
 *
 * @param string $pagetype Current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function poster_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return array(
        'mod-poster-view' => get_string('page-mod-poster-view', 'mod_poster'),
    );
}

/**
 * Returns the lists of all browsable file areas within the given module context.
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}.
 *
 * @package     mod_inter
 * @category    files
 *
 * @param stdClass $course.
 * @param stdClass $cm.
 * @param stdClass $context.
 * @return string[].
 */
function poster_get_file_areas($course, $cm, $context) {
    // return array();
    $areas = array();
    $areas['content'] = get_string('resourcecontent', 'poster');
    return $areas;
}

/**
 * File browsing support for mod_poster file areas.
 *
 * @package     mod_inter
 * @category    files
 *
 * @param file_browser $browser.
 * @param array $areas.
 * @param stdClass $course.
 * @param stdClass $cm.
 * @param stdClass $context.
 * @param string $filearea.
 * @param int $itemid.
 * @param string $filepath.
 * @param string $filename.
 * @return file_info Instance or null if not found.
 */
function poster_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
     global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        // students can not peak here!
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'content') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_poster', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_poster', 'content', 0);
            } else {
                // not found
                return null;
            }
        }
        require_once("$CFG->dirroot/mod/poster/locallib.php");
        return new poster_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true, false);
    }

    // note: resource_intro handled in file_browser automatically

    return null;
}



