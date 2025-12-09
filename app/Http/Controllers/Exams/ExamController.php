<?php

namespace App\Http\Controllers\Exams;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BaseListRequests\BaseListRequest;
use App\Http\Requests\Exams\CreateExamRequest;
use App\Http\Requests\Exams\UpdateExamRequest;
use App\Models\Exams\Exam;
use App\Models\Exams\ExamType;
use App\Services\ListServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index(BaseListRequest $request)
    {
        $sortMap = [
            'type' => 'type.name',
        ];
        try {
            $params = $request->listParams();

            $list = (new ListServices(Exam::query()))
                ->withRelations(['type'=>['name']])
                ->searchable(['name'])
                ->fields([
                    'exam_type'=>'type.name',
                    'title',
                    'duration',
                    'description',
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



    public function store(CreateExamRequest $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $data = Exam::create($validated);

                return ApiResponseHelper::success('Exam created successfully', $data);
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

    public function show(Exam $exam)
    {
        try {
            return ApiResponseHelper::success('Exam Retrieved successfully', $exam->load('type'));

        }catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),
                400
            );
        }
    }

    public function update(UpdateExamRequest $request, Exam $exam)
    {
        try {
            $result = DB::transaction(function () use ($request, $exam) {
                $validated = $request->validated();
                $exam->update($validated);

            });
            return ApiResponseHelper::success('Exam updated successfully', $exam);

        }catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),

            );
        }
    }

    public function destroy(Exam $exam)
    {
        try {
            DB::transaction(function () use ($exam) {
                $exam->delete();

            });
            return ApiResponseHelper::success('Exam deleted successfully');

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
                $examType = Exam::withTrashed()->findOrFail($id);

                $examType->restore();

                return ApiResponseHelper::success('Exam restored successfully', $examType);
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
