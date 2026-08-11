@foreach($categories as $category)
    <a href="{{ route('store.shop', ['category' => $category->slug]) }}" 
       style="{{ request('category') == $category->slug ? 'color: var(--text-primary); background: rgba(255, 255, 255, 0.05);' : '' }} padding-left: {{ 16 + ($depth * 8) }}px; opacity: {{ max(1 - ($depth * 0.15), 0.6) }};">
        {{ $depth > 0 ? '> ' : '' }}{{ $category->name }}
    </a>
    @if($category->children->count() > 0)
        @include('store.partials.mega_tree', ['categories' => $category->children, 'depth' => $depth + 1])
    @endif
@endforeach
