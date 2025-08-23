<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use InfluxDB2\Client;
use InfluxDB2\Service\DeleteService;
use InfluxDB2\Model\DeletePredicateRequest;
use DateTime;
use DateTimeZone;

class CleanupInfluxData extends Command
{
    protected $signature = 'influx:cleanup 
                            {--older-than=365 : Days to keep}
                            {--dry-run : Show what would be deleted}';

    protected $description = 'Cleanup old data in InfluxDB according to retention policies';

    public function handle()
    {
        $days = (int) $this->option('older-than');
        $cutoff = now()->subDays($days)->utc(); // garantir UTC
        $dryRun = $this->option('dry-run');

        $client = new Client([
            'url'   => config('influxdb.url'),
            'token' => config('influxdb.token'),
            'org'   => config('influxdb.org'),
            'bucket'=> config('influxdb.bucket'),
        ]);

        /** @var DeleteService $deleteService */
        $deleteService = $client->createService(DeleteService::class);

        $predicate = new DeletePredicateRequest();

        // início "desde sempre"
        $start = new DateTime('1970-01-01T00:00:00Z', new DateTimeZone('UTC'));

        // stop exclusivo: tudo < cutoff será deletado
        $stop  = new DateTime($cutoff->toIso8601String());

        $predicate->setStart($start);
        $predicate->setStop($stop);
        $predicate->setPredicate('_measurement="energy_consumption"');

        $this->info("Deleting data older than $days days (before {$cutoff->toDateTimeString()} UTC)");

        if ($dryRun) {
            $this->info('[DRY RUN] No data will be deleted.');
            return;
        }

        try {
            $deleteService->postDelete(
                $predicate,
                null,
                config('influxdb.org'),
                config('influxdb.bucket')
            );
            $this->info('Data cleanup completed successfully.');
        } catch (\Throwable $e) {
            $this->error('Data cleanup failed: ' . $e->getMessage());
        } finally {
            $client->close();
        }
    }
}
