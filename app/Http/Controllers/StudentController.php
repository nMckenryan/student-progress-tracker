<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StudentController extends Controller
{
    public function index()
    {
        // load fresh data from JSON
        $json = File::get(public_path('data/studentData.json'));
        $students = json_decode($json, true);

        // Get session data if it exists
        $sessionStudents = session('students', []);

        // Merge session data with JSON data
        foreach ($students as $studentId => &$student) {
            if (isset($sessionStudents[$studentId])) {
                // Update completed status from session
                foreach ($student['course']['units'] as $unitIndex => &$unit) {
                    if (isset($sessionStudents[$studentId]['course']['units'][$unitIndex])) {
                        $unit['completed'] = $sessionStudents[$studentId]['course']['units'][$unitIndex]['completed'];
                    } else {
                        $unit['completed'] = false;
                    }
                }

                // Recalculate progress
                $totalUnits = count($student['course']['units']);
                $completedUnits = count(array_filter(
                    $student['course']['units'],
                    fn($unit) => $unit['completed']
                ));
                $student['course']['progressCompleted'] = round(($completedUnits / $totalUnits) * 100);
            } else {
                // Initialize new student data
                foreach ($student['course']['units'] as &$unit) {
                    $unit['completed'] = false;
                }
                $student['course']['progressCompleted'] = 0;
            }
        }

        // Store merged data back in session
        session(['students' => $students]);

        return view('progressTracker', [
            'students' => $students
        ]);
    }

    public function updateProgress(Request $request)
    {
        $changes = $request->input('changes', []);

        // Get fresh data from session
        $students = session('students', []);

        foreach ($changes as $change) {
            $studentId = $change['studentId'];
            $unitIndex = $change['unitIndex'];
            $completed = $change['completed'];

            // Update the unit status
            if (isset($students[$studentId]['course']['units'][$unitIndex])) {
                $students[$studentId]['course']['units'][$unitIndex]['completed'] = $completed;

                // Recalculate progress
                $totalUnits = count($students[$studentId]['course']['units']);
                $completedUnits = count(array_filter(
                    $students[$studentId]['course']['units'],
                    fn($unit) => $unit['completed']
                ));
                $progress = round(($completedUnits / $totalUnits) * 100);
                $students[$studentId]['course']['progressCompleted'] = $progress;
            }
        }

        // Save back to session and verify
        session(['students' => $students]);
        $saved = session('students');

        $success = $saved === $students;

        return response()->json([
            'success' => $success,
            'progress' => $students[array_key_first($students)]['course']['progressCompleted']
        ]);
    }
}
