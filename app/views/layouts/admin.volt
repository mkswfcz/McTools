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

<body>
<div class="card">
    {{ dirLink(['AMark':['Login':'#','Register':'#'],'Test':['v1':'hello','v2':'world'],'Docs':['doc1':'#','doc2':'#'],'Docs1':['doc1':'#','doc2':'#'],'Docs2':['doc1':'#','doc2':'#'],'Docs3':['doc1':'#','doc2':'#'],
        'Docs4':['doc1':'#','doc2':'#'],'Docs5':['doc1':'#','doc2':'#'],'Docs6':['doc1':'#','doc2':'#']]) }}
</div>

<div class="mc_admin_content">
    <div id="app">
        <p> ${message}</p>
    </div>
    {{ content() }}

</div>
{{ this.assets.outputJs('footer') }}
</body>

</html>