@foreach($categories as $category)
    <a href="{{ route('store.shop', ['category' => $category->slug]) }}" 
       class="{{ request('category') == $category->slug ? 'active' : '' }}" 
       style="font-size: {{ max(14 - ($depth * 1), 12) }}px; padding: 4px 12px; opacity: {{ max(1 - ($depth * 0.1), 0.7) }}; border-radius: 6px; display: block; text-decoration: none; color: inherit;">
        @if($depth > 0)
            <span style="opacity: 0.5; margin-right: 4px;">{{ str_repeat('—', $depth) }}</span>
        @endif
        {{ $category->name }}
    </a>
    @if($category->children->count() > 0)
        <div style="padding-left: 12px; display: flex; flex-direction: column; gap: 4px; margin-bottom: {{ $depth == 0 ? '8px' : '4px' }}; border-left: 1px solid rgba(255,255,255,0.05); margin-left: 12px;">
            @include('store.partials.category_tree', ['categories' => $category->children, 'depth' => $depth + 1])
        </div>
    @endif
@endforeach
