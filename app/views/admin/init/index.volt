<script>
    function bigger(img) {
        localStorage.img_width = img.width;
        localStorage.img_height = img.height;
        img.width = 150;
        img.height = 150;
    }

    function reset(img) {
        img.width = localStorage.img_width;
        img.height = localStorage.img_height;
    }
</script>
{{ welcome }}

<br><img onmouseover="bigger(this)" onmouseleave="reset(this)" src={{ code }}>

