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



    // FASDFASDFAdf
    poster_set_display_options($poster);




    $poster->id = $DB->insert_record('poster', $poster);



    /////////////////////////////////////////////////
    $cmid = $poster->coursemodule;

    // $context = $PAGE->context;
    poster_print('COURSE MODULE', true);
    poster_print($poster->coursemodule);
    
    $context = context_module::instance($cmid);
    


    $DB->set_field('course_modules', 'instance', $poster->id, array('id'=>$cmid));

    // get_item_from_filename($context, 0, $poster->id, $poster);
    $url = poster_set_mainfile($poster);



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

    // FASDFASDFAdf
    poster_set_display_options($poster);


    $DB->update_record('poster', $poster);

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
 * Updates display options based on form input.
 *
 * Shared code used by resource_add_instance and resource_update_instance.
 *
 * @param object $data Data object
 */
function poster_set_display_options($data) {
    $displayoptions = array();
    if ($data->display == RESOURCELIB_DISPLAY_POPUP) {
        $displayoptions['popupwidth']  = $data->popupwidth;
        $displayoptions['popupheight'] = $data->popupheight;
    }
    if (in_array($data->display, array(RESOURCELIB_DISPLAY_AUTO, RESOURCELIB_DISPLAY_EMBED, RESOURCELIB_DISPLAY_FRAME))) {
        $displayoptions['printintro']   = (int)!empty($data->printintro);
    }
    if (!empty($data->showsize)) {
        $displayoptions['showsize'] = 1;
    }
    if (!empty($data->showtype)) {
        $displayoptions['showtype'] = 1;
    }
    if (!empty($data->showdate)) {
        $displayoptions['showdate'] = 1;
    }
    $data->displayoptions = serialize($displayoptions);
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
        return new poaster_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true, false);
    }

    // note: resource_intro handled in file_browser automatically

    return null;
}

/**
 * Serves the files from the mod_inter file areas.
 *
 * @package     mod_inter
 * @category    files
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param stdClass $context The mod_inter's context.
 * @param string $filearea The name of the file area.
 * @param array $args Extra arguments (itemid, path).
 * @param bool $forcedownload Whether or not force download.
 * @param array $options Additional options affecting the file serving.
 */
function poster_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = array()) {
    global $DB, $CFG;

    require_once("$CFG->libdir/resourcelib.php");

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);
    if (!has_capability('mod/poster:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        // intro is handled automatically in pluginfile.php
        return false;
    }

    array_shift($args); // ignore revision - designed to prevent caching problems only

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = rtrim("/$context->id/mod_poster/$filearea/0/$relativepath", '/');
    do {
        if (!$file = $fs->get_file_by_hash(sha1($fullpath))) {
            if ($fs->get_file_by_hash(sha1("$fullpath/."))) {
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.htm"))) {
                    break;
                }
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.html"))) {
                    break;
                }
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/Default.htm"))) {
                    break;
                }
            }
            $instance = $DB->get_record('poster', array('id'=>$cm->instance), 'id, legacyfiles', MUST_EXIST);
            if ($instance->legacyfiles != RESOURCELIB_LEGACYFILES_ACTIVE) {
                return false;
            }
            if (!$file = resourcelib_try_file_migration('/'.$relativepath, $cm->id, $cm->course, 'mod_poster', 'content', 0)) {
                return false;
            }
            // file migrate - update flag
            $instance->legacyfileslast = time();
            $DB->update_record('poster', $instance);
        }
    } while (false);

    // should we apply filters?
    $mimetype = $file->get_mimetype();
    if ($mimetype === 'text/html' or $mimetype === 'text/plain' or $mimetype === 'application/xhtml+xml') {
        $filter = $DB->get_field('poster', 'filterfiles', array('id'=>$cm->instance));
        $CFG->embeddedsoforcelinktarget = true;
    } else {
        $filter = 0;
    }

    // finally send the file
    send_stored_file($file, null, $filter, $forcedownload, $options);
    // send_stored_file($file, null, $filter, false, $options);
    
}

