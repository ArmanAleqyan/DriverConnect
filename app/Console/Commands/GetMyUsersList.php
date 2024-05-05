<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Http\Controllers\Parsing\GetMyUsersList as GetMyUsersListController;
class GetMyUsersList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetUsers:List';

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
        $new = new GetMyUsersListController;
        $new->get_users();
    }
}
