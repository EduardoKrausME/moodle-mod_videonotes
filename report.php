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
 * Teacher report for notes explicitly shared by students.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_videonotes\note_manager;

require('../../config.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('videonotes', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('videonotes', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videonotes:viewsharednotes', $context);

if ((int)$activity->notemode !== 1) {
    throw new moodle_exception('sharingdisabled', 'videonotes');
}

$PAGE->set_url('/mod/videonotes/report.php', ['id' => $cm->id]);
$PAGE->set_title(get_string('sharednotesreport', 'videonotes'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$userfieldsapi = \core_user\fields::for_identity($context)->with_name();
$identityfields = $userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);
$userfieldssql = $userfieldsapi->get_sql('u', true, '', '', false);

$sql = "SELECT n.*, {$userfieldssql->selects}
          FROM {videonotes_notes} n
          JOIN {user} u ON u.id = n.userid
               {$userfieldssql->joins}
         WHERE n.videonotesid = :activityid AND n.shared = 1
      ORDER BY u.lastname, u.firstname, n.timecode, n.id";
$params = ['activityid' => $activity->id] + $userfieldssql->params;
$records = $DB->get_records_sql($sql, $params);

$rows = [];
foreach ($records as $record) {
    $identity = [];
    foreach ($identityfields as $field) {
        if (!isset($record->{$field}) || $record->{$field} === '') {
            continue;
        }
        $identity[] = [
            'label' => \core_user\fields::get_display_name($field),
            'value' => (string)$record->{$field},
        ];
    }

    $rows[] = [
        'fullname' => fullname($record),
        'identity' => $identity,
        'hasidentity' => !empty($identity),
        'timestring' => note_manager::format_time((float)$record->timecode),
        'categorylabel' => get_string('category:' . $record->category, 'videonotes'),
        'note' => $record->note,
    ];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_videonotes/report', [
    'title' => get_string('sharednotesreport', 'videonotes'),
    'activityname' => format_string($activity->name),
    'rows' => $rows,
    'hasrows' => !empty($rows),
]);
echo $OUTPUT->footer();
