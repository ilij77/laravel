<?php

namespace App\Entity\User;
use App\Entity\Adverts\Advert\Advert;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Passport\HasApiTokens;

/**
* @property int $id
* @property string $name
* @property string $last_name
* @property string $email
* @property string $status
* @property string $phone
* @property string $phone_verify_token
* @property bool $phone_verified
* @property Carbon $phone_verify_token_expire
 * @property Network[] networks
 * @method Builder byNetwork(string $network, string $identity)
*/
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public const STATUS_WAIT='wait';
    public const STATUS_ACTIVE='active';
    public const ROLE_ADMIN='admin';
    public const ROLE_USER='user';
    public const ROLE_MODERATOR='moderator';


    protected $fillable = [
        'name', 'email','email_verified_at', 'password', 'remember_token',
        'verify_token', 'role', 'status', 'updated_at', 'created_at','last_name','phone','phone_auth',
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
        'phone_verified'=>'boolean',
        'phone_verify_token_expire'=>'datetime',
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
        if (!\in_array($role,self::rolesList(),true)){
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

    public function isModerator():bool
    {
        return$this->role===self::ROLE_MODERATOR;
    }

    public function unverifyPhone():void
    {
        $this->phone_verified=false;
        $this->phone_verify_token=null;
        $this->phone_verify_token_expire=null;
        $this->saveOrFail();
    }

    public function requestPhoneVerification(Carbon $now):string
    {
        if (empty($this->phone)){
            throw new \DomainException('Phone number is empty.');
        }
        if (!empty($this->phone_verify_token)&&$this->phone_verify_token_expire &&
            $this->phone_verify_token_expire->gt($now)){
            throw new \DomainException('Token is already requested.');
        }
        $this->phone_verified=false;
        $this->phone_verify_token=(string)random_int(10000,99999);
        $this->phone_verify_token_expire=$now->copy()->addSecond(300);
        $this->saveOrFail();

        return $this->phone_verify_token;
    }

    public function verifyPhone($token,Carbon $now):void
    {
        if ($token !==$this->phone_verify_token){
            throw new \DomainException('Incorrect verify token.');
        }
        if ($this->phone_verify_token_expire->lt($now)){
            throw new \DomainException('Token is expired.');
        }
        $this->phone_verified=true;
        $this->phone_verify_token=null;
        $this->phone_verify_token_expire=null;
        $this->saveOrFail();
    }
    public function isPhoneVerified(): bool
    {
        return $this->phone_verified;
    }
    public function enablePhoneAuth(): void
    {
        if (empty($this->phone)) {
            throw new \DomainException('Phone number is empty.');
        }
        $this->phone_auth = true;
        $this->saveOrFail();
    }

    public function disablePhoneAuth(): void
    {
        $this->phone_auth = false;
        $this->saveOrFail();
    }
    public function isPhoneAuthEnabled(): bool
    {
        return (bool)$this->phone_auth;
    }
    public function hasFilledProfile(): bool
    {
        return !empty($this->name) && !empty($this->last_name) && $this->isPhoneVerified();
    }

    public static function rolesList():array
    {
        return[
            self::ROLE_USER=>'user',
            self::ROLE_ADMIN=>'admin',
            self::ROLE_MODERATOR=>'moderator',
        ];
    }
public function favorites()
{
    return $this->belongsToMany(Advert::class,'advert_favorites','user_id','advert_id');
}

    public function addToFavorites($id): void
    {
        if ($this->hasInFavorites($id)) {
            throw new \DomainException('This advert is already added to favorites.');
        }
        $this->favorites()->attach($id);
    }

    public function removeFromFavorites($id): void
    {
        $this->favorites()->detach($id);
    }

    public function hasInFavorites($id): bool
    {
        return $this->favorites()->where('id', $id)->exists();
    }

    public function networks()
    {
        return $this->hasMany(Network::class, 'user_id', 'id');
    }

    public function scopeByNetwork(Builder $query, string $network, string $identity): Builder
    {
        return $query->whereHas('networks', function(Builder $query) use ($network, $identity) {
            $query->where('network', $network)->where('identity', $identity);
        });
    }
    public static function registerByNetwork(string $network, string $identity): self
    {
        $user = static::create([
            'name' => $identity,
            'email' => null,
            'password' => null,
            'verify_token' => null,
            'role' => self::ROLE_USER,
            'status' => self::STATUS_ACTIVE,
        ]);
        $user->networks()->create([
            'network' => $network,
            'identity' => $identity,
        ]);
        return $user;
    }
    public function findForPassport($identifier)
    {
        return self::where('email', $identifier)->where('status', self::STATUS_ACTIVE)->first();
    }

}
