<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityTrip;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TripController extends Controller
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
        $flight = Trip::create([
            'admin_id' => Auth()->user()->id,
            'flight_name' => $validatedData['flight_name'],
            'location' => $validatedData['location'],
            'trip_start_date' => $validatedData['trip_start_date'],
            'trip_end_date' => $validatedData['trip_end_date'],
            'trip_capacity' => $validatedData['trip_capacity'],
        ]);



        foreach ($validatedData['activities'] as $activityData) {
            // ابحث عن النشاط بالاسم أو أنشئه إذا لم يكن موجودًا
            $activity = Activity::firstOrCreate(['name_activity' => $activityData['name']]);

            // إنشاء الواصفات للنشاط
            $activityTrip = ActivityTrip::create([
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


    //=====================================================================================================


}






















