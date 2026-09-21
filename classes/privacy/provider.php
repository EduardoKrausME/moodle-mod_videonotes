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
 * Privacy provider for Video Notes.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videonotes\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

/**
 * Describes and manages user data stored by the plugin.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Returns metadata.
     *
     * @param collection $collection Metadata collection.
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('videonotes_notes', [
            'userid' => 'privacy:metadata:notes:userid',
            'timecode' => 'privacy:metadata:notes:timecode',
            'category' => 'privacy:metadata:notes:category',
            'note' => 'privacy:metadata:notes:note',
            'shared' => 'privacy:metadata:notes:shared',
            'timecreated' => 'privacy:metadata:notes:timecreated',
            'timemodified' => 'privacy:metadata:notes:timemodified',
        ], 'privacy:metadata:notes');
        $collection->add_database_table('videonotes_progress', [
            'userid' => 'privacy:metadata:progress:userid',
            'duration' => 'privacy:metadata:progress:duration',
            'lastposition' => 'privacy:metadata:progress:lastposition',
            'uniquewatched' => 'privacy:metadata:progress:uniquewatched',
            'totalwatchtime' => 'privacy:metadata:progress:totalwatchtime',
            'percent' => 'privacy:metadata:progress:percent',
            'watchedsegments' => 'privacy:metadata:progress:watchedsegments',
            'timecreated' => 'privacy:metadata:progress:timecreated',
            'timemodified' => 'privacy:metadata:progress:timemodified',
        ], 'privacy:metadata:progress');
        return $collection;
    }

    /**
     * Returns contexts containing user data.
     *
     * @param int $userid User id.
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {videonotes} v ON v.id = cm.instance
             LEFT JOIN {videonotes_notes} n ON n.videonotesid = v.id AND n.userid = :notesuserid
             LEFT JOIN {videonotes_progress} p ON p.videonotesid = v.id AND p.userid = :progressuserid
                 WHERE n.id IS NOT NULL OR p.id IS NOT NULL";
        $contextlist->add_from_sql($sql, [
            'contextlevel' => CONTEXT_MODULE,
            'modname' => 'videonotes',
            'notesuserid' => $userid,
            'progressuserid' => $userid,
        ]);
        return $contextlist;
    }

    /**
     * Exports user data from approved contexts.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }
            $cm = get_coursemodule_from_id('videonotes', $context->instanceid, 0, false, IGNORE_MISSING);
            if (!$cm) {
                continue;
            }
            $notes = $DB->get_records('videonotes_notes', ['videonotesid' => $cm->instance, 'userid' => $userid], 'timecode');
            $progress = $DB->get_record('videonotes_progress', ['videonotesid' => $cm->instance, 'userid' => $userid]);
            if ($notes) {
                $data = [];
                foreach ($notes as $note) {
                    $data[] = (object)[
                        'timecode' => $note->timecode,
                        'category' => $note->category,
                        'note' => $note->note,
                        'shared' => transform::yesno($note->shared),
                        'timecreated' => transform::datetime($note->timecreated),
                        'timemodified' => transform::datetime($note->timemodified),
                    ];
                }
                writer::with_context($context)->export_data([
                    get_string('privacy:notespath', 'videonotes'),
                ], (object)['notes' => $data]);
            }
            if ($progress) {
                writer::with_context($context)->export_data([get_string('privacy:progresspath', 'videonotes')], (object)[
                    'duration' => $progress->duration,
                    'lastposition' => $progress->lastposition,
                    'uniquewatched' => $progress->uniquewatched,
                    'totalwatchtime' => $progress->totalwatchtime,
                    'percent' => $progress->percent,
                    'watchedsegments' => $progress->watchedsegments,
                    'timecreated' => transform::datetime($progress->timecreated),
                    'timemodified' => transform::datetime($progress->timemodified),
                ]);
            }
        }
    }

    /**
     * Deletes all user data in a module context.
     *
     * @param context $context Context.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;
        if (!$context instanceof \context_module) {
            return;
        }
        $cm = get_coursemodule_from_id('videonotes', $context->instanceid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return;
        }
        $DB->delete_records('videonotes_notes', ['videonotesid' => $cm->instance]);
        $DB->delete_records('videonotes_progress', ['videonotesid' => $cm->instance]);
    }

    /**
     * Deletes one user's data from approved contexts.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }
            $cm = get_coursemodule_from_id('videonotes', $context->instanceid, 0, false, IGNORE_MISSING);
            if (!$cm) {
                continue;
            }
            $DB->delete_records('videonotes_notes', ['videonotesid' => $cm->instance, 'userid' => $userid]);
            $DB->delete_records('videonotes_progress', ['videonotesid' => $cm->instance, 'userid' => $userid]);
        }
    }
}
