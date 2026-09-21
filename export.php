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
 * Exports the authenticated student's own notes.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_videonotes\note_manager;

require('../../config.php');

$id = required_param('id', PARAM_INT);
$format = optional_param('format', 'csv', PARAM_ALPHA);
$cm = get_coursemodule_from_id('videonotes', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('videonotes', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videonotes:view', $context);

$notes = (new note_manager())->get_user_notes((int)$activity->id, (int)$USER->id);
$filename = clean_filename(format_string($activity->name) . '-notes-' . userdate(time(), '%Y%m%d'));

if ($format === 'txt') {
    header('Content-Type: text/plain; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.txt"');
    echo format_string($activity->name) . "\n";
    echo get_string('exportednotes', 'videonotes') . "\n\n";
    foreach ($notes as $note) {
        echo note_manager::format_time((float)$note->timecode) . ' [' .
            get_string('category:' . $note->category, 'videonotes') . '] ' . $note->note . "\n";
    }
    exit;
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
$output = fopen('php://output', 'w');
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, [
    get_string('timestamp', 'videonotes'),
    get_string('category', 'videonotes'),
    get_string('note', 'videonotes'),
    get_string('shared', 'videonotes'),
]);
foreach ($notes as $note) {
    fputcsv($output, [
        note_manager::format_time((float)$note->timecode),
        get_string('category:' . $note->category, 'videonotes'),
        $note->note,
        $note->shared ? get_string('yes') : get_string('no'),
    ]);
}
fclose($output);
exit;
