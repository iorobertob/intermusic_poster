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
 
if ($hassiteconfig) {
    // $ADMIN->add('mediaposters', new admin_category('mod_mposter_settings', new lang_string('pluginname', 'mod_mposter')));
    $settingspage = new admin_settingpage('managemodmposter', new lang_string('manage', 'mod_mposter'));
 
    if ($ADMIN->fulltree) {
        $settingspage->add(new admin_setting_configcheckbox(
            'mod_poster/showinnavigation',
            new lang_string('showinnavigation', 'mod_mposter'),
            new lang_string('showinnavigation_desc', 'mod_mposter'),
            1
        ));
    }
 
    $ADMIN->add('mediaposters', $settingspage);
}