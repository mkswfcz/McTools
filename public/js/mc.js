var demo = new Vue({
    el: '#demo',
    data: {
        show: true
    }
})

var app = new Vue({
    delimiters:['${','}'],
    el: '#app',
    data: {
        message: 'Hello Vue.js!'
    }
})