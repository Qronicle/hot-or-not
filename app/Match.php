<?php
/**
 * Match.php
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Match
 *
 * The Match model contains information about all face-offs between two subjects
 *
 * Note that ID of subject1 will always be the smallest ID of both subjects, so no duplicate rows are created
 *
 * @since       2016-10-16 18:18
 * @author      ruud.seberechts
 */
class Match extends Model
{

}