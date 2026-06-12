<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Course;
use App\Models\Module;
use App\Models\ClassModel;
use App\Models\CompletedClass;
use App\Models\Exam;
use App\Models\ExamAttempt;

class CourseViewController extends Controller
{
    public function show($curso_id)
    {
        $curso = Course::findWithCategory($curso_id);
        if (!$curso) { Session::setFlash('error', 'Curso no encontrado'); $this->redirect('mis-cursos'); }

        $enrollment = \App\Core\Database::fetchOne(
            "SELECT * FROM matriculas WHERE user_id = ? AND curso_id = ? AND status = 'approved'",
            [Session::userId(), $curso_id]
        );
        if (!$enrollment) { Session::setFlash('error', 'No tienes acceso a este curso'); $this->redirect('mis-cursos'); }

        $modulos = Module::getAllByCourse($curso_id);
        $completedClassIds = CompletedClass::getCompletedIdsByUserAndCourse(Session::userId(), $curso_id);
        $totalClasses = 0;
        $completedCount = 0;
        $moduleData = [];

        foreach ($modulos as &$mod) {
            $classes = ClassModel::getAllByModule($mod['id']);
            $exam = Exam::findByModule($mod['id']);
            $examPassed = false;
            $examAttempts = 0;
            if ($exam) {
                $examPassed = ExamAttempt::hasPassed(Session::userId(), $exam['id']);
                $examAttempts = ExamAttempt::countByUserAndExam(Session::userId(), $exam['id']);
            }
            $modClasses = [];
            $modCompleted = 0;
            foreach ($classes as $cls) {
                $isCompleted = in_array($cls['id'], $completedClassIds);
                if ($isCompleted) $modCompleted++;
                $totalClasses++;
                $modClasses[] = $cls + ['is_completed' => $isCompleted];
            }
            $completedCount += $modCompleted;
            $moduleData[] = [
                'module' => $mod,
                'classes' => $modClasses,
                'exam' => $exam,
                'exam_passed' => $examPassed,
                'exam_attempts' => $examAttempts,
            ];
        }
        unset($mod);

        $progress = $totalClasses > 0 ? round(($completedCount / $totalClasses) * 100) : 0;

        $this->view('courses.view', [
            'title' => $curso['title'],
            'curso' => $curso,
            'moduleData' => $moduleData,
            'progress' => $progress,
            'completedCount' => $completedCount,
            'totalClasses' => $totalClasses,
        ]);
    }

    public function classView($curso_id, $clase_id)
    {
        $clase = ClassModel::find($clase_id);
        if (!$clase) { Session::setFlash('error', 'Clase no encontrada'); $this->redirect('curso/' . $curso_id); }

        $modulo = Module::find($clase['modulo_id']);
        if (!$modulo || $modulo['curso_id'] != $curso_id) {
            Session::setFlash('error', 'Clase no pertenece a este curso'); $this->redirect('curso/' . $curso_id);
        }

        $enrollment = \App\Core\Database::fetchOne(
            "SELECT * FROM matriculas WHERE user_id = ? AND curso_id = ? AND status = 'approved'",
            [Session::userId(), $curso_id]
        );
        if (!$enrollment) { Session::setFlash('error', 'No tienes acceso'); $this->redirect('mis-cursos'); }

        $prevClass = ClassModel::getPreviousClass($clase['modulo_id'], $clase['orden']);
        if ($prevClass) {
            if (!CompletedClass::isCompleted(Session::userId(), $prevClass['id'])) {
                Session::setFlash('error', 'Debes completar la clase anterior primero');
                $this->redirect('curso/' . $curso_id);
            }
        } else {
            $modulos = Module::getAllByCourse($curso_id);
            $prevModule = null;
            $found = false;
            foreach ($modulos as $m) {
                if ($m['id'] == $clase['modulo_id']) {
                    $found = true; break;
                }
                if (!$found) $prevModule = $m;
            }
            if ($prevModule) {
                $exam = Exam::findByModule($prevModule['id']);
                if ($exam && !ExamAttempt::hasPassed(Session::userId(), $exam['id'])) {
                    Session::setFlash('error', 'Debes aprobar el examen del módulo anterior');
                    $this->redirect('curso/' . $curso_id);
                }
            }
        }

        $allClasses = ClassModel::getAllByModule($clase['modulo_id']);
        $nextClass = null;
        foreach ($allClasses as $idx => $c) {
            if ($c['id'] == $clase_id && isset($allClasses[$idx + 1])) {
                $nextClass = $allClasses[$idx + 1];
                break;
            }
        }

        $isCompleted = CompletedClass::isCompleted(Session::userId(), $clase_id);
        $curso = Course::findWithCategory($curso_id);

        $this->view('courses.class', [
            'title' => $clase['title'] . ' - ' . $curso['title'],
            'clase' => $clase,
            'curso' => $curso,
            'modulo' => $modulo,
            'nextClass' => $nextClass,
            'isCompleted' => $isCompleted,
        ]);
    }

    public function completeClass($curso_id, $clase_id)
    {
        $clase = ClassModel::find($clase_id);
        if (!$clase) { $this->json(['error' => 'Clase no encontrada']); }

        CompletedClass::mark(Session::userId(), $clase_id);

        $allClasses = ClassModel::getAllByModule($clase['modulo_id']);
        $nextClass = null;
        foreach ($allClasses as $idx => $c) {
            if ($c['id'] == $clase_id && isset($allClasses[$idx + 1])) {
                $nextClass = $allClasses[$idx + 1];
                break;
            }
        }
        if ($nextClass) {
            $this->json(['success' => true, 'redirect' => url('curso/' . $curso_id . '/clase/' . $nextClass['id'])]);
        } else {
            $exam = Exam::findByModule($clase['modulo_id']);
            if ($exam) {
                $this->json(['success' => true, 'redirect' => url('curso/' . $curso_id . '/examen/' . $exam['id'])]);
            } else {
                $this->json(['success' => true, 'redirect' => url('curso/' . $curso_id)]);
            }
        }
    }
}
