<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Repositories\ConnectPageRepository;
use App\Repositories\ConnectRepository;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateUserValidateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateUserValidateToken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check valid accessToken';

    protected $repUser;
    protected $con;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $user,
        Controller $con

    )
    {
        parent::__construct();
        $this->repUser = $user;
        $this->con = $con;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = date('Y-m-d 00:00:01');
        Log::info('command ' . $this->signature . ' date: ' . $date);
        $date = new \MongoDB\BSON\UTCDateTime(new \DateTime($date));
        $users = $this->repUser->getDataUpdateByDate($date);
        foreach ($users as $user){
            Log::info($user);
            $token = $this->con->getValidateToken();
            $this->repUser->updateToken($user, $token);
        }
    }


}