@extends('layouts.app')
@section('content')

    @include ('admin._nav', ['page' => ''])
    {{--<ul class="nav nav-tabs mb-3">--}}
        {{--<li class="nav-item"><a class="nav-link active" href="{{route('admin.home')}}">Dashboard</a></li>--}}
        {{--@can ('manage-adverts')--}}
            {{--<li class="nav-item"><a class="nav-link" href="{{ route('admin.adverts.adverts.index') }}">Adverts</a></li>--}}
        {{--@endcan--}}
        {{--<li class="nav-item"><a class="nav-link " href="{{route('admin.users.index')}}">Users</a></li>--}}
        {{--<li class="nav-item"><a class="nav-link " href="{{route('admin.regions.index')}}">Regions</a></li>--}}
        {{--<li class="nav-item"><a class="nav-link " href="{{route('admin.adverts.categories.index')}}">Category</a></li>--}}

           {{--</ul>--}}
@endsection