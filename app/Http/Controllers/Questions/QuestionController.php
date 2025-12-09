<?php

namespace App\Http\Controllers\Questions;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BaseListRequests\BaseListRequest;
use App\Http\Requests\Questions\CreateQuestionRequest;
use App\Http\Requests\Questions\UpdateQuestionRequest;
use App\Models\Exams\ExamSection;
use App\Models\Questions\Question;
use App\Services\ListServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    // GET /exam-sections/{exam_section}/questions
    public function index(ExamSection $examSection, BaseListRequest $request)
    {
        $sortMap = [
            'section' => 'section.name',
        ];

        try {
            $params = $request->listParams();

            $query = Question::query()
                ->where('exam_section_id', $examSection->id);

            $list = (new ListServices($query))
                ->withRelations(['section' => ['name']])
                ->searchable(['question_text'])
                ->fields([
                    'question_type',
                    'question_text',
                    'max_score',
                    'media_url',
                ])
                ->search($params['search'])
                ->sortMap($sortMap)
                ->sort($params['sort_by'], $params['sort_dir'])
                ->paginate($params['per_page'])
                ->toApiResponse();

            return $list;
        } catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),
                400
            );
        }
    }

    // POST /exam-sections/{exam_section}/questions
    public function store(ExamSection $examSection, CreateQuestionRequest $request)
    {
        try {
            $result = DB::transaction(function () use ($request, $examSection) {
                $validated = $request->validated();

                // Always bind to the section from URL (Option A)
                $validated['exam_section_id'] = $examSection->id;

                // meta is already array because of prepareForValidation()
                // Handle audio upload (optional)
                if ($request->hasFile('media')) {
                    $path = $request->file('media')->store('questions', 'public');
                    $validated['media_url'] = $path; // ex: questions/abc123.mp3
                }

                if (!isset($validated['media_url'])) {
                    $validated['media_url'] = null;
                }

                $data = Question::create($validated);

                return ApiResponseHelper::success('Question created successfully', $data);
            });

            return $result;

        } catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),
                400
            );
        }
    }

    // GET /exam-sections/{exam_section}/questions/{question}
    public function show(ExamSection $examSection, Question $question)
    {
        try {
            if ($question->exam_section_id !== $examSection->id) {
                return ApiResponseHelper::error(
                    "Not Found",
                    "Question does not belong to this section",
                    404
                );
            }

            return ApiResponseHelper::success(
                'Question retrieved successfully',
                $question->load('section')
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),
                400
            );
        }
    }

    // PUT/PATCH /exam-sections/{exam_section}/questions/{question}
    public function update(ExamSection $examSection, UpdateQuestionRequest $request, Question $question)
    {
        try {
            $result = DB::transaction(function () use ($request, $question, $examSection) {
                if ($question->exam_section_id !== $examSection->id) {
                    throw new \Exception('Question does not belong to this section');
                }

                $validated = $request->validated();

                // Do not allow changing section from here
                unset($validated['exam_section_id']);

                // If meta came as JSON string, it's already converted in prepareForValidation()

                // Handle new audio upload
                if ($request->hasFile('media')) {
                    // delete old file if exists
                    if (!empty($question->media_url) && Storage::disk('public')->exists($question->media_url)) {
                        Storage::disk('public')->delete($question->media_url);
                    }

                    $path = $request->file('media')->store('questions', 'public');
                    $validated['media_url'] = $path;
                }

                // Optional: allow removing media with a flag
                if ($request->boolean('remove_media') === true) {
                    if (!empty($question->media_url) && Storage::disk('public')->exists($question->media_url)) {
                        Storage::disk('public')->delete($question->media_url);
                    }
                    $validated['media_url'] = null;
                }

                $question->update($validated);
            });

            return ApiResponseHelper::success('Question updated successfully', $question->fresh());

        } catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),
            );
        }
    }

    // DELETE /exam-sections/{exam_section}/questions/{question}
    public function destroy(ExamSection $examSection, Question $question)
    {
        try {
            DB::transaction(function () use ($examSection, $question) {
                if ($question->exam_section_id !== $examSection->id) {
                    throw new \Exception('Question does not belong to this section');
                }

                $question->delete();
            });

            return ApiResponseHelper::success('Question deleted successfully');

        } catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),
            );
        }
    }

    // POST /exam-sections/{exam_section}/questions/restore/{id}
    public function restore(ExamSection $examSection, $id)
    {
        try {
            DB::transaction(function () use ($examSection, $id) {
                $question = Question::withTrashed()
                    ->where('exam_section_id', $examSection->id)
                    ->findOrFail($id);

                $question->restore();
            });

            return ApiResponseHelper::success('Question restored successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage()
            );
        }
    }
}
