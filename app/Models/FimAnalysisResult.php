<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FimAnalysisResult extends Model
{
    protected $fillable = [
        'indexer_doc_id',
        'wazuh_alert_id',
        'event_timestamp',
        'analysis_date',

        'agent_id',
        'agent_name',
        'agent_ip',

        'rule_id',
        'rule_level',
        'rule_description',
        'rule_groups',

        'file_path',
        'file_extension',
        'event_type',
        'changed_attributes',

        'user_name',
        'process_name',

        'size_before',
        'size_after',
        'perm_before',
        'perm_after',
        'old_md5',
        'new_md5',
        'old_sha1',
        'new_sha1',
        'old_sha256',
        'new_sha256',

        'is_empty_file',
        'risk_hints',
        'risk_hint',
        'occurrence_count',
        'first_seen',
        'last_seen',

        'classification',
        'risk_score',
        'reason',
        'recommendation',
        'analysis_source',
        'llm_batch_number',

        'diff',
        'full_log',
        'raw_event',
    ];

    protected $casts = [
        'event_timestamp' => 'datetime',
        'analysis_date' => 'date',
        'first_seen' => 'datetime',
        'last_seen' => 'datetime',

        'rule_groups' => 'array',
        'changed_attributes' => 'array',
        'risk_hints' => 'array',
        'raw_event' => 'array',

        'is_empty_file' => 'boolean',
        'rule_level' => 'integer',
        'risk_score' => 'integer',
        'occurrence_count' => 'integer',
        'llm_batch_number' => 'integer',
    ];
}