<?php
// This file is part of Moodle - https://moodle.org/
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
 * Adds admin settings for the plugin.
 *
 * @package     mod_mposter
 * @category    admin
 * @copyright   2020 Your Name <email@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die();
 
if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/mposter/locallib.php');

    // Introductory explanation that all the settings are defaults for the add lesson form.
    $settings->add(new admin_setting_heading('mod_mposter/intro', '', get_string('default_titles', 'mposter')));

    $settings->add(new admin_setting_configtext('mod_mposter/meta1', get_string('meta_label_1', 'mposter')." ".get_string('meta_title','mposter'),
            '', "Composer", PARAM_TEXT));

    $settings->add(new admin_setting_configtext('mod_mposter/meta2', get_string('meta_label_2', 'mposter')." ".get_string('meta_title','mposter'),
            '', "Title", PARAM_TEXT));

    $settings->add(new admin_setting_configtext('mod_mposter/meta3', get_string('meta_label_3', 'mposter')." ".get_string('meta_title','mposter'),
            '', "Title - EN", PARAM_TEXT));

    $settings->add(new admin_setting_configtext('mod_mposter/meta4', get_string('meta_label_4', 'mposter')." ".get_string('meta_title','mposter'),
            '', "Surtitle", PARAM_TEXT));

    $settings->add(new admin_setting_configtext('mod_mposter/meta5', get_string('meta_label_5', 'mposter')." ".get_string('meta_title','mposter'),
            '', "List", PARAM_TEXT));

    $settings->add(new admin_setting_configtext('mod_mposter/meta6', get_string('meta_label_6', 'mposter')." ".get_string('meta_title','mposter'),
            '', "1st Line", PARAM_TEXT));

    $settings->add(new admin_setting_configtext('mod_mposter/meta7', get_string('meta_label_7', 'mposter')." ".get_string('meta_title','mposter'),
            '', "Language", PARAM_TEXT));
}