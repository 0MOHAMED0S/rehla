<?php

namespace App\Http\Controllers\Admin\BedaetElrehla;

use App\Http\Controllers\Controller;
use App\Http\Requests\Packages\StorePackageRequest;
use App\Http\Requests\Packages\UpdatePackageRequest;
use App\Models\Package;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $packages = Package::paginate(10);

            return response()->json([
                'status' => true,
                'data'   => $packages->items(),
                'meta'   => [
                    'current_page' => $packages->currentPage(),
                    'last_page'    => $packages->lastPage(),
                    'per_page'     => $packages->perPage(),
                    'total'        => $packages->total(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في جلب البيانات',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePackageRequest $request): JsonResponse
    {
        try {
            $package = Package::create($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'تم إضافة الباقة بنجاح',
                'data' => $package,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'فشل في إضافة الباقة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $package = Package::find($id);

            if (!$package) {
                return response()->json([
                    'status'  => false,
                    'message' => 'الباقة غير موجودة',
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'data'    => $package,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في جلب الباقة',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackageRequest $request, $id): JsonResponse
    {
        try {
            $package = Package::find($id);

            if (!$package) {
                return response()->json([
                    'status'  => false,
                    'message' => 'الباقة غير موجودة',
                ], 404);
            }

            $package->update($request->validated());

            return response()->json([
                'status'  => true,
                'message' => 'تم تحديث الباقة بنجاح',
                'data'    => $package,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'فشل في تحديث الباقة',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        //
    }
}
