<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;

class ExamController extends Controller
{
    public function take($curso_id, $examen_id)
    {
        $exam = Exam::find($examen_id);
        if (!$exam) { Session::setFlash('error', 'Examen no encontrado'); $this->redirect('curso/' . $curso_id); }

        $enrollment = \App\Core\Database::fetchOne(
            "SELECT * FROM matriculas WHERE user_id = ? AND curso_id = ? AND status = 'approved'",
            [Session::userId(), $curso_id]
        );
        if (!$enrollment) { Session::setFlash('error', 'No tienes acceso'); $this->redirect('mis-cursos'); }

        $attemptCount = ExamAttempt::countByUserAndExam(Session::userId(), $examen_id);
        if ($attemptCount >= $exam['max_attempts']) {
            Session::setFlash('error', 'Has agotado tus intentos para este examen');
            $this->redirect('curso/' . $curso_id);
        }

        $preguntas = Question::getAllByExam($examen_id);
        if (empty($preguntas)) {
            Session::setFlash('error', 'Este examen no tiene preguntas');
            $this->redirect('curso/' . $curso_id);
        }

        $curso = Course::findWithCategory($curso_id);

        $this->view('courses.exam', [
            'title' => $exam['title'] . ' - ' . $curso['title'],
            'exam' => $exam,
            'preguntas' => $preguntas,
            'curso' => $curso,
        ]);
    }

    public function submit($curso_id, $examen_id)
    {
        $exam = Exam::find($examen_id);
        if (!$exam) { $this->json(['error' => 'Examen no encontrado']); }

        $attemptCount = ExamAttempt::countByUserAndExam(Session::userId(), $examen_id);
        if ($attemptCount >= $exam['max_attempts']) {
            $this->json(['error' => 'Has agotado tus intentos']);
        }

        $preguntas = Question::getAllByExam($examen_id);
        $answers = $_POST['answers'] ?? [];

        $totalPoints = 0;
        $earnedPoints = 0;
        $attemptId = ExamAttempt::create(['user_id' => Session::userId(), 'examen_id' => $examen_id]);

        foreach ($preguntas as $pq) {
            $userAnswer = $answers[$pq['id']] ?? '';
            $options = json_decode($pq['options'], true);
            $isCorrect = strcasecmp(trim($userAnswer), trim($pq['correct_answer'])) === 0;
            if ($isCorrect) $earnedPoints += $pq['points'];
            $totalPoints += $pq['points'];
            StudentAnswer::save([
                'intento_id' => $attemptId,
                'pregunta_id' => $pq['id'],
                'answer' => $userAnswer,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $pq['points'] : 0,
            ]);
        }

        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
        $passed = $score >= $exam['passing_score'];
        ExamAttempt::complete($attemptId, $score, $passed);

        $this->json([
            'success' => true,
            'redirect' => url('curso/' . $curso_id . '/examen/' . $examen_id . '/resultado/' . $attemptId),
        ]);
    }

    public function result($curso_id, $examen_id, $intento_id)
    {
        $exam = Exam::find($examen_id);
        $attempt = ExamAttempt::find($intento_id);
        if (!$exam || !$attempt) { Session::setFlash('error', 'Resultado no encontrado'); $this->redirect('curso/' . $curso_id); }

        $preguntas = Question::getAllByExam($examen_id);
        $respuestas = StudentAnswer::getAllByAttempt($intento_id);
        $respMap = [];
        foreach ($respuestas as $r) {
            $respMap[$r['pregunta_id']] = $r;
        }

        $curso = Course::findWithCategory($curso_id);

        $this->view('courses.exam_result', [
            'title' => 'Resultado - ' . $curso['title'],
            'exam' => $exam,
            'attempt' => $attempt,
            'preguntas' => $preguntas,
            'respMap' => $respMap,
            'curso' => $curso,
        ]);
    }
}
