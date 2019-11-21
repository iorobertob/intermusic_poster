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
 * Plugin internal classes, functions and constants are defined here.
 *
 * @package     mod_inter
 * @copyright   2019 LMTA <roberto.becerra@lmta.lt>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/poster/lib.php");



function poster_set_mainfile($data) {
    global $DB;
    $fs = get_file_storage();
    $cmid = $data->coursemodule;
    $draftitemid = $data->files;
    $context = context_module::instance($cmid);
    if ($draftitemid) {
        $options = array('subdirs' => true, 'embed' => false);
        if ($data->display == RESOURCELIB_DISPLAY_EMBED) {
            $options['embed'] = true;
        }
        file_save_draft_area_files($draftitemid, $context->id, 'mod_inter', 'content', 0, $options);
    }
    $files = $fs->get_area_files($context->id, 'mod_inter', 'content', 0, 'sortorder', false);
    if (count($files) == 1) {
        // only one file attached, set it as main file automatically
        $file = reset($files);
        file_set_sortorder($context->id, 'mod_inter', 'content', 0, $file->get_filepath(), $file->get_filename(), 1);
	}
    $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
    return $url;
}


/** 
* Item is each one of the parts in a file name like: item_item_item.extension
* If filenames of files uploaded to this poster contain information separated by _ (undesrcore), this 
* function retreives one of those elements from the first of the files to upload. 
* @param Context  $context the context of the current course
* @param String   $item_number is the position number of the filename to get
* @return String  $item is the piece of string from the filename of the first file in the upload. 
**/
function get_item_from_filename($context, $item_number, $id, $data)
{
    global $DB, $CFG, $PAGE;    
    // require_once("$CFG->dirroot/mod/poster/io_print.php");

    
    poster_print('INSTANCE ID',TRUE);
    poster_print($context->instanceid);
    poster_print($id);


    $draftitemid = $data->files;
    if($draftitemid)
    {
        poster_print("DRAFT");

        $options = array('subdirs' => true, 'embed' => false);
        if ($data->display == RESOURCELIB_DISPLAY_EMBED) {
            $options['embed'] = true;
        }
        file_save_draft_area_files($draftitemid, $context->id, 'mod_poster', 'content', 0, $options);
    }

    // // TODO: here to implement the autopopulation of metadata, from files' metadata
    // $activity_module      = $DB->get_record('course_modules',array('id' =>$context         ->instanceid)); // get the module where the course is the current course
    // $poster_instance      = $DB->get_record('poster',        array('id' =>$activity_module ->instance  )); // get the name of the module instance 
    // $poster_name          = $poster_instance->name;
    // $autopopulateCheckbox = $poster_instance->autopopulate;
    
    // // Get files array and their names, split them by '_' and return the first of those divisions. 
    $fs              = get_file_storage();
    $files           = $fs->get_area_files($context->id, 'mod_poster', 'content', 0, 'sortorder', false);
    poster_print('COUNT');
    poster_print(count($files));
    $keys            = array_keys($files);
    $filename        = $files[$keys[0]] -> get_filename();
    // $filename_parts  = explode("_", $filename);
    // $item            = $filename_parts[$item_number];
    // $characteristics = $filename_parts[2];

    // $items    = [];
    // $items[0] = $item;
    // $items[1] = $poster_name;
    // return $items;






    poster_print($filename);
}