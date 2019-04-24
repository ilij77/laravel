<?php

namespace App\Entity;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $status
 * @property string $role
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public const STATUS_WAIT ='wait';
    public const STATUS_ACTIVE ='active';
    public const ROLE_ADMIN='admin';
    public const ROLE_USER='user';


    protected $fillable = [
        'name', 'email', 'password','status','verify_token','role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function register(string $name, string  $email, string $password):self {
        return static ::create([
            'name'=>$name,
            'email'=>$email,
            'password'=>Hash::make($password),
            'verify_token'=>Str::uuid(),
            'status'=>self::STATUS_WAIT,
            'role'=>self::ROLE_USER,
             ]);
    }

    public static function new($name,$email):self {
        return static ::create([
            'name'=>$name,
            'email'=>$email,
            'password'=>Hash::make(Str::random()),
            'status'=>self::STATUS_ACTIVE,
            'role'=>self::ROLE_USER,
           ]);
    }
    public function isWait():bool{
        return $this->status === self::STATUS_WAIT;
    }
    public function isActive():bool{
        return $this->status === self::STATUS_ACTIVE;
    }

    public function verify():void{
        if (!$this->isWait()){
            throw new \DomainException('User is already verified.');
        }
        $this->update([
            'status'=>self::STATUS_ACTIVE,
            'verify_token'=>null,
        ]);
    }
    public function changeRole($role):void
    {
        if (!\in_array($role,[self::ROLE_USER,self::ROLE_ADMIN],true)){
            throw new \InvalidArgumentException('Undefined role"' .$role. '"');
        }
        if ($this->role===$role){
            throw new \DomainException('Role is already assigned.');
        }
        $this->update(['role'=>$role]);
    }

    public function isAdmin():bool
    {
        return$this->role===self::ROLE_ADMIN;
    }
}
