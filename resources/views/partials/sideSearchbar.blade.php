<div class="side-searchbar">
    <input type="text" id="search" placeholder="Search..." style="color: white;" pattern="[a-zA-Z0-9\s]+">
    <div id="results-users"></div>
    @if (Auth::check())
        <div id="results-spaces"></div>
        <div id="results-groups"></div>
        <div id="results-comments"></div>
    @endif

    <div class ="trend-content">
        @php
            $trends = App\Models\LikeSpace::orderBy('space_id', 'desc')->take(5)->get();
        @endphp
        <p>Trending <i class="fa-solid fa-arrow-trend-up"></i></p>
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
