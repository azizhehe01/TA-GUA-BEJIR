@php
    $detailId = 'fim-analysis-detail-' . $event->id;

    $formatDateTime = fn ($value) => optional($value)->format('Y-m-d H:i:s') ?? '-';
    $formatDate = fn ($value) => optional($value)->format('Y-m-d') ?? '-';
    $formatBoolean = fn ($value) => $value ? 'true' : 'false';
    $formatJson = function ($value) {
        if (blank($value)) {
            return '-';
        }

        return json_encode(
            $value,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) ?: '-';
    };

    $detailGroups = [
        'Alert' => [
            'ID' => $event->id,
            'Indexer Doc ID' => $event->indexer_doc_id,
            'Wazuh Alert ID' => $event->wazuh_alert_id,
            'Event Timestamp' => $formatDateTime($event->event_timestamp),
            'Analysis Date' => $formatDate($event->analysis_date),
        ],
        'Agent' => [
            'Agent ID' => $event->agent_id,
            'Agent Name' => $event->agent_name,
            'Agent IP' => $event->agent_ip,
            'User' => $event->user_name,
            'Process' => $event->process_name,
        ],
        'Rule' => [
            'Rule ID' => $event->rule_id,
            'Rule Level' => $event->rule_level,
            'Rule Description' => $event->rule_description,
            'Rule Groups' => $formatJson($event->rule_groups),
        ],
        'File' => [
            'File Path' => $event->file_path,
            'Extension' => $event->file_extension,
            'Event Type' => $event->event_type,
            'Changed Attributes' => $formatJson($event->changed_attributes),
        ],
        'Change' => [
            'Size Before' => $event->size_before,
            'Size After' => $event->size_after,
            'Permission Before' => $event->perm_before,
            'Permission After' => $event->perm_after,
            'Empty File' => $formatBoolean($event->is_empty_file),
        ],
        'Reducer' => [
            'Risk Hint' => $event->risk_hint,
            'Risk Hints' => $formatJson($event->risk_hints),
            'Occurrence Count' => $event->occurrence_count,
            'First Seen' => $formatDateTime($event->first_seen),
            'Last Seen' => $formatDateTime($event->last_seen),
        ],
        'Analysis' => [
            'Classification' => $event->classification,
            'Risk Score' => $event->risk_score,
            'Analysis Source' => $event->analysis_source,
            'LLM Batch Number' => $event->llm_batch_number,
            'Reason' => $event->reason,
            'Recommendation' => $event->recommendation,
        ],
    ];

    $hashes = [
        'Old MD5' => $event->old_md5,
        'New MD5' => $event->new_md5,
        'Old SHA1' => $event->old_sha1,
        'New SHA1' => $event->new_sha1,
        'Old SHA256' => $event->old_sha256,
        'New SHA256' => $event->new_sha256,
    ];
@endphp

<tr
    class="cursor-pointer hover:bg-gray-100"
    onclick="document.getElementById('{{ $detailId }}').classList.toggle('hidden'); this.classList.toggle('bg-gray-100')"
    title="Lihat detail lengkap"
>
    <td class="px-4 py-3 whitespace-nowrap">
        {{ optional($event->event_timestamp)->format('Y-m-d H:i') ?? '-' }}
    </td>

    <td class="px-4 py-3">
        <div class="font-bold">{{ $event->agent_name ?? '-' }}</div>
        <div class="text-xs text-gray-500">{{ $event->agent_ip ?? '-' }}</div>
    </td>

    <td class="px-4 py-3 max-w-md">
        <div class="truncate font-medium" title="{{ $event->file_path }}">
            {{ $event->file_path ?? '-' }}
        </div>
        <div class="text-xs text-gray-500">
            Occurrence: {{ $event->occurrence_count ?? 1 }}
            |
            Rule: {{ $event->rule_id ?? '-' }}
        </div>
    </td>

    <td class="px-4 py-3 whitespace-nowrap">
        {{ $event->event_type ?? '-' }}
    </td>

    <td class="px-4 py-3 whitespace-nowrap">
        <span class="rounded-full bg-black px-3 py-1 text-xs font-bold text-white">
            {{ $event->risk_score }}
        </span>
    </td>

    <td class="px-4 py-3 whitespace-nowrap">
        @if($event->classification === 'aman')
            <span class="rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white">
                Aman
            </span>
        @elseif($event->classification === 'mencurigakan')
            <span class="rounded-full bg-yellow-400 px-3 py-1 text-xs font-bold text-black">
                Mencurigakan
            </span>
        @else
            <span class="rounded-full bg-red-500 px-3 py-1 text-xs font-bold text-white">
                Berbahaya
            </span>
        @endif
    </td>

    <td class="px-4 py-3 whitespace-nowrap">
        <span class="rounded-full bg-purple-200 px-3 py-1 text-xs font-bold text-purple-900">
            {{ $event->analysis_source ?? '-' }}
        </span>
    </td>

    <td class="px-4 py-3 max-w-md">
        <div class="line-clamp-2" title="{{ $event->reason }}">
            {{ $event->reason ?? '-' }}
        </div>
        <div class="mt-1 text-xs text-gray-500 line-clamp-2" title="{{ $event->recommendation }}">
            {{ $event->recommendation ?? '-' }}
        </div>
    </td>
</tr>

<tr id="{{ $detailId }}" class="hidden bg-gray-50">
    <td colspan="8" class="px-4 py-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach($detailGroups as $title => $fields)
                <div class="rounded-lg border border-gray-300 bg-white p-3">
                    <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-gray-700">{{ $title }}</h3>

                    <dl class="space-y-2 text-xs">
                        @foreach($fields as $label => $value)
                            <div>
                                <dt class="font-semibold text-gray-500">{{ $label }}</dt>
                                <dd class="mt-0.5 break-words text-gray-900 whitespace-pre-wrap">{{ filled($value) ? $value : '-' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endforeach
        </div>

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-lg border border-gray-300 bg-white p-3">
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-gray-700">Hashes</h3>

                <dl class="space-y-2 text-xs">
                    @foreach($hashes as $label => $value)
                        <div>
                            <dt class="font-semibold text-gray-500">{{ $label }}</dt>
                            <dd class="mt-0.5 break-all text-gray-900">{{ filled($value) ? $value : '-' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="rounded-lg border border-gray-300 bg-white p-3">
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-gray-700">Raw Event</h3>

                <pre class="max-h-64 overflow-auto whitespace-pre-wrap break-words rounded bg-gray-100 p-3 text-xs text-gray-900">{{ $formatJson($event->raw_event) }}</pre>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-lg border border-gray-300 bg-white p-3">
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-gray-700">Diff</h3>

                <pre class="max-h-64 overflow-auto whitespace-pre-wrap break-words rounded bg-gray-100 p-3 text-xs text-gray-900">{{ filled($event->diff) ? $event->diff : '-' }}</pre>
            </div>

            <div class="rounded-lg border border-gray-300 bg-white p-3">
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-gray-700">Full Log</h3>

                <pre class="max-h-64 overflow-auto whitespace-pre-wrap break-words rounded bg-gray-100 p-3 text-xs text-gray-900">{{ filled($event->full_log) ? $event->full_log : '-' }}</pre>
            </div>
        </div>
    </td>
</tr>
