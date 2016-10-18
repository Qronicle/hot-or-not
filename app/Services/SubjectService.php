<?php
/**
 * SubjectService.php
 */

namespace App\Services;

use App\Match;
use App\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * SubjectService
 *
 * @since       2016-10-18 19:05
 * @author      ruud.seberechts
 */
class SubjectService
{
    /**
     * Get two semi-random subjects to display
     *
     * @param bool $markAsShown Whether to mark both objects as shown. Defaults to TRUE
     *
     * @return Subject[]|bool Returns an array with two subjects with keys 1 and 2, or FALSE when no two subjects could be found
     * @throws \Exception
     */
    public function getSubjectsForMatch($markAsShown = true)
    {
        // We start with a subject that hasn't been shown in a long time
        /** @var Collection $possibleSubjects */
        $possibleSubjects = Subject::orderBy('last_shown_at', 'asc')->take(5)->get();
        if (!$possibleSubjects->count()) return false;
        $subject1 = $possibleSubjects->random();

        // We match with a subject that either wasn't matched yet, or was matched a long time ago
        $query = 'SELECT s.id FROM subjects s 
          WHERE s.id != ? 
          AND NOT EXISTS (
            SELECT * FROM matches m 
            WHERE (m.subject1_id = s.id AND m.subject2_id = ?) 
            OR (m.subject2_id = s.id AND m.subject1_id = ?)
          ) LIMIT 5';
        $newMatches = DB::select($query, [$subject1->id, $subject1->id, $subject1->id]);
        $existingMatches = DB::table('matches')
            ->where('subject1_id', '=', $subject1->id)
            ->orWhere('subject2_id', '=', $subject1->id)
            ->orderBy('last_matched_at', 'asc')
            ->limit(5)
            ->get();
        // When we have new matches and are lucky (or have no existing matches) we pick a new match
        if ($newMatches && (!$existingMatches->count() || rand(0, 100) > 50)) {
            // We haven't ever matched with these subject
            $subject2 = Subject::find($newMatches[array_rand($newMatches)]->id);
            if (!$subject2) {
                throw new \Exception('Could not find subject ' . $newMatches[0]->id);
            }
            // Create a new match
            try {
                $ids = [$subject1->id, $subject2->id];
                sort($ids);
                Match::insert(['subject1_id' => $ids[0], 'subject2_id' => $ids[1]]);
            } catch (QueryException $ex) {
                // Duplicate key exception, would insert ignore
            }
        } else {
            if (!$existingMatches->count()) return false;
            $match      = $existingMatches->random();
            $subject2Id = $match->subject1_id == $subject1->id ? $match->subject2_id : $match->subject1_id;
            $subject2   = Subject::find($subject2Id);
        }
        if (!$subject2) return false;

        if ($markAsShown) {
            $this->markSubjectAsShown($subject1);
            $this->markSubjectAsShown($subject2);
        }

        return rand(0, 100) > 50 ? [1 => $subject1, $subject2] : [1 => $subject2, $subject1];
    }

    public function markSubjectAsShown(Subject $subject)
    {
        DB::table('subjects')->whereId($subject->id)
            ->update(['last_shown_at' => new \DateTime()]);
        DB::table('subjects')->whereId($subject->id)
            ->increment('times_shown', 1);
    }

    /**
     * @param int $wonSubjectId
     * @param int $lostSubjectId
     */
    public function setMatchResult($wonSubjectId, $lostSubjectId)
    {
        // Increase individual won/lost amount for both subjects
        DB::table('subjects')->whereId($wonSubjectId)->increment('times_won', 1);
        DB::table('subjects')->whereId($lostSubjectId)->increment('times_lost', 1);
        $query = 'UPDATE subjects SET '
            . 'percentage_won = (100 * times_won / (times_won + times_lost)) '
            . 'WHERE id = ? OR id = ?';
        DB::update($query, [$wonSubjectId, $lostSubjectId]);
        // Update match data
        $s1Id = $wonSubjectId > $lostSubjectId ? $lostSubjectId : $wonSubjectId;
        $s2Id = $wonSubjectId < $lostSubjectId ? $lostSubjectId : $wonSubjectId;
        $sWonId = $s1Id == $wonSubjectId ? 1 : 2;
        $query = 'UPDATE matches SET '
            . 'times_matched = times_matched + 1, '
            . "last_matched_at = '" . date('Y-m-d H:i:s') . "', "
            . "subject{$sWonId}_wins = subject{$sWonId}_wins + 1 "
            . "WHERE subject1_id = $s1Id AND subject2_id = $s2Id";
        DB::update($query);
    }

    public function getMostPopularSubjects($limit = 50)
    {
        return Subject::where('times_shown', '>', 5)->orderBy('percentage_won', 'desc')->take($limit)->get();
    }

    public function getMatch($subject1Id, $subject2Id) {
        // Swap subject ID when the first is higher than the second
        if ($subject1Id > $subject2Id) {
            $subject1Id += $subject2Id;
            $subject2Id = $subject1Id - $subject2Id;
            $subject1Id -= $subject2Id;
        }
        // Fetch the match
        $match = Match::where('subject1_id', '=', $subject1Id)->where('subject2_id', '=', $subject2Id)->get()->first();
        if ($match) {
            $match->subject1 = Subject::find($match->subject1_id);
            $match->subject2 = Subject::find($match->subject2_id);
        }
        return $match;
    }
}