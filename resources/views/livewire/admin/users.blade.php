<div class="mx-auto w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="mb-2">User Management</flux:heading>
            <flux:subheading>Manage system users and permissions</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="openCreateModal">
            Add User
        </flux:button>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by name or email..."
            icon="magnifying-glass" />
    </div>

    <!-- Users Table -->
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Created</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>
                        <div class="font-semibold">{{ $user->name }}</div>
                        @if ($user->email === auth()->user()->email)
                            <flux:badge size="sm" color="blue">You</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $user->email }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <time datetime="{{ $user->created_at->toISOString() }}" class="text-sm">
                            {{ $user->created_at->format('M d, Y') }}
                        </time>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="openEditModal({{ $user->id }})" variant="ghost" size="sm"
                                icon="pencil" icon:variant="outline" />
                            @if ($user->email !== auth()->user()->email)
                                <flux:button wire:click="confirmDelete({{ $user->id }})" variant="ghost" size="sm"
                                    icon="trash" icon:variant="outline" />
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4" class="text-center">
                        <div class="py-12">
                            <flux:icon.inbox class="mx-auto mb-4 text-zinc-400" variant="outline" />
                            <flux:heading size="lg" class="mb-2">No users found</flux:heading>
                            <flux:subheading class="mb-4">
                                @if ($search)
                                    Try adjusting your search criteria
                                @else
                                    No users in the system yet
                                @endif
                            </flux:subheading>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($users->hasPages())
        <div class="border-t border-zinc-200 py-4 dark:border-zinc-700">
            {{ $users->links() }}
        </div>
    @endif

    <!-- Create User Modal -->
    <flux:modal name="create-user" class="min-w-[28rem]" wire:model="createModalOpen">
        <form wire:submit="createUser" class="space-y-6">
            <div>
                <flux:heading size="lg">Create New User</flux:heading>
                <flux:subheading>Add a new user to the system</flux:subheading>
            </div>

            <flux:separator />

            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="name" placeholder="Enter user name" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input type="email" wire:model="email" placeholder="user@example.com" />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>Password</flux:label>
                <flux:input type="password" wire:model="password" placeholder="Minimum 8 characters" />
                <flux:error name="password" />
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" type="button" wire:click="$set('createModalOpen', false)">
                    Cancel
                </flux:button>
                <flux:button variant="primary" type="submit">Create User</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit User Modal -->
    <flux:modal name="edit-user" class="min-w-[28rem]" wire:model="editModalOpen">
        <form wire:submit="updateUser" class="space-y-6">
            <div>
                <flux:heading size="lg">Edit User</flux:heading>
                <flux:subheading>Update user information</flux:subheading>
            </div>

            <flux:separator />

            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="name" placeholder="Enter user name" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input type="email" wire:model="email" placeholder="user@example.com" />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>Password</flux:label>
                <flux:input type="password" wire:model="password" placeholder="Leave blank to keep current password" />
                <flux:error name="password" />
                <flux:description>Leave blank to keep the current password</flux:description>
            </flux:field>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" type="button" wire:click="$set('editModalOpen', false)">
                    Cancel
                </flux:button>
                <flux:button variant="primary" type="submit">Update User</flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-user" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete user?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this user.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button variant="danger" wire:click="deleteUser">Delete user</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
