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
 * English strings for Video Notes.
 *
 * @package mod_videonotes
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addnoteatcurrenttime'] = 'Add note at this moment';
$string['allowseek'] = 'Allow free seeking';
$string['category'] = 'Category';
$string['category:example'] = 'Example';
$string['category:important'] = 'Important';
$string['category:question'] = 'Question';
$string['category:review'] = 'Review';
$string['completiondetail:notes'] = 'Create at least {$a} note(s)';
$string['completiondetail:percent'] = 'Watch at least {$a}% of the video';
$string['completionnotes'] = 'Minimum number of notes';
$string['completionnotes_help'] = 'Set the minimum number of notes the student must create to complete the activity. Use 0 to disable this rule.';
$string['completionnoteserror'] = 'The minimum number of notes must be greater than zero, or 0 to disable it.';
$string['completionpercent'] = 'Required watched percentage';
$string['completionpercent_help'] = 'Set from 1 to 100 to require a minimum percentage of unique video content actually watched. Use 0 to disable this rule.';
$string['completionpercenterror'] = 'The watched percentage must be between 1 and 100, or 0 to disable it.';
$string['completionrules'] = '';
$string['confirmdelete'] = 'Delete this note?';
$string['emptynote'] = 'The note cannot be empty.';
$string['errormaxfiles'] = 'Only one file can be uploaded.';
$string['eventcoursemoduleviewed'] = 'Video Notes activity viewed';
$string['exportcsv'] = 'Export CSV';
$string['exportednotes'] = 'Notes ordered by video time';
$string['exporttxt'] = 'Export text';
$string['invalidvimeourl'] = 'Enter a valid Vimeo URL.';
$string['invalidyoutubeurl'] = 'Enter a valid YouTube URL.';
$string['modulename'] = 'Video Notes';
$string['modulename_help'] = 'A video activity where each student creates private timestamped notes while watching.';
$string['modulenameplural'] = 'Video Notes';
$string['mynotes'] = 'My notes';
$string['nonotesmatch'] = 'No notes match this search.';
$string['nonotesyet'] = 'You have not created any notes yet.';
$string['nosharednotes'] = 'No student has shared notes with teachers yet.';
$string['note'] = 'Note';
$string['notemode'] = 'Note privacy';
$string['notemode_help'] = 'Private notes can only be viewed by the student. In optional sharing mode, each note remains private unless the student explicitly shares it with teachers.';
$string['notemodeprivate'] = 'All notes are private';
$string['notemodeshareoptional'] = 'Students may share individual notes with teachers';
$string['notesettings'] = 'Notes';
$string['notetimeline'] = 'Timeline of my notes';
$string['playererror'] = 'The configured video player could not be loaded.';
$string['pluginadministration'] = 'Video Notes Administration';
$string['pluginname'] = 'Video Notes';
$string['printnotes'] = 'Print';
$string['privacy:metadata:notes'] = 'Timestamped notes created by the student.';
$string['privacy:metadata:notes:category'] = 'The selected note category.';
$string['privacy:metadata:notes:note'] = 'The note text.';
$string['privacy:metadata:notes:shared'] = 'Whether the student chose to share the note with teachers.';
$string['privacy:metadata:notes:timecode'] = 'The video position associated with the note.';
$string['privacy:metadata:notes:timecreated'] = 'When the note was created.';
$string['privacy:metadata:notes:timemodified'] = 'When the note was last changed.';
$string['privacy:metadata:notes:userid'] = 'The user who owns the note.';
$string['privacy:metadata:progress'] = 'Video viewing progress for the student.';
$string['privacy:metadata:progress:duration'] = 'Known video duration.';
$string['privacy:metadata:progress:lastposition'] = 'Last playback position.';
$string['privacy:metadata:progress:percent'] = 'Unique watched percentage.';
$string['privacy:metadata:progress:timecreated'] = 'When progress tracking started.';
$string['privacy:metadata:progress:timemodified'] = 'When progress was last updated.';
$string['privacy:metadata:progress:totalwatchtime'] = 'Total playback time including rewatches.';
$string['privacy:metadata:progress:uniquewatched'] = 'Unique seconds of video watched.';
$string['privacy:metadata:progress:userid'] = 'The user whose progress is stored.';
$string['privacy:metadata:progress:watchedsegments'] = 'Merged video ranges that were actually played.';
$string['privacy:notespath'] = 'Video notes';
$string['privacy:progresspath'] = 'Video progress';
$string['resumeask'] = 'Ask before resuming';
$string['resumeautomatic'] = 'Automatically resume from the last position';
$string['resumefromstart'] = 'Always start from the beginning';
$string['resumeno'] = 'Start over';
$string['resumeplayback'] = 'Playback resume';
$string['resumequestion'] = 'You stopped at {$a}. Resume from this point?';
$string['resumeyes'] = 'Resume';
$string['savenoteerror'] = 'The note could not be saved.';
$string['searchnotes'] = 'Search my notes';
$string['seekblocked'] = 'You cannot skip ahead to a part that has not been watched yet.';
$string['shared'] = 'Shared';
$string['sharednotesreport'] = 'Shared notes';
$string['sharedwithteacher'] = 'Shared with teacher';
$string['sharewithteacher'] = 'Share this note with teachers';
$string['sharingdisabled'] = 'Sharing notes with teachers is disabled for this activity.';
$string['sourceupload'] = 'Upload to Moodle';
$string['sourceurl'] = 'Direct URL';
$string['sourcevimeo'] = 'Vimeo';
$string['sourceyoutube'] = 'YouTube';
$string['student'] = 'Student';
$string['timestamp'] = 'Time';
$string['trackingerror'] = 'Video progress could not be synchronized.';
$string['video'] = 'Video';
$string['videofile'] = 'Video file';
$string['videonotes:addinstance'] = 'Add a new Video Notes activity';
$string['videonotes:view'] = 'View Video Notes activity';
$string['videonotes:viewsharednotes'] = 'View notes explicitly shared by students';
$string['videonotesname'] = 'Activity name';
$string['videosettings'] = 'Video';
$string['videosource'] = 'Video source';
$string['videourl'] = 'Video URL';
$string['yourprogress'] = 'Your video progress';
