<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReportVariationDuplicates extends Command
{
    protected $signature = 'variations:report-duplicates {--output=storage/app/cleanup_report.json}';
    protected $description = 'Scan product_variations for duplicate attribute groups and emit a cleanup_report.json';

    public function handle()
    {
        $out = $this->option('output');
        $this->info('Scanning product_variations for duplicates...');

        // First, ensure attributes_hash is populated for rows where possible
        $rows = DB::table('product_variations')
            ->select('product_id', 'attributes_hash', DB::raw('COUNT(*) as c'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->whereNotNull('attributes_hash')
            ->groupBy('product_id', 'attributes_hash')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $report = [
            'generated_at' => date('c'),
            'duplicates_by_hash' => [],
            'summary' => [
                'groups_found' => 0,
                'rows' => 0,
            ],
        ];

        foreach ($rows as $r) {
            $ids = explode(',', $r->ids);
            $report['duplicates_by_hash'][] = [
                'product_id' => (int)$r->product_id,
                'attributes_hash' => $r->attributes_hash,
                'count' => (int)$r->c,
                'ids' => array_map('intval', $ids),
            ];
            $report['summary']['groups_found']++;
            $report['summary']['rows'] += (int)$r->c;
        }

        // Also detect duplicate null-hash groups by attributes JSON equality (best-effort, limited)
        $nullRows = DB::table('product_variations')
            ->select('product_id', DB::raw('attributes as attrs'), DB::raw('COUNT(*) as c'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->whereNull('attributes_hash')
            ->whereNotNull('attributes')
            ->groupBy('product_id', 'attributes')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $report['duplicates_by_attrs_json'] = [];
        foreach ($nullRows as $r) {
            $ids = explode(',', $r->ids);
            $report['duplicates_by_attrs_json'][] = [
                'product_id' => (int)$r->product_id,
                'attributes_json' => $r->attrs,
                'count' => (int)$r->c,
                'ids' => array_map('intval', $ids),
            ];
            $report['summary']['groups_found']++;
            $report['summary']['rows'] += (int)$r->c;
        }

        // write report
        $json = json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents(base_path($out), $json);
        $this->info('Report written to ' . base_path($out));
        $this->line('Groups: ' . $report['summary']['groups_found'] . ', rows involved: ' . $report['summary']['rows']);
        return 0;
    }
}
