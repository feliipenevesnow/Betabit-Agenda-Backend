<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    /**
     * Retorna todos os contatos do usuário autenticado.
     */
    public function index(Request $request)
    {
        $contacts = $request->user()->contacts()
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($contact) {
                $contact->image_url = $contact->image_path 
                    ? asset('storage/' . $contact->image_path)
                    : null;
                return $contact;
            });

        return response()->json($contacts);
    }

    /**
     * Cria um novo contato.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'image' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml|max:4096',
                'is_favorite' => 'nullable|boolean',
                'sort_order' => 'nullable|integer',
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
                'is_favorite' => $validatedData['is_favorite'] ?? false,
                'sort_order' => $validatedData['sort_order'] ?? 0,
            ]);

            $contact->image_url = $imagePath ? asset('storage/' . $imagePath) : null;

            return response()->json($contact, 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Exibe um contato específico.
     */
    public function show(Request $request, Contact $contact)
    {
        if ($request->user()->id !== $contact->user_id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $contact->image_url = $contact->image_path ? asset('storage/' . $contact->image_path) : null;

        return response()->json($contact);
    }

    /**
     * Atualiza um contato existente.
     */
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
                'image' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml|max:4096',
                'is_favorite' => 'sometimes|boolean',
                'sort_order' => 'sometimes|integer',
            ]);

            // Atualiza imagem se enviada
            if ($request->hasFile('image')) {
                if ($contact->image_path && Storage::disk('public')->exists($contact->image_path)) {
                    Storage::disk('public')->delete($contact->image_path);
                }

                $validatedData['image_path'] = $request->file('image')->store('images', 'public');
            }

            $contact->update($validatedData);
            $contact->image_url = $contact->image_path ? asset('storage/' . $contact->image_path) : null;

            return response()->json($contact);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Exclui um contato.
     */
    public function destroy(Request $request, Contact $contact)
    {
        if ($request->user()->id !== $contact->user_id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        if ($contact->image_path && Storage::disk('public')->exists($contact->image_path)) {
            Storage::disk('public')->delete($contact->image_path);
        }

        $contact->delete();

        return response()->json(['message' => 'Contato apagado com sucesso'], 200);
    }

    /**
     * Alterna o estado de favorito do contato.
     * Não altera a ordem visual (sort_order permanece igual).
     */
    public function toggleFavorite(Request $request, Contact $contact)
    {
        if ($request->user()->id !== $contact->user_id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $contact->is_favorite = !$contact->is_favorite;
        $contact->save();

        $contact->image_url = $contact->image_path ? asset('storage/' . $contact->image_path) : null;

        return response()->json($contact);
    }

    /**
     * Atualiza a ordem dos contatos.
     */
    public function updateSortOrder(Request $request)
    {
        $validated = $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
        ]);

        $userContacts = $request->user()->contacts()->pluck('id')->toArray();

        foreach ($validated['contact_ids'] as $index => $contactId) {
            if (in_array($contactId, $userContacts)) {
                Contact::where('id', $contactId)
                    ->where('user_id', $request->user()->id)
                    ->update(['sort_order' => $index + 1]);
            }
        }

        return response()->json(['message' => 'Ordem atualizada com sucesso']);
    }
}
