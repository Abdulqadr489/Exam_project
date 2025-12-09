<?php

namespace App\Http\Controllers\Exams;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BaseListRequests\BaseListRequest;
use App\Http\Requests\ExamTypes\CreateExamTypeRequest;
use App\Http\Requests\ExamTypes\UpdateExamTypeRequest;
use App\Models\Exams\ExamType;
use App\Services\ListServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamTypeController extends Controller
{
    public function index(BaseListRequest $request)
    {
        $sortMap = [
            'name' => 'name',
            'code' => 'code',
        ];
        try {
            $params = $request->listParams();

            $list = (new ListServices(ExamType::query()))
                ->withRelations([])
                ->searchable(['name'])
                ->fields([
                    'name',
                    'code',
                    'scoring_strategy',
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



    public function store(CreateExamTypeRequest $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $data = ExamType::create($validated);

                return ApiResponseHelper::success('ExamType created successfully', $data);
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

    public function show(ExamType $examType)
    {
        try {
            $data = $examType;
            return ApiResponseHelper::success('ExamType Retrieved successfully', $data);

        }catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),
                400
            );
        }
    }

    public function update(UpdateExamTypeRequest $request, ExamType $examType)
    {
        try {
            $result = DB::transaction(function () use ($request, $examType) {
                $validated = $request->validated();
                $examType->update($validated);

            });
            return ApiResponseHelper::success('ExamType updated successfully', $examType);

        }catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage(),

            );
        }
    }

    public function destroy(ExamType $examType)
    {
        try {
            DB::transaction(function () use ($examType) {
                $examType->delete();

            });
            return ApiResponseHelper::success('ExamType deleted successfully');

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
                $examType = ExamType::withTrashed()->findOrFail($id);

                $examType->restore();

             return ApiResponseHelper::success('ExamType restored successfully', $examType);
            });
            return ApiResponseHelper::success('ExamType restored successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error(
                "Error Occurred",
                "Unable to process request: " . $e->getMessage()
            );
        }
    }

}
