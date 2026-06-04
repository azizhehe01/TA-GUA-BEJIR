<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FimAnalysisResult;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class FimAnalysisController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'indexer_doc_id' => ['nullable', 'string'],
            'wazuh_alert_id' => ['nullable', 'string'],

            'timestamp' => ['nullable', 'string'],

            'agent_id' => ['nullable', 'string'],
            'agent_name' => ['nullable', 'string'],
            'agent_ip' => ['nullable', 'string'],

            'rule_id' => ['nullable', 'string'],
            'rule_level' => ['nullable', 'integer'],
            'rule_description' => ['nullable', 'string'],
            'rule_groups' => ['nullable', 'array'],

            'file_path' => ['nullable', 'string'],
            'file_extension' => ['nullable', 'string'],
            'event_type' => ['nullable', 'string'],
            'changed_attributes' => ['nullable', 'array'],

            'user_name' => ['nullable', 'string'],
            'process_name' => ['nullable', 'string'],

            'size_before' => ['nullable'],
            'size_after' => ['nullable'],
            'perm_before' => ['nullable', 'string'],
            'perm_after' => ['nullable', 'string'],
            'old_md5' => ['nullable', 'string'],
            'new_md5' => ['nullable', 'string'],
            'old_sha1' => ['nullable', 'string'],
            'new_sha1' => ['nullable', 'string'],
            'old_sha256' => ['nullable', 'string'],
            'new_sha256' => ['nullable', 'string'],

            'is_empty_file' => ['nullable', 'boolean'],
            'risk_hints' => ['nullable', 'array'],
            'risk_hint' => ['nullable', 'string'],
            'occurrence_count' => ['nullable', 'integer'],
            'first_seen' => ['nullable', 'string'],
            'last_seen' => ['nullable', 'string'],

            'classification' => ['required', 'in:aman,mencurigakan,berbahaya'],
            'risk_score' => ['required', 'integer', 'min:0', 'max:100'],
            'reason' => ['nullable', 'string'],
            'recommendation' => ['nullable', 'string'],
            'analysis_source' => ['nullable', 'string'],
            'llm_batch_number' => ['nullable', 'integer'],

            'diff' => ['nullable', 'string'],
            'full_log' => ['nullable', 'string'],
            'raw_event' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $eventTimestamp = $this->parseDateTime($data['timestamp'] ?? null);
        $firstSeen = $this->parseDateTime($data['first_seen'] ?? null);
        $lastSeen = $this->parseDateTime($data['last_seen'] ?? null);

        $analysisDate = $eventTimestamp
            ? $eventTimestamp->toDateString()
            : now()->toDateString();

        $payload = [
            'indexer_doc_id' => $data['indexer_doc_id'] ?? null,
            'wazuh_alert_id' => $data['wazuh_alert_id'] ?? null,
            'event_timestamp' => $eventTimestamp,
            'analysis_date' => $analysisDate,

            'agent_id' => $data['agent_id'] ?? null,
            'agent_name' => $data['agent_name'] ?? null,
            'agent_ip' => $data['agent_ip'] ?? null,

            'rule_id' => $data['rule_id'] ?? null,
            'rule_level' => $data['rule_level'] ?? null,
            'rule_description' => $data['rule_description'] ?? null,
            'rule_groups' => $data['rule_groups'] ?? null,

            'file_path' => $data['file_path'] ?? null,
            'file_extension' => $data['file_extension'] ?? null,
            'event_type' => $data['event_type'] ?? null,
            'changed_attributes' => $data['changed_attributes'] ?? null,

            'user_name' => $data['user_name'] ?? null,
            'process_name' => $data['process_name'] ?? null,

            'size_before' => isset($data['size_before']) ? (string) $data['size_before'] : null,
            'size_after' => isset($data['size_after']) ? (string) $data['size_after'] : null,
            'perm_before' => $data['perm_before'] ?? null,
            'perm_after' => $data['perm_after'] ?? null,
            'old_md5' => $data['old_md5'] ?? null,
            'new_md5' => $data['new_md5'] ?? null,
            'old_sha1' => $data['old_sha1'] ?? null,
            'new_sha1' => $data['new_sha1'] ?? null,
            'old_sha256' => $data['old_sha256'] ?? null,
            'new_sha256' => $data['new_sha256'] ?? null,

            'is_empty_file' => $data['is_empty_file'] ?? false,
            'risk_hints' => $data['risk_hints'] ?? null,
            'risk_hint' => $data['risk_hint'] ?? null,
            'occurrence_count' => $data['occurrence_count'] ?? 1,
            'first_seen' => $firstSeen,
            'last_seen' => $lastSeen,

            'classification' => $data['classification'],
            'risk_score' => $data['risk_score'],
            'reason' => $data['reason'] ?? null,
            'recommendation' => $data['recommendation'] ?? null,
            'analysis_source' => $data['analysis_source'] ?? null,
            'llm_batch_number' => $data['llm_batch_number'] ?? null,

            'diff' => $data['diff'] ?? null,
            'full_log' => $data['full_log'] ?? null,
            'raw_event' => $data['raw_event'] ?? null,
        ];

        $result = FimAnalysisResult::updateOrCreate(
            [
                'analysis_date' => $analysisDate,
                'agent_id' => $payload['agent_id'],
                'file_path' => $payload['file_path'],
                'event_type' => $payload['event_type'],
                'rule_id' => $payload['rule_id'],
            ],
            $payload
        );

        return response()->json([
            'message' => 'FIM analysis result saved',
            'data' => [
                'id' => $result->id,
                'classification' => $result->classification,
                'risk_score' => $result->risk_score,
                'analysis_source' => $result->analysis_source,
            ],
        ], 201);
    }

    public function summary(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $query = FimAnalysisResult::whereDate('analysis_date', $date);

        return response()->json([
            'date' => $date,
            'total' => (clone $query)->count(),
            'aman' => (clone $query)->where('classification', 'aman')->count(),
            'mencurigakan' => (clone $query)->where('classification', 'mencurigakan')->count(),
            'berbahaya' => (clone $query)->where('classification', 'berbahaya')->count(),
            'llm' => (clone $query)->where('analysis_source', 'llm')->count(),
            'rule_based' => (clone $query)->where('analysis_source', 'rule_based')->count(),
        ]);
    }

    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $classification = $request->query('classification');

        $query = FimAnalysisResult::query()
            ->whereDate('analysis_date', $date)
            ->latest('risk_score')
            ->latest('event_timestamp');

        if ($classification) {
            $query->where('classification', $classification);
        }

        return response()->json([
            'date' => $date,
            'data' => $query->paginate(20),
        ]);
    }

    private function parseDateTime(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}