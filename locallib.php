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


// moodle 
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
        file_save_draft_area_files($draftitemid, $context->id, 'mod_poster', 'content', 0, $options);
    }
    $files = $fs->get_area_files($context->id, 'mod_poster', 'content', 0, 'sortorder', false);
    if (count($files) == 1) {
        // only one file attached, set it as main file automatically
        $file = reset($files);
        file_set_sortorder($context->id, 'mod_poster', 'content', 0, $file->get_filepath(), $file->get_filename(), 1);
	}
    $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
    return $url;
}

/** 
* lmta.utility
* Item is each one of the parts in a file name like: item_item_item.extension
* If filenames of files uploaded to this poster contain information separated by _ (undesrcore), this 
* function retreives one of those elements from the first of the files to upload. 
* @param Context  $context the context of the current course
* @param String   $item_number is the position number of the filename to get
* @return String  $item is the piece of string from the filename of the first file in the upload. 
*/
function get_item_from_filename($context, $item_number, $id)
{
    global $DB, $CFG, $PAGE;    
    // require_once("$CFG->dirroot/mod/poster/io_print.php");

    poster_print('INSTANCE ID',TRUE);
    poster_print($context->instanceid);
    poster_print($id);


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
    $filename_parts  = explode("_", $filename);
    $item            = $filename_parts[$item_number];
    $characteristics = $filename_parts[2];


    // $items    = [];
    $items[0] = $item;
    $items[1] = $filename;

    poster_print($items[0]);
    poster_print($items[1]);
    return $items;
    
}

/**
 * Display embedded moduleinstance file.
 * @param object $moduleinstance module instance 
 * @param object $cm
 * @param object $course
 * @param stored_file $file main file
 * @return does not return
 */
// function poster_display_embed($moduleinstance, $cm, $course, $file) {
//     global $CFG, $PAGE, $OUTPUT;

//     $clicktoopen = poster_get_clicktoopen($file, $moduleinstance->revision);

//     $context = context_module::instance($cm->id);
//     $path = '/'.$context->id.'/mod_poster/content/'.$moduleinstance->revision.$file->get_filepath().$file->get_filename();
//     $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);
//     $moodleurl = new moodle_url('/pluginfile.php' . $path);

//     $mimetype = $file->get_mimetype();
//     $title    = $moduleinstance->name;

//     $extension = resourcelib_get_extension($file->get_filename());

//     $mediamanager = core_media_manager::instance($PAGE);
//     $embedoptions = array(
//         core_media_manager::OPTION_TRUSTED => true,
//         core_media_manager::OPTION_BLOCK => true,
//     );

//     if (file_mimetype_in_typegroup($mimetype, 'web_image')) {  // It's an image
//         $code = resourcelib_embed_image($fullurl, $title);

//     } else if ($mimetype === 'application/pdf') {
//         // PDF document
//         $code = resourcelib_embed_pdf($fullurl, $title, $clicktoopen);

//     } else if ($mediamanager->can_embed_url($moodleurl, $embedoptions)) {
//         // Media (audio/video) file.
//         $code = $mediamanager->embed_url($moodleurl, $title, 0, 0, $embedoptions);

//     } else {
//         // We need a way to discover if we are loading remote docs inside an iframe.
//         $moodleurl->param('embed', 1);

//         // anything else - just try object tag enlarged as much as possible
//         $code = resourcelib_embed_general($moodleurl, $title, $clicktoopen, $mimetype);
//     }

//     // resource_print_header($moduleinstance, $cm, $course);
//     // resource_print_heading($moduleinstance, $cm, $course);

//     echo $code;

//     // resource_print_intro($moduleinstance, $cm, $course);

//     echo $OUTPUT->footer();
//     die;
// }

/**
 * Internal function - create click to open text with link.
 */
// function poster_get_clicktoopen($file, $revision, $extra='') {
//     global $CFG;

//     $filename = $file->get_filename();

//     $path = '/'.$file->get_contextid().'/mod_poster/content/'.$revision.$file->get_filepath().$file->get_filename();

//     $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);

//     $string = get_string('clicktoopen2', 'poster', "<a href=\"$fullurl\" $extra>$filename</a>");

//     return $string;
// }

/**
 * File browsing support class
 */
// class inter_content_file_info extends file_info_stored {
//     public function get_parent() {
//         if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
//             return $this->browser->get_file_info($this->context);
//         }
//         return parent::get_parent();
//     }
//     public function get_visible_name() {
//         if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
//             return $this->topvisiblename;
//         }
//         return parent::get_visible_name();
//     }
// }

// moodle 
/**
 * File browsing support class
 */
class poster_content_file_info extends file_info_stored {
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }
    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}

/**
 * Get the fields from the Resourcespae metadata
 */
function get_file_fields_metadata($string)
{
    $api_result = do_api_search($string, 'do_search');
    return $api_result;
}


/**
 * Do an API requeuest with 
 */
function do_api_search($string, $function)
{
    $RS_object = init_resourcespace();
    // Set the private API key for the user (from the user account page) and the user we're accessing the system as.
    // $private_key="9885aec8ea7eb2fb8ee45ff110773a5041030a7bdf7abb761c9e682de7f03045";
    $private_key = $RS_object->api_key;

    $user="admin";
    $user = $RS_object->api_user;

    $url = $RS_object->resourcespace_api_url ;
    // Formulate the query
    $query="user=" . $user . "&function=".$function."&param1=".$string."&param2=&param3=&param4=&param5=&param6=";

    // Sign the query using the private key
    $sign=hash("sha256",$private_key . $query);

    // Make the request and output the JSON results.
    // $results=json_decode(file_get_contents("https://resourcespace.lmta.lt/api/?" . $query . "&sign=" . $sign));
    $results=json_decode(file_get_contents($url . $query . "&sign=" . $sign));
    $results=file_get_contents($url . $query . "&sign=" . $sign);
    $results=json_decode(file_get_contents($url . $query . "&sign=" . $sign), TRUE);
    // print_r($results);
    
    $result = [];
    $result[0] = "https://resourcespace.lmta.lt/api/?" . $query . "&sign=" . $sign;
    $result[1] = $results;

    poster_print($result[0]);
    return $result;
}


/**
 * Initialise Resourcespace API variables
 */
function init_resourcespace()
{
    $RS_object = [];
    $RS_object->config          = get_config('resourcespace');
    $RS_object->resourcespace_api_url = get_config('resourcespace', 'resourcespace_api_url');
    $RS_object->api_key         = get_config('resourcespace', 'api_key');
    $RS_object->api_user        = get_config('resourcespace', 'api_user');
    $RS_object->enable_help     = get_config('resourcespace', 'enable_help');
    $RS_object->enable_help_url = get_config('resourcespace', 'enable_help_url');

    poster_print($RS_object->api_key);
    return $RS_object;
}