<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $createModalOpen = false;

    public bool $editModalOpen = false;

    public ?int $editingUserId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public ?int $userToDelete = null;

    public function mount(): void
    {
        $this->authorize('admin-access');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'email', 'password', 'editingUserId']);
        $this->createModalOpen = true;
    }

    public function createUser(): void
    {
        $this->authorize('admin-access');

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        ActivityLog::log(
            'user_created',
            "Created user: {$user->email}",
            ['user_id' => $user->id, 'email' => $user->email]
        );

        $this->reset(['name', 'email', 'password']);
        $this->createModalOpen = false;

        Flux::toast(
            heading: 'User created',
            text: "User {$user->email} has been created successfully.",
            variant: 'success'
        );
    }

    public function openEditModal(int $userId): void
    {
        $this->authorize('admin-access');

        $user = User::findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';

        $this->editModalOpen = true;
    }

    public function updateUser(): void
    {
        $this->authorize('admin-access');

        $user = User::findOrFail($this->editingUserId);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->editingUserId],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        ActivityLog::log(
            'user_updated',
            "Updated user: {$user->email}",
            ['user_id' => $user->id, 'email' => $user->email]
        );

        $this->reset(['name', 'email', 'password', 'editingUserId']);
        $this->editModalOpen = false;

        Flux::toast(
            heading: 'User updated',
            text: "User {$user->email} has been updated successfully.",
            variant: 'success'
        );
    }

    public function confirmDelete(int $userId): void
    {
        $this->authorize('admin-access');
        $this->userToDelete = $userId;
        $this->modal('delete-user')->show();
    }

    public function deleteUser(): void
    {
        $this->authorize('admin-access');

        if ($this->userToDelete) {
            $user = User::findOrFail($this->userToDelete);

            if ($user->email === auth()->user()->email) {
                Flux::toast(
                    heading: 'Cannot delete',
                    text: 'You cannot delete your own account.',
                    variant: 'danger'
                );

                return;
            }

            $userEmail = $user->email;

            ActivityLog::log(
                'user_deleted',
                "Deleted user: {$userEmail}",
                ['user_id' => $user->id, 'email' => $userEmail]
            );

            $user->delete();

            $this->userToDelete = null;
            $this->modal('delete-user')->close();

            Flux::toast(
                heading: 'User deleted',
                text: "{$userEmail} has been removed successfully.",
                variant: 'success'
            );
        }
    }

    public function cancelDelete(): void
    {
        $this->userToDelete = null;
        $this->modal('delete-user')->close();
    }

    #[Title('User Management')]
    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('email', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.users', [
            'users' => $users,
        ]);
    }
}
