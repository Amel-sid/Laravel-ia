<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssistantMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pas d'authentification requise pour l'assistant anonyme
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'session_id' => 'required|string|max:100',
            'message_type' => 'required|string|in:document_selection,answer,generate',
            'content' => 'required|string|max:500'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'session_id.required' => 'Session ID manquant',
            'session_id.string' => 'Session ID invalide',
            'session_id.max' => 'Session ID trop long',

            'message_type.required' => 'Type de message manquant',
            'message_type.in' => 'Type de message invalide',

            'content.required' => 'Contenu du message manquant',
            'content.string' => 'Contenu du message invalide',
            'content.max' => 'Message trop long (maximum 500 caractères)'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'session_id' => 'identifiant de session',
            'message_type' => 'type de message',
            'content' => 'contenu'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->wantsJson()) {
            $response = response()->json([
                'success' => false,
                'error' => 'Données invalides',
                'details' => $validator->errors()
            ], 422);

            throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
        }

        parent::failedValidation($validator);
    }
}
