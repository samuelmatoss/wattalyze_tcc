<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use InfluxDB2\Client;
use InfluxDB2\Model\BucketRetentionRules;
use InfluxDB2\Model\DBRP;
use InfluxDB2\Service\DBRPsService;
use InfluxDB2\Service\BucketsService;

class SetupInfluxDB extends Command
{
    protected $signature = 'influx:setup';
    protected $description = 'Setup InfluxDB database and retention policies';

    public function handle()
    {
        $client = new Client([
            'url' => config('influxdb.url'),
            'token' => config('influxdb.token'),
        ]);
        
        $org = config('influxdb.org');
        $bucket = config('influxdb.bucket');
        $database = config('influxdb.database');
        
        try {
            // Criar bucket
            $this->createBucket($client, $org, $bucket);
            
            // Criar DBRP mapping
            $this->createDBRP($client, $database, $bucket, $org);
            
            $this->info("InfluxDB setup completed successfully.");
            
        } catch (\Exception $e) {
            $this->error("InfluxDB setup failed: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    protected function createBucket($client, $org, $bucket)
    {
        $bucketsApi = $client->createService(BucketsService::class);
        
        // Verificar se o bucket já existe
        $existing = $bucketsApi->getBuckets(null, $bucket);
        
        if ($existing && count($existing->getBuckets())) {
            $this->info("Bucket ",$bucket," already exists.");
            return;
        }
        
        // Criar bucket com política de retenção
        $retentionRule = new BucketRetentionRules();
        $retentionRule->setEverySeconds(86400 * 365); // 1 ano
        
        $bucketData = [
            'name' => $bucket,
            'orgID' => $org,
            'retention_rules' => [$retentionRule],
        ];
        
        $bucketsApi->postBuckets($bucketData);
        $this->info("Bucket '$bucket' created successfully.");
    }
    
    protected function createDBRP($client, $database, $bucket, $org)
    {
        $dbrpService = $client->createService(DBRPsService::class);
        
        $dbrp = new DBRP();
        $dbrp->setDatabase($database);
        $dbrp->setBucketID($bucket);
        $dbrp->setDefault(true);
        $dbrp->setOrgID($org);
        
        $dbrpService->postDBRP($dbrp);
        $this->info("DBRP mapping created for database '$database' to bucket '$bucket'.");
    }
}