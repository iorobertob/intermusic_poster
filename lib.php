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

    $poster->id = $DB->insert_record('poster', $poster);



    /////////////////////////////////////////////////
    $context = $PAGE->context;
    file_print('COURSE MODULE', true);
    file_print($poster->coursemodule);
    // get_item_from_filename($context, 0, $poster->id);
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
    * Item is each one of the parts in a file name like: item_item_item.extension
    * If filenames of files uploaded to this poster contain information separated by _ (undesrcore), this 
    * function retreives one of those elements from the first of the files to upload. 
    * @param Context  $context the context of the current course
    * @param String   $item_number is the position number of the filename to get
    * @return String  $item is the piece of string from the filename of the first file in the upload. 
    **/
    function get_item_from_filename($context, $item_number, $id)
    {
        global $DB, $CFG, $PAGE;    
        require_once("$CFG->dirroot/mod/poster/io_print.php");

        
        file_print('INSTANCE ID',TRUE);
        file_print($context->instanceid);
        file_print($id);


        // // TODO: here to implement the autopopulation of metadata, from files' metadata
        // $activity_module      = $DB->get_record('course_modules',array('id' =>$context         ->instanceid)); // get the module where the course is the current course
        // $poster_instance      = $DB->get_record('poster',        array('id' =>$activity_module ->instance  )); // get the name of the module instance 
        // $poster_name          = $poster_instance->name;
        // $autopopulateCheckbox = $poster_instance->autopopulate;
        
        // // Get files array and their names, split them by '_' and return the first of those divisions. 
        $fs              = get_file_storage();
        $files           = $fs->get_area_files($context->id, 'mod_poster', 'file', 0);
        $keys            = array_keys($files);
        $filename        = $files[$keys[1]] -> get_filename();
        // $filename_parts  = explode("_", $filename);
        // $item            = $filename_parts[$item_number];
        // $characteristics = $filename_parts[2];

        // $items    = [];
        // $items[0] = $item;
        // $items[1] = $poster_name;
        // return $items;






        file_print($filename);
    }
