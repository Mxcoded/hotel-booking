{{--
    Tailwind Play CDN for realtime utility styling (esp. Availability Calendar).

    Loaded with preflight disabled and class-based dark mode so Filament's
    own compiled CSS stays authoritative for its components while ANY new
    Tailwind class typed into custom views (calendar blade, etc.) renders
    instantly - no rebuild needed.

    Note: needs internet access. public/css/filament/calendar-palette.css
    remains registered as the offline/fallback layer for the calendar.
--}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        corePlugins: {
            preflight: false,
        },
    };
</script>
