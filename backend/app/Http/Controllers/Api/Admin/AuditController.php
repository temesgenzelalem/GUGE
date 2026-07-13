<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Audit\Contracts\AuditServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogCollection;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function __construct(protected AuditServiceInterface $auditService)
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', AuditLog::class);

        $perPage = $request->integer('per_page', 20);
        $filters = $request->only(['actor_id', 'action', 'auditable_type', 'auditable_id']);

        $audits = $this->auditService->listAuditLogs($filters, $perPage);

        return ApiResponse::success(new AuditLogCollection($audits), 'Audit logs retrieved successfully', [
            'current_page' => $audits->currentPage(),
            'per_page' => $audits->perPage(),
            'total' => $audits->total(),
            'last_page' => $audits->lastPage(),
        ], [
            'next' => $audits->nextPageUrl(),
            'prev' => $audits->previousPageUrl(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $this->authorize('viewAny', AuditLog::class);

        $audit = $this->auditService->getAuditLog($id);

        if (! $audit) {
            return ApiResponse::error('Audit log not found.', 404);
        }

        $this->authorize('view', $audit);

        return ApiResponse::success(new AuditLogResource($audit), 'Audit log retrieved successfully');
    }
}
