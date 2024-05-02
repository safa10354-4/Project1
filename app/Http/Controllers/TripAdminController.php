<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityTrip;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TripAdminController extends Controller
{


    public function addTripWithActivities(Request $request)
    {
        // تحقق من صحة البيانات المرسلة
        $validatedData = $request->validate([
            'flight_name' => 'required|string',
            'location' => 'required|string',
            'trip_start_date' => 'required|date',
            'trip_end_date' => 'required|date',
            'trip_capacity' => 'required|integer',
            'activities' => 'required|array|min:1', // يجب أن يكون هناك على الأقل نشاط واحد
            'activities.*.name' => 'required|string',
            'activities.*.price' => 'required|numeric',
            'activities.*.photo' => 'nullable',
            'activities.*.activity_start_time' => 'required|date',
            'activities.*.activity_end_time' => 'required|date',
            'activities.*.location' => 'required|string',
            'activities.*.option' => 'required|boolean',
        ]);

        // إنشاء الرحلة
        $flight = Trip::query()->create([
            'admin_id' => Auth()->user()->id,
            'flight_name' => $validatedData['flight_name'],
            'location' => $validatedData['location'],
            'trip_start_date' => $validatedData['trip_start_date'],
            'trip_end_date' => $validatedData['trip_end_date'],
            'trip_capacity' => $validatedData['trip_capacity'],
        ]);



        foreach ($validatedData['activities'] as $activityData) {
            // ابحث عن النشاط بالاسم أو أنشئه إذا لم يكن موجودًا
            $activity = Activity::query()->firstOrCreate(['name_activity' => $activityData['name']]);

            // إنشاء الواصفات للنشاط
            $activityTrip = ActivityTrip::query()->create([
                'trip_id' => $flight['id'],
                'activity_id' => $activity['id'],
                'price' => $activityData['price'],
                //'photo' => $activityData['photo'],
                'activity_start_time' => $activityData['activity_start_time'],
                'activity_end_time' => $activityData['activity_end_time'],
                'location' => $activityData['location'],
                'option' => $activityData['option'],
            ]);

            // التحقق من وجود الصورة وتحميلها
            if (isset($activityData['photo'])) {
                $image = $activityData['photo']; // الوصول إلى الصورة المرفقة مباشرة
                $imageName = $image->hashName();

                Storage::disk("public")->put($imageName, file_get_contents($image));

                // تحديث الوصفة للنشاط برابط الصورة
                $activityTrip->update(['photo' => $imageName]);



            }
        }

        return response()->json(['message' => 'The flight has been added successfully'], 200);


    }



    //==========================================================================================




    public function getAllTripsWithActivities()
    {
        // الحصول على جميع الرحلات مع النشاطات المرتبطة
        $trips = Trip::with('activities')->get();

        // إرجاع البيانات كمصفوفة JSON
        return response()->json($trips, 200);
    }


    //=====================================================================================================






    public function updateTripWithActivities(Request $request, $tripId)
    {
        // تحقق من صحة البيانات المرسلة
        $validatedData = $request->validate([
            'flight_name' => 'nullable|string',
            'location' => 'nullable|string',
            'trip_start_date' => 'nullable|date',
            'trip_end_date' => 'nullable|date',
            'trip_capacity' => 'nullable|integer',
            'activities' => 'nullable|array|min:1',
            'activities.*.id' => 'required|integer',
            'activities.*.name' => 'nullable|string',
            'activities.*.price' => 'nullable|numeric',
            'activities.*.activity_start_time' => 'nullable|date',
            'activities.*.activity_end_time' => 'nullable|date',
            'activities.*.location' => 'nullable|string',
            'activities.*.option' => 'nullable|boolean',
        ]);

        // ابحث عن الرحلة
        $trip = Trip::query()->findOrFail($tripId);

        // تحديث بيانات الرحلة إذا تم تقديمها
        if (!empty($validatedData)) {
            $trip->update(array_filter($validatedData));
        }

        // تحديث أو إضافة النشاطات المرتبطة بالرحلة
        foreach ($validatedData['activities'] as $activityData) {
            $activityTrip = ActivityTrip::query()->findOrFail($activityData['id']); // ابحث عن النشاط المراد تحديثه

            // تحديث بيانات النشاط إذا تم تقديمها
            if (!empty($activityData)) {
                $activityTrip->update(array_filter($activityData));
            }
        }

        // إرسال رسالة نجاح إلى المستخدم
        return response()->json(['message' => 'The flight and its activities have been updated successfully'], 200);
    }



//========================================================================================================










}






















