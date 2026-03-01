<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SignalConfig;
use Illuminate\Http\Request;

class SignalConfigController extends Controller
{
    /**
     * GET /api/signal_config
     * List all signal configs
     */
    public function index()
    {
        $configs = SignalConfig::all()->map(function ($config) {
            return [
                'id' => $config->id,
                'status' => $config->status,
                'pairs' => $config->pairs,
                'api_keys' => $config->api_keys,
            ];
        });

        return response()->json($configs);
    }

    /**
     * POST /api/signal_config
     * Create a new signal config
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'status' => 'required|in:active,inactive',
            'pairs' => 'required|string',
            'api_keys' => 'required|string',
        ]);

        $config = SignalConfig::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Signal config created',
            'data' => [
                'id' => $config->id,
                'status' => $config->status,
                'pairs' => $config->pairs,
                'api_keys' => $config->api_keys,
            ],
        ], 201);
    }

    /**
     * PUT /api/signal_config/{id}
     * Update a signal config
     */
    public function update(Request $request, $id)
    {
        $config = SignalConfig::findOrFail($id);

        $data = $request->validate([
            'status' => 'sometimes|in:active,inactive',
            'pairs' => 'sometimes|string',
            'api_keys' => 'sometimes|string',
        ]);

        $config->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Signal config updated',
            'data' => [
                'id' => $config->id,
                'status' => $config->status,
                'pairs' => $config->pairs,
                'api_keys' => $config->api_keys,
            ],
        ]);
    }

    /**
     * DELETE /api/signal_config/{id}
     * Delete a signal config
     */
    public function destroy($id)
    {
        $config = SignalConfig::findOrFail($id);
        $config->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Signal config deleted',
        ]);
    }
}
