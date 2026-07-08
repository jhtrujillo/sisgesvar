<?php

namespace App\Http\Controllers;

use App\Models\LabInventoryAlertEmail;
use Illuminate\Http\Request;

class LabInventoryAlertEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return response()->json(LabInventoryAlertEmail::orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:lab_inventory_alert_emails,email',
            'activo' => 'boolean'
        ]);

        $email = LabInventoryAlertEmail::create($validated);
        return response()->json($email, 201);
    }

    public function update(Request $request, $id)
    {
        $email = LabInventoryAlertEmail::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:lab_inventory_alert_emails,email,'.$id,
            'activo' => 'boolean'
        ]);

        $email->update($validated);
        return response()->json($email);
    }

    public function destroy($id)
    {
        $email = LabInventoryAlertEmail::findOrFail($id);
        $email->delete();
        return response()->json(['message' => 'Eliminado exitosamente']);
    }
}
