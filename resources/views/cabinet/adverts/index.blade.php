@extends('layouts.app')
@section('content')
    @include('cabinet.adverts._nav')

    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Updated</th>
            <th>Title</th>
            <th>Region</th>
            <th>Category</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>

        @foreach ($adverts as $advert)
            <tr>
                <td>{{ $advert->id }}</td>
                <td>{{ $advert->updated_at }}</td>
                <td><a href="{{ route('adverts.show', $advert) }}" target="_blank">{{ $advert->title }}</a></td>
                <td>
                    @if ($advert->region)
                        {{ $advert->region->name }}
                    @endif
                </td>
                <td>{{ $advert->category->name }}</td>
                <td>
                    @if ($advert->isDraft())
                        <span class="badge badge-secondary">Draft</span>
                    @elseif ($advert->isModeration())
                        <span class="badge badge-primary">Moderation</span>
                    @elseif ($advert->isActive())
                        <span class="badge badge-primary">Active</span>
                    @elseif ($advert->isClosed())
                        <span class="badge badge-secondary">Closed</span>
                    @endif
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
    <div class="row">
        <div class="col-md-9">

            <div class="adverts-list">
                @foreach ($adverts as $advert)
                    <div class="advert">
                        <div class="row">
                            <div class="col-md-3">
                                <div style="height: 180px; background: #f6f6f6; border: 1px solid #ddd"></div>
                            </div>
                            <div class="col-md-9">
                                <span class="float-right">{{ $advert->price }}</span>
                                <div class="h4" style="margin-top: 0"><a href="{{ route('adverts.show', $advert) }}">{{ $advert->title }}</a></div>
                                <p>Region: <a href="">{{ $advert->region ? $advert->region->name : 'All' }}</a></p>
                                <p>Category: <a href="">{{ $advert->category->name }}</a></p>
                                <p>Date: {{ $advert->created_at }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

    {{ $adverts->links() }}
@endsection