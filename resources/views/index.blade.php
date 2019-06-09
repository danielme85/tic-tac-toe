<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Tic-Tac-Toe</title>
    <meta name="description" content="Tic-Tac-Toe">
    <meta name="author" content="Daniel Mellum">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="css/app.css">

</head>
<body>
<div id="app" class="container">
    <div class="col-12">
        <tic-tac-toe></tic-tac-toe>
    </div>
</div>

<script src="js/app.js"></script>
</body>
</html>