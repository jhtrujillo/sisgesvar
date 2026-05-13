<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Catalogo;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use Inertia\Inertia;

class UserController extends Controller
{
    private function authorizeJefe()
    {
        abort_unless(auth()->user() && auth()->user()->role === 'JEFE', 403, 'No autorizado.');
    }

    public function index()
    {
        $this->authorizeJefe();

        $users = User::orderBy('name')->get();
        
        // Fetch Ambientes to feed user dropdown
        $ambientes = Catalogo::where('categoria', 'AMBIENTE')
            ->pluck('valor')
            ->unique()
            ->sort()
            ->values()
            ->all();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'ambientes' => $ambientes
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            // Standardize: empty array passed as null for database cleanliness
            'ambiente' => (!empty($request->ambiente)) ? array_values(array_filter($request->ambiente)) : null
        ];

        if (!empty($request->password)) {
            $data['password'] = \Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('message', 'Usuario actualizado con éxito.');
    }

    public function destroy(User $user)
    {
        $this->authorizeJefe();
        
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes borrar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->back()->with('message', 'Usuario eliminado del sistema.');
    }
}
