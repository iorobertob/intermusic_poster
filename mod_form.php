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
 * Poster instance settings form is defined here.
 *
 * @package     mod_poster
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Defines the poster instance settings form
 *
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_poster_mod_form extends moodleform_mod {

    /**
     * Defines the fields of the form
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Start the general form section.
        $mform->addElement('header', 'general', get_string('general', 'core_form'));

        // Add the poster name field.
        $mform->addElement('text', 'name', get_string('postername', 'mod_poster'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', 'core', 255), 'maxlength', 255, 'client');

        // Add the poster surtitle field.
        $mform->addElement('text', 'surtitle', get_string('postersurtitle', 'mod_poster'), array('size' => '64'));
        $mform->setType('surtitle', PARAM_TEXT);
        // $mform->addRule('surtitle', null, 'required', null, 'client');
        $mform->addRule('surtitle', get_string('maximumchars', 'core', 255), 'maxlength', 255, 'client');

        // Add the poster author field.
        $mform->addElement('text', 'author', get_string('posterauthor', 'mod_poster'), array('size' => '64'));
        $mform->setType('author', PARAM_TEXT);
        // $mform->addRule('author', null, 'required', null, 'client');
        $mform->addRule('author', get_string('maximumchars', 'core', 255), 'maxlength', 255, 'client');

        // Add the poster surtitle field.
        $mform->addElement('text', 'numbering', get_string('posternumbering', 'mod_poster'), array('size' => '64'));
        $mform->setType('numbering', PARAM_TEXT);
        // $mform->addRule('numbering', null, 'required', null, 'client');
        $mform->addRule('numbering', get_string('maximumchars', 'core', 255), 'maxlength', 255, 'client');

        // Add the poster surtitle field.
        $mform->addElement('text', 'language', get_string('posterlanguage', 'mod_poster'), array('size' => '64'));
        $mform->setType('language', PARAM_TEXT);
        // $mform->addRule('language', null, 'required', null, 'client');
        $mform->addRule('language', get_string('maximumchars', 'core', 255), 'maxlength', 255, 'client');



        //========================   FILE PIKCER ==========================================
        // $element = $mform->getElement('introeditor');
        // $attributes = $element->getAttributes();
        // $attributes['rows'] = 5;
        // $element->setAttributes($attributes);
        $filemanager_options = array();
        $filemanager_options['accepted_types'] = '*';
        $filemanager_options['maxbytes'] = 0;
        $filemanager_options['maxfiles'] = -1;
        $filemanager_options['mainfile'] = true;

        $mform->addElement('filemanager', 'files', get_string('metadatafile','poster'), null, $filemanager_options);
        //========================   FILE PIKCER ==========================================

        // Add checkbox to indicate whether to autopopulate the previous fields from children block objects
        $mform->addElement('advcheckbox', 'autopopulate', get_string('autopopulate', 'poster'), '', array('group' => 1), array(0, 1));

        // Add the show name at the view page field.
        $mform->addElement('advcheckbox', 'shownameview', get_string('shownameview', 'mod_poster'));
        $mform->addHelpButton('shownameview', 'shownameview', 'mod_poster');

        // Add the instruction/description field.
        if ($CFG->version >= 2015051100) {
            // Moodle 2.9.0 and higher use the new API.
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Add the show description at the view page field.
        $mform->addElement('advcheckbox', 'showdescriptionview', get_string('showdescriptionview', 'mod_poster'));
        $mform->addHelpButton('showdescriptionview', 'showdescriptionview', 'mod_poster');

        // Add standard elements.
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values) {
        if ($this->current->instance and !$this->current->tobemigrated) {
            $draftitemid = file_get_submitted_draft_itemid('files');
            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_poster', 'content', 0, array('subdirs'=>true));
            $default_values['files'] = $draftitemid;
        }


}
