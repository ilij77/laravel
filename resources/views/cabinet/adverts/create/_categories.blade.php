<ul>
    @foreach($categories as $category)
        <li>
            <a href="{{route('adverts.create.region',$category)}}">{{$category->name}}</a>
            @include('adverts.create._categories',['categories'=>$category->children])
        </li>

    @endforeach
</ul>