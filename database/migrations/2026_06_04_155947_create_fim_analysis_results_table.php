<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fim_analysis_results', function (Blueprint $table) {
            $table->id();
                        // Identitas alert
            $table->string('indexer_doc_id')->nullable()->index();
            $table->string('wazuh_alert_id')->nullable()->index();

            // Waktu event
            $table->timestamp('event_timestamp')->nullable();
            $table->date('analysis_date')->nullable()->index();

            // Informasi agent
            $table->string('agent_id')->nullable()->index();
            $table->string('agent_name')->nullable()->index();
            $table->string('agent_ip')->nullable();

            // Informasi rule Wazuh
            $table->string('rule_id')->nullable()->index();
            $table->integer('rule_level')->nullable();
            $table->text('rule_description')->nullable();
            $table->json('rule_groups')->nullable();

            // Informasi file
            $table->text('file_path')->nullable();
            $table->string('file_extension')->nullable();
            $table->string('event_type')->nullable()->index();
            $table->json('changed_attributes')->nullable();

            // Informasi user/process
            $table->string('user_name')->nullable();
            $table->text('process_name')->nullable();

            // Informasi perubahan file
            $table->string('size_before')->nullable();
            $table->string('size_after')->nullable();
            $table->string('perm_before')->nullable();
            $table->string('perm_after')->nullable();
            $table->string('old_md5')->nullable();
            $table->string('new_md5')->nullable();
            $table->string('old_sha1')->nullable();
            $table->string('new_sha1')->nullable();
            $table->string('old_sha256')->nullable();
            $table->string('new_sha256')->nullable();

            // Hasil reducer
            $table->boolean('is_empty_file')->default(false);
            $table->json('risk_hints')->nullable();
            $table->string('risk_hint')->nullable();
            $table->integer('occurrence_count')->default(1);
            $table->timestamp('first_seen')->nullable();
            $table->timestamp('last_seen')->nullable();

            // Hasil analisis
            $table->enum('classification', ['aman', 'mencurigakan', 'berbahaya'])->index();
            $table->integer('risk_score')->default(0);
            $table->text('reason')->nullable();
            $table->text('recommendation')->nullable();
            $table->string('analysis_source')->nullable(); // llm / rule_based / rule_based_overflow
            $table->integer('llm_batch_number')->nullable();

            // Raw data untuk audit/debug
            $table->longText('diff')->nullable();
            $table->longText('full_log')->nullable();
            $table->json('raw_event')->nullable();

            // Biar event yang sama tidak dobel terus kalau job harian dijalankan ulang
            $table->unique(
                ['analysis_date', 'agent_id', 'file_path', 'event_type', 'rule_id'],
                'fim_unique_daily_event'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fim_analysis_results');
    }
};
