<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Phase 1: Scan JSON (File or Body) and detect conflicts without saving.
     */
    public function scan(Request $request, Supplier $supplier): JsonResponse
    {
        $data = null;

        // Choice 1: From File Upload
        if ($request->hasFile('file')) {
            $jsonContent = file_get_contents($request->file('file')->getRealPath());
            $data = json_decode($jsonContent, true);
        } 
        // Choice 2: From Raw JSON Body
        else if ($request->isJson() || $request->all()) {
            $data = $request->all();
            // Remove 'file' key if it accidentally got in from form-data
            unset($data['file']); 
        }

        if (!$data || !is_array($data)) {
            return $this->errorResponse('No valid JSON data provided. Please upload a file or send a JSON body.', 422);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->errorResponse('Invalid JSON format', 422);
        }

        try {
            $report = $this->importService->scan($supplier->id, $data);
            
            // AUTO-CONFIRM IF NO CONFLCTS
            if (!$report['summary']['has_conflicts']) {
                $summary = $this->importService->execute($supplier->id, $report['import_token'], 'skip'); // Strategy doesn't matter much if no conflicts
                
                return $this->successResponse(
                    [
                        'auto_confirmed' => true,
                        'execution_summary' => $summary
                    ],
                    "Clean data detected. Import executed automatically."
                );
            }

            return $this->successResponse(
                $report,
                "Conflicts detected. Review before confirming."
            );
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Phase 2: Execute the import after user review.
     */
    public function confirm(Request $request, Supplier $supplier): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'import_token' => 'required|string',
            'strategy' => 'required|string|in:overwrite,skip',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        try {
            $summary = $this->importService->execute(
                $supplier->id, 
                $request->input('import_token'), 
                $request->input('strategy')
            );
            
            return $this->successResponse(
                $summary,
                "Import executed successfully using '{$request->input('strategy')}' strategy"
            );
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
