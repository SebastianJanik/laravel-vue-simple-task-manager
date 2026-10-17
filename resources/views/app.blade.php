<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.title') }}</title>
    <script>
        // Runs before first paint to avoid a light/dark flash; useTheme.js owns the toggle afterwards.
        try {
            const stored = localStorage.getItem('theme');
            document.documentElement.classList.toggle('dark', stored === 'dark'
                || (!stored && matchMedia('(prefers-color-scheme: dark)').matches));
        } catch {
            document.documentElement.classList.toggle('dark', matchMedia('(prefers-color-scheme: dark)').matches);
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased">
    <div class="page-background"></div>
    <div id="app"></div>
</body>
</html>
