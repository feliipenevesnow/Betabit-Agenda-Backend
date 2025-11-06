<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = $request->user()->contacts()->orderBy('name', 'asc')->get();
        return response()->json($contacts);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
            }

            $contact = $request->user()->contacts()->create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'image_path' => $imagePath,
            ]);

            return response()->json($contact, 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function show(Request $request, Contact $contact)
    {
        if ($request->user()->id !== $contact->user_id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        return response()->json($contact);
    }

    public function update(Request $request, Contact $contact)
    {
        if ($request->user()->id !== $contact->user_id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $imagePath = $contact->image_path;

            if ($request->hasFile('image')) {
                if ($contact->image_path) {
                    Storage::disk('public')->delete($contact->image_path);
                }
                $imagePath = $request->file('image')->store('images', 'public');
                $validatedData['image_path'] = $imagePath;
            }

            $contact->update($validatedData);

            return response()->json($contact);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy(Request $request, Contact $contact)
    {
        if ($request->user()->id !== $contact->user_id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        if ($contact->image_path) {
            Storage::disk('public')->delete($contact->image_path);
        }

        $contact->delete();

        return response()->json(['message' => 'Contacto apagado com sucesso'], 200);
    }
}