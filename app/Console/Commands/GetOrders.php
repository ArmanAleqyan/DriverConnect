<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Http\Controllers\Parsing\OrdersController;
class GetOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $new = new OrdersController;
        $new->get_hour_status_transporting();
        $new->get_order();
    }
}
