<?php

namespace App\Console\Commands\User;

use App\Entity\User\User;
use App\UseCases\Auth\RegisterService;
use Illuminate\Console\Command;


class VerifyCommand extends Command
{
    protected $signature = 'user:verify {email}';
    private $service;

    protected $description = 'Command description';
    public function __construct(RegisterService $service)
    {
        parent::__construct();
        $this->service=$service;
    }


    public function handle():bool
    {
        $email=$this->argument('email');
        if (!$user=User::where('email',$email)->first()){
            $this->error('Undefined user wish email ' .$email);
            return false;
        }
        try{
            $this->service->verify($user->id);
        }catch (\DomainException $e){
            $this->error($e->getMessage());
            return false;
        }
        $this->service->verify($user->id);
        $this->info('Success');

        return true;
    }
}
