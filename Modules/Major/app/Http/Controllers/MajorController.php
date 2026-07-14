<?php

namespace Modules\Major\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Major\Models\Major;

class MajorController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'province_id' => 'nullable|exists:provinces,id',
            'city_id' => 'nullable|exists:cities,id',
            'email' => 'nullable|email|max:255',
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|in:thermal,frozen',
            'weight' => 'required|integer|min:10|max:1000000', // حداقل 1 کیلوگرم
        ]);

        // بررسی اینکه حداقل یکی از mobile یا email پر شده باشد
        $validator->after(function ($validator) use ($request) {
            if (empty($request->mobile) && empty($request->email)) {
                $validator->errors()->add('mobile', 'حداقل یکی از فیلدهای موبایل یا ایمیل باید پر شود.');
                $validator->errors()->add('email', 'حداقل یکی از فیلدهای موبایل یا ایمیل باید پر شود.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // ایجاد رکورد جدید با وضعیت پیش‌فرض pending
        $major = Major::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'email' => $request->email,
            'product_name' => $request->product_name,
            'product_type' => $request->product_type,
            'weight' => $request->weight,
            'status' => 'pending', // وضعیت پیش‌فرض
            'last_call_summary' => null, // از فرانت نمیاد
        ]);

        return response()->json([
            'success' => true,
            'message' => 'درخواست با موفقیت ثبت شد',
            'data' => $major
        ], 201);
    }

    /**
     * دریافت لیست تمام درخواست‌ها برای پنل ادمین
     */
    public function index(Request $request)
    {
        $query = Major::query();

        // فیلتر بر اساس وضعیت
        if ($request->has('status') && in_array($request->status, ['pending', 'answered', 'not_answered'])) {
            $query->where('status', $request->status);
        }

        // فیلتر بر اساس نوع محصول
        if ($request->has('product_type') && in_array($request->product_type, ['thermal', 'frozen'])) {
            $query->where('product_type', $request->product_type);
        }

        // جستجو
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * نمایش یک درخواست خاص
     */
    public function show($id)
    {
        $major = Major::find($id);

        if (!$major) {
            return response()->json([
                'success' => false,
                'message' => 'درخواست یافت نشد'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $major
        ]);
    }

    /**
     * تغییر وضعیت درخواست
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,answered,not_answered'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $major = Major::find($id);

        if (!$major) {
            return response()->json([
                'success' => false,
                'message' => 'درخواست یافت نشد'
            ], 404);
        }

        $major->status = $request->status;
        $major->save();

        return response()->json([
            'success' => true,
            'message' => 'وضعیت با موفقیت تغییر یافت',
            'data' => $major
        ]);
    }

    /**
     * ثبت خلاصه تماس
     */
    public function updateLastCallSummary(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'last_call_summary' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $major = Major::find($id);

        if (!$major) {
            return response()->json([
                'success' => false,
                'message' => 'درخواست یافت نشد'
            ], 404);
        }

        $major->last_call_summary = $request->last_call_summary;
        $major->save();

        return response()->json([
            'success' => true,
            'message' => 'خلاصه تماس با موفقیت ثبت شد',
            'data' => $major
        ]);
    }

    /**
     * حذف درخواست (اختیاری)
     */
    public function destroy($id)
    {
        $major = Major::find($id);

        if (!$major) {
            return response()->json([
                'success' => false,
                'message' => 'درخواست یافت نشد'
            ], 404);
        }

        $major->delete();

        return response()->json([
            'success' => true,
            'message' => 'درخواست با موفقیت حذف شد'
        ]);
    }

    /**
     * آمار کلی درخواست‌ها
     */
    public function statistics()
    {
        $statistics = [
            'total' => Major::count(),
            'pending' => Major::where('status', 'pending')->count(),
            'answered' => Major::where('status', 'answered')->count(),
            'not_answered' => Major::where('status', 'not_answered')->count(),
            'thermal' => Major::where('product_type', 'thermal')->count(),
            'frozen' => Major::where('product_type', 'frozen')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }
}
