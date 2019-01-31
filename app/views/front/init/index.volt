<!DOCTYPE html>
<html>
<head> 
    <meta charset="utf-8">
     
    <title>API</title> 
    <script src="js/jquery.min.js"></script>
    <style>
        #main {
            margin: 5% 5% 5px 5%;
            height: 600px;
            border: 1px solid;
            text-align: center;
            position: relative;
        }

        #api_from {
            margin: 10% 10% 10% 10%;
        }

        input#domain {
            width: 200px;
            height: 20px;
        }

        select {
            width: auto;
            padding: 0 2%;
            margin: 0;
        }

        option {
            text-align: center;
        }

        #out_line_res {
            position: absolute;
            width: 100%;
            height: 300px;
            bottom: 0;
            border: 1px solid;
        }

    </style>
</head>
<body>
<div id ='main'>
<div id="api_test">
    <form id="api_from" action="" method="post" enctype="text/plain">
        域名<input id="domain" type="text" name="url">
        <select id="request_method">
            <option value="post">POST</option>
            <option value="get">GET</option>
        </select><br><br>
        <button id="curl_request">测试</button>
    </form>
</div>
<div id="out_line_res">
    {#<div id="in_line_res">#}
    {#</div>#}
</div>
</div>
</body>
<script>
    $("#domain").bind("input propertychange", function (event) {
        var url = $("#domain").val();
        var method = $("#request_method option:selected").val();
        $("#api_from").attr("action", url);
        $("#api_from").attr("method", method);
    });
</script>
</html>