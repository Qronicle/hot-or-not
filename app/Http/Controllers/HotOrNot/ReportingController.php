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
    public function index(SubjectService $subjectService)
    {
        $subjects = $subjectService->getMostPopularSubjects();

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