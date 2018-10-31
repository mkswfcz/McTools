<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MC_ADMIN</title>
    {{ this.assets.outputJs() }}
    {{ this.assets.outputCss() }}
</head>

<body style="background-color: whitesmoke">
<div class="card">
    {% if default != true %}
        {{ dirLink(['AMark':['Login':'/admin/administrators','ajax_link_Logout':'myModal']]) }}
    {% endif %}
</div>
{{ confirm('','退出登录?','/admin/administrators/logout','myModal') }}
<div class="mc_admin_content">
    {{ content() }}
</div>
{{ this.assets.outputJs('footer') }}
</body>

</html>