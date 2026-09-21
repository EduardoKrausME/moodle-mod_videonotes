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
 * Core callbacks for Video Notes.
 *
 * @package   mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the Moodle features supported by this activity.
 *
 * @param string $feature Moodle feature constant.
 * @return bool|string|null
 */
function videonotes_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_CONTENT;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        default:
            return null;
    }
}

/**
 * Adds a Video Notes instance.
 *
 * @param stdClass $data Form data.
 * @param mod_videonotes_mod_form|null $mform Form instance.
 * @return int
 */
function videonotes_add_instance(stdClass $data, ?mod_videonotes_mod_form $mform = null): int {
    global $DB;

    $now = time();
    $data->timecreated = $now;
    $data->timemodified = $now;
    $id = $DB->insert_record('videonotes', $data);
    $data->id = $id;
    videonotes_save_video_file($data);
    return $id;
}

/**
 * Updates a Video Notes instance.
 *
 * @param stdClass $data Form data.
 * @param mod_videonotes_mod_form|null $mform Form instance.
 * @return bool
 */
function videonotes_update_instance(stdClass $data, ?mod_videonotes_mod_form $mform = null): bool {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();
    $result = $DB->update_record('videonotes', $data);
    videonotes_save_video_file($data);
    return $result;
}

/**
 * Deletes a Video Notes instance and associated user data.
 *
 * @param int $id Instance id.
 * @return bool
 */
function videonotes_delete_instance(int $id): bool {
    global $DB;

    $activity = $DB->get_record('videonotes', ['id' => $id]);
    if (!$activity) {
        return false;
    }

    $cm = get_coursemodule_from_instance('videonotes', $id, $activity->course, false, IGNORE_MISSING);
    if ($cm) {
        $context = context_module::instance($cm->id);
        get_file_storage()->delete_area_files($context->id, 'mod_videonotes');
    }

    $DB->delete_records('videonotes_notes', ['videonotesid' => $id]);
    $DB->delete_records('videonotes_progress', ['videonotesid' => $id]);
    $DB->delete_records('videonotes', ['id' => $id]);
    return true;
}

/**
 * Saves the uploaded video file from the draft area.
 *
 * @param stdClass $data Activity record.
 * @return void
 */
function videonotes_save_video_file(stdClass $data): void {
    $cmid = !empty($data->coursemodule) ? (int)$data->coursemodule : 0;
    if (!$cmid) {
        $cm = get_coursemodule_from_instance('videonotes', $data->id, $data->course, false, IGNORE_MISSING);
        $cmid = $cm ? (int)$cm->id : 0;
    }
    if (!$cmid) {
        return;
    }

    $context = context_module::instance($cmid);
    if ((int)$data->videosource !== 0) {
        get_file_storage()->delete_area_files($context->id, 'mod_videonotes', 'video', 0);
        return;
    }
    if (!isset($data->videofile)) {
        return;
    }

    file_save_draft_area_files(
        (int)$data->videofile,
        $context->id,
        'mod_videonotes',
        'video',
        0,
        ['subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => ['video']]
    );
}

/**
 * Serves protected uploaded videos.
 *
 * @param stdClass $course Course record.
 * @param stdClass $cm Course module record.
 * @param context $context Context.
 * @param string $filearea File area.
 * @param array $args File path arguments.
 * @param bool $forcedownload Force download flag.
 * @param array $options Serving options.
 * @return bool
 */
function mod_videonotes_pluginfile($course, $cm, $context, string $filearea, array $args,
                                   bool $forcedownload, array $options = []): bool {
    if ($context->contextlevel !== CONTEXT_MODULE || $filearea !== 'video') {
        return false;
    }

    require_login($course, true, $cm);
    require_capability('mod/videonotes:view', $context);

    $filename = array_pop($args);
    $filepath = '/' . ($args ? implode('/', $args) . '/' : '');
    $file = get_file_storage()->get_file(
        $context->id,
        'mod_videonotes',
        'video',
        0,
        $filepath,
        $filename
    );
    if (!$file || $file->is_directory()) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Returns file areas.
 *
 * @param stdClass $course Course record.
 * @param stdClass $cm Course module record.
 * @param context $context Context.
 * @return array
 */
function videonotes_get_file_areas($course, $cm, $context): array {
    return ['video' => get_string('videofile', 'videonotes')];
}

/**
 * Adds course page information and custom completion metadata.
 *
 * @param stdClass $cm Course module record.
 * @return cached_cm_info|null
 */
function videonotes_get_coursemodule_info(stdClass $cm): ?cached_cm_info {
    global $DB;

    $activity = $DB->get_record('videonotes', ['id' => $cm->instance],
        'id,name,intro,introformat,completionpercent,completionnotes');
    if (!$activity) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $activity->name;
    if ($cm->showdescription) {
        $info->content = format_module_intro('videonotes', $activity, $cm->id, false);
    }
    if ((int)$cm->completion === COMPLETION_TRACKING_AUTOMATIC) {
        $info->customdata['customcompletionrules'] = [
            'completionpercent' => (int)$activity->completionpercent,
            'completionnotes' => (int)$activity->completionnotes,
        ];
    }
    return $info;
}

/**
 * Returns active custom completion descriptions.
 *
 * @param cached_cm_info $cm Course module information.
 * @return array
 */
function videonotes_get_completion_active_rule_descriptions(cached_cm_info $cm): array {
    if ((int)$cm->completion !== COMPLETION_TRACKING_AUTOMATIC || empty($cm->customdata['customcompletionrules'])) {
        return [];
    }

    $rules = $cm->customdata['customcompletionrules'];
    $descriptions = [];
    if (!empty($rules['completionpercent'])) {
        $descriptions[] = get_string('completiondetail:percent', 'videonotes', $rules['completionpercent']);
    }
    if (!empty($rules['completionnotes'])) {
        $descriptions[] = get_string('completiondetail:notes', 'videonotes', $rules['completionnotes']);
    }
    return $descriptions;
}

/**
 * Legacy completion callback.
 *
 * @param stdClass $course Course.
 * @param stdClass $cm Course module.
 * @param int $userid User id.
 * @param bool $type Expected completion state.
 * @return bool
 */
function videonotes_get_completion_state($course, $cm, int $userid, bool $type): bool {
    global $DB;

    $activity = $DB->get_record('videonotes', ['id' => $cm->instance], '*', MUST_EXIST);
    if ((int)$activity->completionpercent > 0) {
        $progress = $DB->get_record('videonotes_progress', [
            'videonotesid' => $activity->id,
            'userid' => $userid,
        ]);
        if (!$progress || (float)$progress->percent < (float)$activity->completionpercent) {
            return false;
        }
    }
    if ((int)$activity->completionnotes > 0) {
        $count = $DB->count_records('videonotes_notes', [
            'videonotesid' => $activity->id,
            'userid' => $userid,
        ]);
        if ($count < (int)$activity->completionnotes) {
            return false;
        }
    }
    return true;
}
