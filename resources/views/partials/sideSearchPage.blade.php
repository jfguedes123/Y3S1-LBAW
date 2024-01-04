<div class="side-searchpage">
    <div class ="trend-content">
        @php
            $trends = App\Models\LikeSpace::orderBy('space_id', 'desc')->take(5)->get();
        @endphp
        <p>Trending Spaces<i class="fa-solid fa-arrow-trend-up"></i></p>
        <div class="trend">
            @if (Auth::check() && isset($trends) && !empty($trends))
                @foreach ($trends as $trend)
                    @php
                        $real_space = \App\Models\Space::findOrFail($trend->space_id);
                    @endphp
                    <a href="/space/{{ $trend->space_id }}" class="trend-card">{{ $real_space->content }}</a>
                @endforeach
            @endif
        </div>
    </div>
</div>
