@props(['items' => []])

<nav aria-label="breadcrumb" class="text-sm breadcrumbs">
  <ul class="p-0 m-0">
    <li><a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800">Painel</a></li>
    @foreach($items as $item)
        @if($loop->last)
            <li class="text-gray-800 font-semibold" aria-current="page">{{ $item['title'] }}</li>
        @else
            <li><a href="{{ $item['url'] }}" class="text-gray-600 hover:text-gray-800">{{ $item['title'] }}</a></li>
        @endif
    @endforeach
  </ul>
</nav>


