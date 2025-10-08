<?php

namespace App\Http\Controllers\Admin\Trainers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
}
