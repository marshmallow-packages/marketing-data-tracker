<?php

namespace Marshmallow\MarketingData\Commands;

use Illuminate\Console\Command;

class MarketingDataCommand extends Command
{
    public $signature = 'marketing-data-tracker';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
