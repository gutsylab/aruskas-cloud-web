<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class IssueApiKey extends Command
{
    protected $signature = 'apikey:issue {name} {--rate=120}';
    protected $description = 'Issue an API key for a client (shown once)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $prefix = Str::upper(Str::random(6));
        $secret = Str::random(48);
        $apiKey = $prefix . '.' . $secret;

        $client = ApiClient::create([
            'name' => $this->argument('name'),
            'key_hash' => Hash::make($apiKey),
            'rate_per_min' => (int)$this->option('rate'),
        ]);

        $this->info("Client: {$client->name} (id: {$client->id})");
        $this->info("API KEY (save securely, shown once): {$apiKey}");
        return self::SUCCESS;
    }
}
