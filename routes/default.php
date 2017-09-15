<?php 
        
$router->get('/', function () {
    echo '<h1>bramus/router</h1><p>Try these routes:<p><ul><li>/hello/<em>name</em></li><li>/blog</li><li>/blog/<em>year</em></li><li>/blog/<em>year</em>/<em>month</em></li><li>/blog/<em>year</em>/<em>month</em>/<em>day</em></li><li>/movies</li><li>/movies/<em>id</em></li></ul>';
});

$router->post('/auth', function(){
    $req = getPostData();
    
    global $db;
    $stmt = $db->prepare('SELECT * FROM users WHERE login_id=? AND password=?');
    $row = $db->getRow($stmt, [$req['login_id'], $req['password']]);
    if($row){
        $token = Token::create([
            'uid' => $row['id']
        ]);
        $stmt = $db->prepare('UPDATE users SET token=? WHERE id=?');
        $update = $db->execute($stmt, [$token, $row['id']]);
        Response::json(['token' => $token]);
        return;
    }
    Response::json(['error' => 'User not found']);
});

$router->get('/users', function(){
    try{
        Token::verify();
        global $db;
        $stmt = $db->prepare('SELECT * FROM users');
        $rows = $db->getAll($stmt);
        Response::json($rows);
    }catch(\Exception $e){
        Response::json($e->getMessage());
    }
    
});

$router->get('/user/(\d+)', function($uid){
    try{
        Token::verify();
        global $db;
        $stmt = $db->prepare('SELECT * FROM users WHERE id=?');
        $row = $db->getRow($stmt, [$uid]);
        Response::json($row);
    }catch(\Exception $e){
        Response::json($e->getMessage());
    }
});

