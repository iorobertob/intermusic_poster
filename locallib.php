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
    
    if (count($files) > 0) {
        $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
    }
    else{
        poster_print($e);
        $url = 'no file';
    }

    
    return $url;
}

/**
 * Get Metadata from Resource Space based on the Metadata File added on the settings of this activity
 * @param $context  The Context of this activity / module
 * @param @poster   The current module's instance
 */
function poster_get_metadata($cmid, $poster)
{
    global $DB;
    $context = context_module::instance($cmid);
    try{
        // Retrieve elements from filename divided by "_"s
        // collection[0]= collection section in filename, collection[1]=whole filename
        $collection = get_item_from_filename($context, 0, $poster->id);

        // If there was no file then we cut short here. 
        if ($collection == null){
            return;
        }


        $DB->set_field('poster', 'rs_collection', $collection[0], array('name' => $poster->name));

        // Findout which ID corresponds to this file in RS
        $request_json     = get_file_fields_metadata($collection[1]);
        $resourcespace_id = $request_json[1][0]["ref"];
   
        $DB->set_field('poster', 'rs_id', $resourcespace_id, array('name' => $poster->name));
   
        // $list_metadata[0] = ($poster->meta1 != "" ? $poster->meta1 : "Composer");
        // $list_metadata[1] = ($poster->meta2 != "" ? $poster->meta2 : "Title");
        // $list_metadata[2] = ($poster->meta3 != "" ? $poster->meta3 : "Surtitle");
        // $list_metadata[3] = ($poster->meta4 != "" ? $poster->meta4 : "List");
        // $list_metadata[4] = ($poster->meta5 != "" ? $poster->meta5 : "1st line");
        // $list_metadata[5] = ($poster->meta6 != "" ? $poster->meta6 : "Language");

        $list_metadata[0] = ($poster->meta1 != "" ? "" : "");
        $list_metadata[1] = ($poster->meta2 != "" ? "" : "");
        $list_metadata[2] = ($poster->meta3 != "" ? "" : "");
        $list_metadata[3] = ($poster->meta4 != "" ? "" : "");
        $list_metadata[4] = ($poster->meta5 != "" ? "" : "");
        $list_metadata[5] = ($poster->meta6 != "" ? "" : "");
        
        $metadata = get_metadata_from_api($resourcespace_id, $poster, $list_metadata);

        // Commit metadata to database
        $length = count($metadata);
        for ($i = 0; $i < $length; $i++) {
            // if($metadata[$i] != NULL){
                $index = $i + 1;
                $data = $metadata[$i];
                // if (mb_detect_encoding($metadata[$i]) === 'ASCII'){
                //     poster_print('ASCII CONVERSION');
                //     $data = iconv('ASCII', 'UTF-8//IGNORE', $metadata[$i]);
                // }
                // else{
                //     $data = $metadata[$i];
                // }
                
                $DB->set_field('poster', 'meta_value'.$index, $data, array('name' => $poster->name));
                $DB->set_field('poster', 'meta'.$index, $list_metadata[$i],  array('name' => $poster->name));
            // }
        }

    }catch (Exception $e){
        poster_print($e);
    }
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
    
    // // Get files array and their names, split them by '_' and return the first of those divisions. 
    $fs              = get_file_storage();
    $files           = $fs->get_area_files($context->id, 'mod_poster', 'content', 0, 'sortorder', false);

    if (count($files) > 0){

        $keys            = array_keys($files);
        $filename        = $files[$keys[0]] -> get_filename();
        $filename_parts  = explode("_", $filename);
        $item            = $filename_parts[$item_number];
        $characteristics = $filename_parts[2];
    
        $items[0] = $item;
        $items[1] = $filename;
    
        return $items;
    }
    else{
        return null;
    }
    
    
}

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
    
    $result = [];
    $result[0] = "https://resourcespace.lmta.lt/api/?" . $query . "&sign=" . $sign;
    $result[1] = $results;

    return $result;
}

/**
 * Initialise Resourcespace API variables
 */
function init_resourcespace()
{
    $RS_object = new stdClass;
    $RS_object->config          = get_config('resourcespace');
    $RS_object->resourcespace_api_url = get_config('resourcespace', 'resourcespace_api_url');
    $RS_object->api_key         = get_config('resourcespace', 'api_key');
    $RS_object->api_user        = get_config('resourcespace', 'api_user');
    $RS_object->enable_help     = get_config('resourcespace', 'enable_help');
    $RS_object->enable_help_url = get_config('resourcespace', 'enable_help_url');

    return $RS_object;
}

/**
 * Get the data via API call and compare its metadata with the one indicated in the current Inter list instance
 */
function get_metadata_from_api($resourcespace_id, $moduleinstance, $list_metadata)
{
    global $PAGE, $DB, $CFG;
    $prefix = $CFG->prefix;

    $result = do_api_search($resourcespace_id, 'get_resource_field_data');

    $new_list_metadata = array_fill(0, sizeof($list_metadata), '');
    for($i = 0; $i <= sizeof($list_metadata); $i++)
    {
        foreach($result[1] as $row)
        {
            if ($row["title"] === $list_metadata[$i])
            {
                $new_list_metadata[$i] = $row["value"];
            }
        }
    } 
    return $new_list_metadata;
}
