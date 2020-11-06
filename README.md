Moodle Media Poster activity module [INTERMUSIC]
=============================

Important!!
----------
- This is a fork of the original 'Media Poster' plugin, which is customised to work together with other Intermusic project plugins and therefore does not follow the releaes of the original plugin, and will likely have a name space collision if trying to use this version parallel to the original.
- When installing the name of this module's folder should be named 'mdposter'.

Differences with origintal Media Poster:
----------
- This version has settings to add metadata to this activity, that is late read by the 'Intermusic Database' activity plugin (https://github.com/iorobertob/intermusic-database)
- This metadata is retreived from the Intermusic project's asset management system(AMS). (https://resourcespace.lmta.lt) or it can be manually input.
- It can take a metadata file, whose sole purpose is to provide some strings to look for in the AMS. 


How does this module get its metadata
----------
- The module has settings for  5 metadata field titles and values, that can be filled manually or sought for in the AMS. 
- The mmodule also has the option to upload a metadata file. The metadata file contains in its name, the ID of a collection in the AMS, and performs an API call to retreive a json list with information about files of that collection. 
- From the info of the collection, the script looks for the fields with the titles set in this module's configuration. If they are found, they are used to fill the values in this module's info. If the metadata field titles in the config are left empty, the mdposter will use the default values:  	 "Composer";
            		"Title";
            		"Title - English";
            		"Surtitle";
            		"List";
            		"1st line";
            		"Language";



Moodle Media Poster activity module [ORIGINAL]
=============================

[![Build status](https://travis-ci.org/mudrd8mz/moodle-mod_mdposter.svg?branch=master)](https://travis-ci.org/mudrd8mz/moodle-mod_mdposter)

Media Poster is a resource activity module for Moodle. It allows teachers to create a page for their students. What makes this module
unique is that the contents of the mdposter page is composed of Moodle blocks (such as HTML blocks, Calendar block, Latest news block
etc.). It provides yet another place within the course where blocks can be put without polluting the course front page.



Motivation
----------

There are many useful blocks available for Moodle. Typically, they can be only added to the sides of the Moodle pages, or to the
user's dashboard page (also known as My home page). Sometimes, you may want to keep your course main page quite clean, not cluttered
with blocks on both sides. In such case, you can put useful blocks into a separate Media Poster page.

The overall concept is somewhat similar to how pages are created in Mahara - but it is typically the teacher in Moodle who creates
the Media Poster for students to view.

Usage
-----

To use the module, you should understand how Moodle sticky blocks work. See [Block
settings](https://docs.moodle.org/en/Block_settings) page for more details.

1. Add the module instance into the course.
2. Keep the editing mode on.
3. Add the Moodle blocks you want to display on the mdposter.
4. Click the icon to configure the block. Set the block instance so that it is displayed in the context of the
   mdposter, on page type _Media Poster module main page_ (`mod-mdposter-view`), inside the region `mod_mdposter-pre` or `mod_mdposter-post`.
5. Alternatively, use the drag and drop feature to move the block to the regions at the mdposter content area.
6. Note that some blocks must be first added to the course main page first, configured to be displayed at any page and then
   configured again to be displayed at the mdposter main page only (this is how block positioning works in Moodle generally).

The mdposter can be used as for example:

* Course wall/dashboard (contact teachers, detailed outline of the course, latest news, comments, ...).
* Project dashboard (project goals, calendar, comments, people, ...)
* Research report (goals, methods, results, comments, ...)

Implementation
--------------

The Media Poster module uses not so well known feature of the Moodle blocks architecture. In almost all cases, it is the theme that
defines regions where plugins can be added to. However in special cases, such as this one, any Moodle plugin can define its custom
block regions.  Within the context of the Media Poster module instance, when displaying its view.php page, two extra block regions are
defined - `mod_mdposter-pre` and `mod_mdposter-post`. The Media Poster module itself is just a tiny wrapper for displaying these two regions
as its content. Simple and clever.

The module natively supports responsive layout in bootstrap based themes (both 2.x and 3.x versions).

Author
------

* The Media Poster module has been written and is currently maintained by David Mudrák <david@moodle.com>
* The current plugin is a modified version of David's as part of Intermusic Project.

Licence
-------

Copyright (C) 2015 David Mudrák <david@moodle.com>

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as
published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see
<http://www.gnu.org/licenses/>.


TODOs
-------
* Perform migration from original mdposter plugin, which was used as template for the current module. 
