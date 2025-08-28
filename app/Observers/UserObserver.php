<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        $this->validateEmailUniqueness($user);
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        // Só valida se o email foi alterado
        if ($user->isDirty('email')) {
            $this->validateEmailUniqueness($user);
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }

    /**
     * Validate email uniqueness considering soft deletes
     */
    private function validateEmailUniqueness(User $user): void
    {
        $query = User::where('email', $user->email);
        
        // Se estiver atualizando, exclui o próprio usuário da verificação
        if ($user->exists) {
            $query->where('id', '!=', $user->id);
        }
        
        // Verifica se existe outro usuário ativo com o mesmo email
        $existingUser = $query->first();
        
        if ($existingUser) {
            throw ValidationException::withMessages([
                'email' => ['Este e-mail já está em uso por outro usuário ativo.']
            ]);
        }
    }
}
