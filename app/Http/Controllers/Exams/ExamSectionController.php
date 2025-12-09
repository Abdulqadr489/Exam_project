<?php

namespace App\Http\Controllers\Exams;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BaseListRequests\BaseListRequest;
use App\Http\Requests\ExamsSection\CreateExamSectionRequest;
use App\Http\Requests\ExamsSection\UpdateExamSectionRequest;
use App\Models\Exams\ExamSection;
use App\Services\ListServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamSectionController extends Controller
{
    public function index(BaseListRequest $request)
    {
        $sortMap = [
            'exam' => 'exam.name',
        ];
        try {
            $params = $request->listParams();

            $list = (new ListServices(ExamSection::query()))
                ->withRelations(['exam'=>['title']])
                ->searchable(['name'])
                ->fields([
                    'exam_title'=>'exam.title',
                    'name',
                    'order',
                    'instructions',
                ])
                ->search($params['search'])
                ->sortMap($sortMap)
                ->sort($params['sort_by'], $params['sort_dir'])
                ->paginate($params['per_page'])
                ->toApiResponse();

            return $list;
        } catch (\Exception $e) {
            return ApiResponseHelper::error("Error Occurred", "Unable to process request: " . $e->getMessage(), 400);
        }
    }



    public function store(CreateExamSectionRequest $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $data = ExamSection::create($validated);

                return ApiResponseHelper::success('Exam Section created successfully', $data);
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

    public function show(ExamSection $examSection)
    {
        try {
            return ApiResponseHelper::success('Exam Section Retrieved successfully', $examSection->load('exam'));

        }catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),
                400
            );
        }
    }

    public function update(UpdateExamSectionRequest $request, ExamSection $examSection)
    {
        try {
            $result = DB::transaction(function () use ($request, $examSection) {
                $validated = $request->validated();
                $examSection->update($validated);

            });
            return ApiResponseHelper::success('Exam Section updated successfully', $examSection);

        }catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),

            );
        }
    }

    public function destroy(ExamSection $examSection)
    {
        try {
            DB::transaction(function () use ($examSection) {
                $examSection->delete();

            });
            return ApiResponseHelper::success('Exam Section deleted successfully');

        }catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),
            );
        }
    }

    public function restore($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $examType = ExamSection::withTrashed()->findOrFail($id);

                $examType->restore();

                return ApiResponseHelper::success('Exam Section restored successfully', $examType);
            });
            return ApiResponseHelper::success('Exam restored successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage()
            );
        }
    }
}
