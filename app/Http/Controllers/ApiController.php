<?php

namespace App\Http\Controllers;

use App\Services\EvolutionApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    private $wsInstance = "vtfotografia";
    private $eas;
    public function __construct(EvolutionApiService $evolutionService)
    {
        $this->eas = $evolutionService;
    }

    /**
     * funcion q envia mensaje ws
     * @return void
     */
    public function sendSms(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'message' => 'required|string|max:100',
            'phone' => [
                'required',
                'regex:/^\+58\d{10}$/', // Valida el formato +58 seguido de 10 dígitos
            ],
        ]);
        if ($fields->fails()) {
            return response()->json(["errors" => $fields->errors()->first()],422);
        }
        $fields = $fields->validated();

        //dd($fields->validated());
        try {
            $this->eas->sendSms(
                [
                    'instanceName' => $this->wsInstance,
                    'message' => $fields["message"],
                    'phone' => $fields["phone"],
                ]
            );
            return response()->json(["message" => "Enviado correctamente"]);
        } catch (Exception $e) {
            return response()->json(["message" => "Error: " . $e], 422);
        }
    }
}
