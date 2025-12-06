<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DedupeVariations extends Command
{
    protected $signature = 'variations:dedupe {--apply : Actually perform deletions/merges}';
    protected $description = 'Detect and optionally deduplicate product_variations groups by attributes_hash (merge stocks)';

    public function handle()
    {
        $apply = $this->option('apply');
        $this->info('Scanning for duplicate variation attribute groups...');

        $rows = DB::table('product_variations')
            ->select('product_id', 'attributes_hash', DB::raw('COUNT(*) as c'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->whereNotNull('attributes_hash')
            ->groupBy('product_id', 'attributes_hash')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $report = ['generated_at' => date('c'), 'groups' => []];

        foreach ($rows as $r) {
            $ids = array_map('intval', explode(',', $r->ids));
            sort($ids);
            $keep = array_shift($ids);
            $others = $ids;

            $report['groups'][] = [
                'product_id' => (int)$r->product_id,
                'attributes_hash' => $r->attributes_hash,
                'keep' => $keep,
                'remove' => $others,
                'count' => (int)$r->c,
            ];

            if ($apply) {
                DB::transaction(function() use ($keep, $others) {
                    // Sum stocks from others
                    $sum = DB::table('product_variations')->whereIn('id', $others)->sum('stock_quantity');
                    if ($sum > 0) {
                        DB::table('product_variations')->where('id', $keep)->increment('stock_quantity', $sum);
                    }
                    // Optionally merge other useful fields if missing on keep
                    $keepRow = DB::table('product_variations')->where('id', $keep)->first();
                    $othersRows = DB::table('product_variations')->whereIn('id', $others)->get();
                    foreach ($othersRows as $or) {
                        if (empty($keepRow->color_hex) && !empty($or->color_hex)) {
                            DB::table('product_variations')->where('id', $keep)->update(['color_hex' => $or->color_hex]);
                            $keepRow = DB::table('product_variations')->where('id', $keep)->first();
                            break;
                        }
                    }

                    // delete others
                    DB::table('product_variations')->whereIn('id', $others)->delete();
                });
            }
        }

        $path = 'storage/app/variations_dedupe_report.json';
        try { Storage::disk('local')->put('variations_dedupe_report.json', json_encode($report, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)); } catch (\Throwable $e) {}

        $this->info('Found ' . count($report['groups']) . ' duplicate groups. Report written to storage/app/variations_dedupe_report.json');
        if (!$apply) $this->info('Run with --apply to actually merge/delete duplicates.');
        return 0;
    }
}
