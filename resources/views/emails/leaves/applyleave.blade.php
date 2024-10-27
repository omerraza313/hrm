<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    {{ $user->first_name }} {{ $user->last_name }} Applied for Leave
    <a href="{{ route('admin.dashboard') }}">Visit Dashboard</a>
</body>

</html>
