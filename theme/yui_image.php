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
 * This file is responsible for serving of yui images
 *
 * @package   moodlecore
 * @copyright 2009 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// we need just the values from config.php and minlib.php
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // this stops immediately at the beginning of lib/setup.php

$path = min_optional_param('file', '', 'SAFEPATH');

$parts = explode('/', $path);
if (count($parts) != 2) {
    yui_image_not_found();
}
list($version, $image) = $parts;

if ($version == $CFG->yui3version) {
    $imagepath = "$CFG->dirroot/lib/yui/$CFG->yui3version/build/assets/skins/sam/$image";
} else if ($version == $CFG->yui2version) {
    $imagepath = "$CFG->dirroot/lib/yui/$CFG->yui2version/build/assets/skins/sam/$image";
} else {
    yui_image_not_found();
}

if (!file_exists($imagepath)) {
    yui_image_not_found();
}

yui_image_cached($imagepath);



function yui_image_cached($imagepath) {
    $lifetime = 60*60*24*300; // 300 days === forever
    $pathinfo = pathinfo($imagepath);
    $imagename = $pathinfo['filename'].'.'.$pathinfo['extension'];

    switch($pathinfo['extension']) {
        case 'gif' : $mimetype = 'image/gif'; break;
        case 'png' : $mimetype = 'image/png'; break;
        case 'jpg' : $mimetype = 'image/jpeg'; break;
        case 'jpeg' : $mimetype = 'image/jpeg'; break;
        case 'ico' : $mimetype = 'image/vnd.microsoft.icon'; break;
        default: $mimetype = 'document/unknown';
    }

    header('Content-Disposition: inline; filename="'.$imagename.'"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($imagepath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: '.$mimetype);
    header('Content-Length: '.filesize($imagepath));

    while (@ob_end_flush()); //flush the buffers - save memory and disable sid rewrite
    readfile($imagepath);
    die;
}

function yui_image_not_found() {
    header('HTTP/1.0 404 not found');
    die('Image was not found, sorry.');
}