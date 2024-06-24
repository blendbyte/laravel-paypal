<?php

namespace Srmklive\PayPal\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class PublishAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paypal:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the PayPal JS SDK and related code detailing the API implementation';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment('Installing the PayPal JS SDK through npm');

        $result = Process::run('npm install --save @paypal/paypal-js');
        if ($result->successful()) {
            echo $result->output();
            $this->comment('Installed the PayPal JS SDK.');
        } else {
            echo $result->errorOutput();
            $this->error('Unable to install the PayPal JS SDK.');
        }
    }
}
