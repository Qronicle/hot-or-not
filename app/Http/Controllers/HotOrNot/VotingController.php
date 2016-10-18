<?php
/**
 * VotingController.php
 */

namespace App\Http\Controllers\HotOrNot;

use App\Http\Controllers\Controller;
use App\Match;
use App\Services\SubjectService;
use App\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * VotingController
 *
 * @since       2016-10-16 12:53
 * @author      ruud.seberechts
 */
class VotingController extends Controller
{
    public function index(SubjectService $subjectService)
    {
        $subjects   = $subjectService->getSubjectsForMatch();
        $sessionKey = $this->setSessionSubjects($subjects);

        return view('public.vote', [
            'subjects'   => $subjects,
            'sessionKey' => $sessionKey,
            'incoming'   => false,
        ]);
    }

    public function vote(Request $request, SubjectService $subjectService)
    {
        // Process the vote
        $this->validateVote($request, $subjectService);

        // When this is not an ajax request, redirect to the homepage
        if (!$request->ajax()) {
            return redirect('/');
        }

        // Get new match data
        $subjects   = $subjectService->getSubjectsForMatch();
        $sessionKey = $this->setSessionSubjects($subjects);

        return view('public.vote-subjects', [
            'subjects'   => $subjects,
            'sessionKey' => $sessionKey,
            'incoming'   => true,
        ]);
    }

    /**
     * Save the two shown subjects to the session so we can check given input later, using the session key returned by
     * this method
     *
     * @param Subject[]|bool $subjects
     *
     * @return bool|string  The key or FALSE when no subjects set
     */
    protected function setSessionSubjects($subjects)
    {
        if (!$subjects) return false;
        $key             = md5(microtime(true));
        $sessionSubjects = request()->session()->get('subjects') ?: [];
        // We don't want a million matches to be kept in the session
        if (count($sessionSubjects) > 20) {
            array_shift($sessionSubjects);
        }
        $sessionSubjects[$key] = [$subjects[1]->id, $subjects[2]->id];
        request()->session()->set('subjects', $sessionSubjects);
        return $key;
    }

    protected function validateVote(Request $request, SubjectService $subjectService)
    {
        // Get input
        $sessionKey = $request->get('key');
        $subjectId  = $request->get('subject');
        // Validate input with session data
        $sessionSubjects = request()->session()->get('subjects') ?: [];
        if (!isset($sessionSubjects[$sessionKey])) return false;
        $subjectIds = $sessionSubjects[$sessionKey];
        if (!in_array($subjectId, $subjectIds)) return false;
        // Save vote
        $subjectService->setMatchResult($subjectId, $subjectIds[0] == $subjectId ? $subjectIds[1] : $subjectIds[0]);
        // Unset the session key
        unset($sessionSubjects[$sessionKey]);
        request()->session()->set('subjects', $sessionSubjects);

        return $subjectId;
    }
}