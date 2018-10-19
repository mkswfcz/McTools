<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title>MC_ADMIN</title>
    {{ this.assets.outputCss() }}
</head>
<style type="text/css">

</style>
<body>
<div class="card">
 {{ dirLink(['AMark':['Login':'#','Register':'#']]) }}

</div>
{{ content() }}
</body>
