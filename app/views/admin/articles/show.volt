<div style="float: left;">
<ul style="background-color: #bce8f1; width: 500px; text-align: center;">
    {{ article.title }}
</ul>
<div style="width: 500px;">
    {{ article.content }}
</div>
<br>
<ul style="background-color: #bce8f1; width: 500px; text-align: left;">更新于: {{ date('Y-m-d H:i:s',article.updated_at) }}</ul>
</div>