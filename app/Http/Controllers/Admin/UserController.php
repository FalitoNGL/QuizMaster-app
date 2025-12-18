<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount('quizAttempts')
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user with full history
     */
    public function show(User $user)
    {
        $user->load(['quizAttempts.quiz', 'achievements']);
        
        // Get attempts with pagination
        $attempts = $user->quizAttempts()
            ->with('quiz')
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->paginate(10);

        $stats = [
            'total_attempts' => $user->quizAttempts()->count(),
            'completed_attempts' => $user->quizAttempts()->where('status', 'completed')->count(),
            'total_score' => $user->quizAttempts()->where('status', 'completed')->sum('total_score'),
            'avg_score' => round($user->quizAttempts()->where('status', 'completed')->avg('total_score') ?? 0, 1),
            'total_violations' => $user->quizAttempts()->sum('violations'),
            'total_achievements' => $user->achievements()->count(),
        ];

        return view('admin.users.show', compact('user', 'stats', 'attempts'));
    }

    /**
     * Ban/Unban user (toggle role to banned or user)
     */
    public function toggleBan(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat memblokir admin!');
        }

        // We'll use a simple approach - just soft ban by setting a banned_at field
        // For now, let's just track it differently
        $user->role = $user->role === 'banned' ? 'user' : 'banned';
        $user->save();

        $message = $user->role === 'banned' 
            ? 'User berhasil diblokir!' 
            : 'User berhasil diaktifkan kembali!';

        return back()->with('success', $message);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created admin user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus admin!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}
