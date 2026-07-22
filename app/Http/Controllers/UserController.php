<?php

namespace App\Http\Controllers;

use App\Actions\User\CreateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::latest()->paginate(10);
        $totalUsers = User::count();
        $githubUsersCount = User::whereNotNull('github_id')->orWhere('auth_provider', 'github')->count();
        $emailUsersCount = User::where('auth_provider', 'email')->orWhereNull('auth_provider')->count();

        return view('users.index', compact('users', 'totalUsers', 'githubUsersCount', 'emailUsersCount'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $action->execute($request->validated());

        return redirect()->route('users.index')->with('status', 'Usuário cadastrado com sucesso!');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        Gate::authorize('update', $user);

        $action->execute($user, $request->validated());

        return redirect()->route('users.index')->with('status', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user, DeleteUserAction $action): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $action->execute($user, auth()->user());

        return redirect()->route('users.index')->with('status', 'Usuário excluído com sucesso!');
    }
}
