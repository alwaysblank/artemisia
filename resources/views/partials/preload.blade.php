@foreach(apply_filters('artemesia/preload/images', []) as $image)
  <link rel="preload" href="{{App\asset_path('images/'.$image)}}" as="image">
@endforeach
