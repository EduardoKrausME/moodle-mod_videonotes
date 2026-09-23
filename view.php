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
 * Student view for Video Notes.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_videonotes\note_manager;
use mod_videonotes\player_config;

require('../../config.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('videonotes', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('videonotes', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videonotes:view', $context);

$PAGE->set_url('/mod/videonotes/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($activity->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$completion = new completion_info($course);
if ($completion->is_enabled($cm)) {
    $completion->set_module_viewed($cm);
}
$event = \mod_videonotes\event\course_module_viewed::create([
    'objectid' => $activity->id,
    'context' => $context,
]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('videonotes', $activity);
$event->trigger();

$progress = $DB->get_record('videonotes_progress', [
    'videonotesid' => $activity->id,
    'userid' => $USER->id,
]);
if (!$progress) {
    $progress = (object)[
        'duration' => 0,
        'lastposition' => 0,
        'uniquewatched' => 0,
        'totalwatchtime' => 0,
        'percent' => 0,
        'watchedsegments' => '[]',
    ];
}

$manager = new note_manager();
$notes = [];
foreach ($manager->get_user_notes((int)$activity->id, (int)$USER->id) as $note) {
    $notes[] = [
        'id' => (int)$note->id,
        'timecode' => (float)$note->timecode,
        'timestring' => note_manager::format_time((float)$note->timecode),
        'category' => $note->category,
        'categorylabel' => get_string('category:' . $note->category, 'videonotes'),
        'note' => $note->note,
        'rawnote' => $note->note,
        'shared' => !empty($note->shared),
        'shareavailable' => (int)$activity->notemode === 1,
    ];
}

$player = player_config::build($activity, $context);
$config = [
    'cmid' => (int)$cm->id,
    'player' => $player,
    'lastposition' => (float)$progress->lastposition,
    'resumeplayback' => (int)$activity->resumeplayback,
    'allowseek' => !empty($activity->allowseek),
    'segments' => json_decode((string)$progress->watchedsegments, true) ?: [],
    'shareavailable' => (int)$activity->notemode === 1,
];

$categories = [];
foreach (note_manager::CATEGORIES as $category) {
    $categories[] = ['value' => $category, 'label' => get_string('category:' . $category, 'videonotes')];
}

$data = [
    'name' => format_string($activity->name),
    'intro' => format_module_intro('videonotes', $activity, $cm->id),
    'hasintro' => trim((string)$activity->intro) !== '',
    'configjson' => json_encode($config, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
    'notes' => $notes,
    'hasnotes' => !empty($notes),
    'notecount' => count($notes),
    'categories' => $categories,
    'shareavailable' => (int)$activity->notemode === 1,
    'completionnotes' => (int)$activity->completionnotes,
    'requiresnotes' => (int)$activity->completionnotes > 0,
    'completionnotesmessage' => (int)$activity->completionnotes > 0
        ? get_string('completiondetail:notes', 'videonotes', (int)$activity->completionnotes) : '',
    'completionpercent' => (int)$activity->completionpercent,
    'requirespercent' => (int)$activity->completionpercent > 0,
    'completionpercentmessage' => (int)$activity->completionpercent > 0
        ? get_string('completiondetail:percent', 'videonotes', (int)$activity->completionpercent) : '',
    'percent' => round((float)$progress->percent, 2),
    'percentrounded' => (int)round((float)$progress->percent),
    'exportcsvurl' => (new moodle_url('/mod/videonotes/export.php', ['id' => $cm->id, 'format' => 'csv']))->out(false),
    'exporttxturl' => (new moodle_url('/mod/videonotes/export.php', ['id' => $cm->id, 'format' => 'txt']))->out(false),
    'canviewreport' => (int)$activity->notemode === 1 && has_capability('mod/videonotes:viewsharednotes', $context),
    'reporturl' => (new moodle_url('/mod/videonotes/report.php', ['id' => $cm->id]))->out(false),
];

$PAGE->requires->strings_for_js([
    'resumequestion', 'resumeyes', 'resumeno', 'trackingerror', 'seekblocked', 'confirmdelete',
    'savenoteerror', 'playererror', 'nonotesmatch', 'category:important', 'category:question',
    'category:review', 'category:example',
], 'videonotes');
$PAGE->requires->js_call_amd('mod_videonotes/app', 'init');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_videonotes/view', $data);
echo $OUTPUT->footer();
