<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestHomepageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-homepage-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Homepage Controller...');

        try {
            $controller = app(\Modules\HomePage\App\Http\Controllers\HomePageController::class);

            // Test the index method
            $response = $controller->index();

            $this->info('Homepage controller executed successfully');

            if ($response instanceof \Illuminate\View\View) {
                $this->info('View returned: '.$response->getName());
                $this->info('Data keys: '.implode(', ', array_keys($response->getData())));
            } else {
                $this->error('Unexpected response type: '.get_class($response));
            }

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
            $this->error('File: '.$e->getFile().':'.$e->getLine());
            $this->error('Trace: '.$e->getTraceAsString());
        }
    }
}
