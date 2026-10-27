<x-layouts.app title="About">
    <div id="about-page-content">
        {!! $aboutPage?->html ?? '' !!}
    </div>

    @if ($aboutPage?->css)
        <style>
            {!! $aboutPage->css !!}
        </style>
    @endif

    @if ($aboutPage?->javascript)
        <script>
            {!! $aboutPage->javascript !!}
        </script>
    @endif
</x-layouts.app>
