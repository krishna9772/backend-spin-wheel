<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lucky Splash Wheel</title>
    <link rel="stylesheet" href="{{ asset('css/spin.css') }}">
</head>
<body>
    <div id="app"></div>
    <script>
        const LOGO_URL = "{{ asset('images/logo.jpg') }}";
        const BG_URL = "{{ asset('images/background.jpg') }}";
    </script>
    <script src="{{ asset('js/spin.js') }}"></script>
</body>
</html>