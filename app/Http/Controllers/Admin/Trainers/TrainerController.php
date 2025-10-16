<?php

namespace App\Http\Controllers\Admin\Trainers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrainerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|min:6|confirmed',
            'specialization'  => 'required|string|max:255',
            'slug'            => 'required|string|unique:trainer_profiles,slug',
            'bio'             => 'required|string',
            'image'           => 'required|image|max:2048',
            'price'           => 'required|numeric|min:0',

        ]);

        // Find instructor role id
        $instructorRole = Role::where('name', 'instructor')->firstOrFail();

        // Create instructor user
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role_id'       => $instructorRole->id,
            'subscribed_at' => now(),
            'expired_at'    => now()->addYear(),
        ]);

        // Upload image
        $imagePath = $request->file('image')->store('trainers', 'public');

        // Create trainer profile
        TrainerProfile::create([
            'user_id'        => $user->id,
            'specialization' => $request->specialization,
            'slug'           => Str::slug($request->slug),
            'bio'            => $request->bio,
            'image'          => $imagePath,
            'price'            => $request->price,
        ]);

        return response()->json([
            'message' => 'Trainer created successfully',
            'trainer' => $user->load('trainerProfile', 'role'),
        ]);
    }
    public function index()
    {
        $instructors = User::with('trainerProfile')
            ->whereHas('role', function ($query) {
                $query->where('name', 'instructor');
            })
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'All instructors retrieved successfully',
            'data'    => $instructors,
        ]);
    }
    public function update(Request $request, $id)
    {
        $user = User::with('trainerProfile')->findOrFail($id);

        if (!$user->trainerProfile) {
            return response()->json([
                'status' => false,
                'message' => 'Trainer profile not found for this user.'
            ], 404);
        }

        $trainerProfile = $user->trainerProfile;

        $request->validate([
            'name'           => 'sometimes|string|max:255',
            'email'          => 'sometimes|email|unique:users,email,' . $user->id,
            'specialization' => 'sometimes|string|max:255',
            'slug'           => 'sometimes|string|unique:trainer_profiles,slug,' . $trainerProfile->id,
            'bio'            => 'sometimes|string',
            'image'          => 'nullable|image|max:2048',
            'price'          => 'sometimes|numeric|min:0',
        ]);

        $user->update([
            'name'  => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
        ]);

        if ($request->hasFile('image')) {
            if ($trainerProfile->image && Storage::disk('public')->exists($trainerProfile->image)) {
                Storage::disk('public')->delete($trainerProfile->image);
            }

            $trainerProfile->image = $request->file('image')->store('trainers', 'public');
        }

        $trainerProfile->update([
            'specialization' => $request->specialization ?? $trainerProfile->specialization,
            'slug'           => $request->slug ? Str::slug($request->slug) : $trainerProfile->slug,
            'bio'            => $request->bio ?? $trainerProfile->bio,
            'price'          => $request->price ?? $trainerProfile->price,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'تم تحديث بيانات المدرب بنجاح',
            'data'    => $user->load('trainerProfile'),
        ]);
    }






    public function index2()
    {
        $trainers = User::whereHas('trainerProfile')
            ->with('trainerProfile')
            ->get()
            ->map(function ($trainer) {
                return [
                    'id' => $trainer->id,
                    'name' => $trainer->name,
                    'specialization' => $trainer->trainerProfile->specialization ?? '',
                    'slug' => $trainer->trainerProfile->slug ?? '',
                    'bio' => $trainer->trainerProfile->bio ?? '',
                    'price' => $trainer->trainerProfile->price ?? '',
                    'image' => $trainer->trainerProfile->image
                        ? asset('storage/' . $trainer->trainerProfile->image)
                        : null,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $trainers,
        ]);
    }

    public function show($id)
    {
        $trainer = User::with('trainerProfile')->find($id);

        if (!$trainer || !$trainer->trainerProfile) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على المدرب',
            ], 404);
        }

        $profile = $trainer->trainerProfile;

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $trainer->id,
                'name' => $trainer->name,
                'specialization' => $profile->specialization ?? '',
                'slug' => $profile->slug ?? '',
                'bio' => $profile->bio ?? '',
                'price' => $profile->price ?? '',
                'image' => $profile->image
                    ? asset('storage/' . $profile->image)
                    : null,
            ],
        ]);
    }
}
