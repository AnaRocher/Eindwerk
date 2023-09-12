<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') / Awesome Shoestore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro-v6@44659d9/css/all.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="h-full flex flex-col">

    @include('layouts.includes.user-menu')

    @include('layouts.includes.header')

    <div class="container mx-auto px-8 flex-1">
        @yield('content')
    </div>

    @include('layouts.includes.footer')

</body>
</html>
