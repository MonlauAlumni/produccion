<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite('resources/js/app.js') <!-- Vincula el archivo JS de tu app Vue -->
</head>
<body>
    
    @inertia
    
</body>
</html>
