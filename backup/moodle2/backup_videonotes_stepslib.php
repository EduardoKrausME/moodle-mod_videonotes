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
 * Backup structure for Video Notes.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Defines the backup XML structure.
 */
class backup_videonotes_activity_structure_step extends backup_activity_structure_step {
    /**
     * Defines XML elements and sources.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value('userinfo');

        $videonotes = new backup_nested_element('videonotes', ['id'], [
            'name', 'intro', 'introformat', 'videosource', 'videourl', 'resumeplayback', 'allowseek',
            'notemode', 'completionpercent', 'completionnotes', 'timecreated', 'timemodified',
        ]);
        $progresses = new backup_nested_element('progresses');
        $progress = new backup_nested_element('progress', ['id'], [
            'userid', 'duration', 'lastposition', 'uniquewatched', 'totalwatchtime', 'percent',
            'watchedsegments', 'timecreated', 'timemodified',
        ]);
        $notes = new backup_nested_element('notes');
        $note = new backup_nested_element('note', ['id'], [
            'userid', 'timecode', 'category', 'note', 'shared', 'timecreated', 'timemodified',
        ]);

        $videonotes->add_child($progresses);
        $progresses->add_child($progress);
        $videonotes->add_child($notes);
        $notes->add_child($note);

        $videonotes->set_source_table('videonotes', ['id' => backup::VAR_ACTIVITYID]);
        if ($userinfo) {
            $progress->set_source_table('videonotes_progress', ['videonotesid' => backup::VAR_PARENTID]);
            $note->set_source_table('videonotes_notes', ['videonotesid' => backup::VAR_PARENTID]);
        }

        $progress->annotate_ids('user', 'userid');
        $note->annotate_ids('user', 'userid');
        $videonotes->annotate_files('mod_videonotes', 'video', null);

        return $this->prepare_activity_structure($videonotes);
    }
}
