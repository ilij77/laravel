<?php

namespace App\Http\Controllers\Admin;

use App\Entity\User\User;
use App\Http\Requests\Admin\Users\CreateRequest;
use App\Http\Requests\Admin\Users\UpdateRequest;
use App\UseCases\Auth\RegisterService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
 public $service;
public function __construct(RegisterService $service)
{

    $this->service = $service;
    $this->middleware('can:manage-users');
}

    public function index(Request $request)
    {
        $query=User::orderByDesc('id');

        if (!empty($value=$request->get('id'))){
            $query->where('id',$value);
        }

        if (!empty($value=$request->get('name'))){
            $query->where('name','like','%'.$value.'%');
        }

        if (!empty($value=$request->get('email'))){
            $query->where('email','like','%'.$value.'%');
        }

        if (!empty($value=$request->get('status'))){
            $query->where('status',$value);
        }

        if (!empty($value=$request->get('role'))){
            $query->where('role',$value);
        }
        $users=$query->paginate(20);

        //$users=User::orderBy('id','desc')->paginate(20);

        $roles=User::rolesList();
        $statuses=[
            User::STATUS_ACTIVE=>'Active',
            User::STATUS_WAIT=>'Waiting',
        ];
        return view('admin.users.index',compact('users','roles','statuses'));
    }


    public function create()
    {
        return view('admin.users.create');
    }


    public function store(CreateRequest $request)
    {

       $user=User::create([
           'name'=>$request['name'],
           'email'=>$request['email'],
           'status'=>User::STATUS_WAIT,
           'password'=>Hash::make(Str::random()),
       ]);

       return redirect()->route('admin.users.show',['id'=>$user->id]);
    }


    public function show(User $user)
    {
        return view('admin.users.show',compact('user'));
    }


    public function edit(User $user)
    {

        $roles=User::rolesList();

        return view('admin.users.edit',compact('user','roles'));
    }


    public function update(UpdateRequest $request, User $user)
    {

        $user->update($request->only(['name','email','status','role']));
        return redirect()->route('admin.users.show',$user);
    }



    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index');
    }
    public function verify(User $user){
        $this->service->verify($user->id);
        return redirect()->route('admin.users.show',$user);
    }
}
