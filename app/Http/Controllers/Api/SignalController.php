<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Signal;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    /**
     * GET /api/signals
     * List all signals (newest first)
     */
    public function index()
    {
        $signals = Signal::latest()->get();

        return response()->json([
            'status' => 'success',
            'count' => $signals->count(),
            'data' => $signals,
        ]);
    }

    /**
     * POST /api/signals
     * Accept array of signal objects and bulk insert
     */
    public function store(Request $request)
    {
        $signals = $request->all();

        // Support both single object and array of objects
        if (isset($signals['signal'])) {
            $signals = [$signals];
        }

        $created = [];
        foreach ($signals as $item) {
            $created[] = Signal::create([
                'row_number' => $item['row_number'] ?? null,
                'signal' => $item['signal'],
                'pair' => $item['pair'],
                'price' => $item['price'] ?? '',
                'sl' => $item['sl'] ?? null,
                'tp' => $item['tp'] ?? null,
                'reason' => $item['reason'] ?? null,
                'signal_timestamp' => $item['timestamp'] ?? null,
                'score' => $item['score'] ?? null,
                'stars' => $item['stars'] ?? null,
                'conf_level' => $item['confLevel'] ?? $item['conf_level'] ?? null,
                'last_sl' => $item['last_sl'] ?? null,
                'last_tp' => $item['last_tp'] ?? null,
                'result' => $item['result'] ?? null,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => count($created) . ' signal(s) created',
            'data' => $created,
        ], 201);
    }

    /**
     * PUT /api/signals/{id}
     * Update a signal
     */
    public function update(Request $request, $id)
    {
        $signal = Signal::findOrFail($id);

        $data = $request->validate([
            'row_number' => 'sometimes|integer|nullable',
            'signal' => 'sometimes|string',
            'pair' => 'sometimes|string',
            'price' => 'sometimes|string',
            'sl' => 'sometimes|string|nullable',
            'tp' => 'sometimes|string|nullable',
            'reason' => 'sometimes|string|nullable',
            'timestamp' => 'sometimes|string|nullable',
            'score' => 'sometimes|string|nullable',
            'stars' => 'sometimes|string|nullable',
            'confLevel' => 'sometimes|string|nullable',
            'last_sl' => 'sometimes|string|nullable',
            'last_tp' => 'sometimes|string|nullable',
            'result' => 'sometimes|string|nullable',
        ]);

        // Map camelCase fields from API to snake_case DB columns
        if (isset($data['timestamp'])) {
            $data['signal_timestamp'] = $data['timestamp'];
            unset($data['timestamp']);
        }
        if (isset($data['confLevel'])) {
            $data['conf_level'] = $data['confLevel'];
            unset($data['confLevel']);
        }

        $signal->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Signal updated',
            'data' => $signal,
        ]);
    }

    /**
     * DELETE /api/signals/{id}
     * Delete a signal
     */
    public function destroy($id)
    {
        $signal = Signal::findOrFail($id);
        $signal->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Signal deleted',
        ]);
    }

    /**
     * POST /api/signals/bulk-delete
     * Bulk delete signals by IDs
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:signals,id',
        ]);

        $count = Signal::whereIn('id', $request->ids)->delete();

        return response()->json([
            'status' => 'success',
            'message' => $count . ' signal(s) deleted',
        ]);
    }

    /**
     * GET /api/signals/stats?date=2026-02-22
     * Get daily signal statistics
     */
    public function stats(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $signals = Signal::whereDate('created_at', $date)->get();

        $total = $signals->count();
        $buyCount = $signals->where('signal', 'BUY')->count();
        $sellCount = $signals->where('signal', 'SELL')->count();

        // Count TP hits and SL hits from result field
        $tpHits = $signals->filter(fn($s) => stripos($s->result, 'TP') !== false || stripos($s->result, 'WIN') !== false)->count();
        $slHits = $signals->filter(fn($s) => stripos($s->result, 'SL') !== false || stripos($s->result, 'LOSS') !== false)->count();

        $resolved = $tpHits + $slHits;
        $winRate = $resolved > 0 ? round(($tpHits / $resolved) * 100, 2) . '%' : '0%';

        // Top pair by frequency
        $topPair = $signals->groupBy('pair')->sortByDesc(fn($group) => $group->count())->keys()->first() ?? '-';

        return response()->json([
            [
                'date' => $date,
                'total_signals' => $total,
                'buy_count' => $buyCount,
                'sell_count' => $sellCount,
                'tp_hits' => $tpHits,
                'sl_hits' => $slHits,
                'win_rate' => $winRate,
                'top_pair' => $topPair,
            ]
        ]);
    }
}

