<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of users with pagination, search, and roles.
     * This method is used only by admin users.
     */
    public function index(Request $request): \Inertia\Response
    {
        $perPage = 10;
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $query = User::select('id', 'name', 'email', 'email_verified_at', 'created_at')
            ->with('roles:id,name');

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%')
                  ->orWhereHas('roles', function ($roleQuery) use ($search) {
                      $roleQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Apply sorting
        if ($sortColumn === 'roles') {
            // Special handling for role sorting
            $query->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                  ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                  ->orderBy('roles.name', $sortDirection)
                  ->select('users.*')
                  ->distinct();
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $paginatedUsers = $query->paginate($perPage, ['*'], 'page', $page);

        // For initial page load, also send all users for client-side pagination fallback
        $responseData = [
            'users' => collect($paginatedUsers->items())->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'primary_role' => $user->roles->first()?->name ?? 'No Role',
                ];
            }),
            'pagination' => [
                'current_page' => $paginatedUsers->currentPage(),
                'last_page' => $paginatedUsers->lastPage(),
                'total' => $paginatedUsers->total(),
                'per_page' => $paginatedUsers->perPage(),
                'from' => $paginatedUsers->firstItem(),
                'to' => $paginatedUsers->lastItem(),
            ],
            'filters' => [
                'search' => $search,
                'sort' => $sortColumn,
                'direction' => $sortDirection,
            ]
        ];

        // On initial load (no AJAX request), also provide allUsers for fallback
        if (!$request->header('X-Inertia')) {
            $allUsers = User::select('id', 'name', 'email', 'email_verified_at', 'created_at')
                ->with('roles:id,name')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'created_at' => $user->created_at,
                        'roles' => $user->roles->pluck('name')->toArray(),
                        'primary_role' => $user->roles->first()?->name ?? 'No Role',
                    ];
                });
            
            $responseData['allUsers'] = $allUsers->toArray();
        }

        return Inertia::render('Users/Index', $responseData);
    }

    /**
     * Toggle user status (activate/deactivate).
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // For now, we'll use email_verified_at as a way to "activate/deactivate" users
            $user->email_verified_at = $user->email_verified_at ? null : now();
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => $user->email_verified_at ? 'User activated successfully' : 'User deactivated successfully',
                'data' => [
                    'user_id' => $user->id,
                    'is_active' => $user->email_verified_at !== null,
                ]
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle user status',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get available roles for assignment.
     */
    public function getRoles(): JsonResponse
    {
        try {
            $roles = \Spatie\Permission\Models\Role::select('id', 'name')->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $roles
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve roles',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}