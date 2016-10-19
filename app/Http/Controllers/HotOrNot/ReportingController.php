<?php
/**
 * ReportingController.php
 */

namespace App\Http\Controllers\HotOrNot;

use App\Http\Controllers\Controller;
use App\Services\SubjectService;

/**
 * ReportingController
 *
 * @since       2016-10-18 21:01
 * @author      ruud.seberechts
 */
class ReportingController extends Controller
{
    /**
     * Reporting page
     *
     * Displays both the most popular subjects and the statistics between the two currently matched users
     *
     * @param SubjectService $subjectService
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(SubjectService $subjectService)
    {
        // Most popular subjects
        $subjects = $subjectService->getMostPopularSubjects();
        // Currently matched subjects (when available)
        $lastMatch = false;
        if ($sessionSubjects = (request()->session()->get('subjects'))) {
            $lastSubjectIds = array_pop($sessionSubjects);
            $lastMatch      = $subjectService->getMatch($lastSubjectIds[0], $lastSubjectIds[1]);
        }

        return view('public.statistics', [
            'subjects'  => $subjects,
            'lastMatch' => $lastMatch,
        ]);
    }
}