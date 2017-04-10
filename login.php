<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Test</title>
  <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>

  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
  <![endif]-->
</head>

<body>

<p>
    <button id="login">Login</button>
    <button id="logout">Logout</button>
    <button id="getUsers">Get Users</button>
</p>
<p>
    <span id="jwt" style="width: 500px; word-wrap: break-word; display: inline-block; text-align: top;"></span>
    <span id="jwt-decoded" style="width: 500px; word-wrap: break-word; display: inline-block; text-align: top;"></span>
</p>

<script>
(function($){

    function parseJwt (token) {
        if(token == null || token == 'null' || !token || token == false){ return false; }
        var base64Url = token.split('.')[1];
        var base64 = base64Url.replace('-', '+').replace('_', '/');
        return JSON.parse(window.atob(base64));
    }

    function update(token){
        $('#jwt').text(token);
        $('#jwt-decoded').html(JSON.stringify(parseJwt(token)));
    }

    var headers = {}
    var token = localStorage.getItem('token');
    
    if(token){
        headers.Authorization = 'Bearer ' + token;
        update(token);
        // $.ajax({
        //     type: 'get',
        //     url: 'http://localhost/endpoint-api/user/2',
        //     headers: headers,
        //     success: function(data){
        //         console.log(data);
        //     }
        // })
    }

    $('#login').click(function(evt){
        $.ajax({
            type: 'post',
            url: 'http://localhost/endpoint-api/auth',
            headers: headers,
            data: {
                email: 'mikematos84@gmail.com',
                password: 'open(this)'
            },
            success: function(data){
                localStorage.setItem('token', data.token);
                update(data.token);
                headers.Authorization = 'Bearer ' + data.token;
            }
        })
    });

    $('#logout').click(function(evt){
        var token = localStorage.getItem('token');
        if(token){
            localStorage.removeItem('token');
            token = localStorage.getItem('token');
            update(token);
            delete headers.Authorization;
        }
    });

    $('#getUsers').click(function(evt){
        $.ajax({
            type: 'get',
            url: 'http://localhost/endpoint-api/users',
            headers: headers,
            success: function(data){
                console.log(data);
            }
        })
    });

})(jQuery);
</script>

</body>
</html>