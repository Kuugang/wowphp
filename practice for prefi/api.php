<?php
session_start([
    'cookie_lifetime' => 86400,
]);

// users JSON
$usersJSON = './users.json';

// posts JSON
$postsJSON = './posts.json';

// comments JSON
$commentsJSON = './comments.json';

function login(){
    if (isset($_POST['username']) && isset($_POST['email'])){ 
        $users = getUsersData();

        $loggedIn = false;
        $current_user;
        foreach($users as $user){
            if($user['username'] == $_POST['username'] && $user['email'] == $_POST['email']){
                $current_user = $user;
                $loggedIn = true;
                break;
            }
        }

        if(!$loggedIn){
            echo "SAYUP IMONG USERNAME OR EMAIL";
        }else{
            $_SESSION['user'] = [
                'id' => $current_user['id'],
                'name' => $current_user['name'],
                'username' => $current_user['username'],
                'email' => $current_user['email'],
                'address' => $current_user['address'],
            ];
            echo "<script>window.location.href = 'index'</script>";
        }
    }
}

function register(){
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];

    if(!$username || !$name || !$email || !$city){
        echo "PLEASE ENTER ALL FIELDS";
    }else{
        $users = getUsersData();
        $count = count($users);
        $id = $users[$count - 1]['id'];

        $data = array(
            "id" => $id + 1,
            "name" => $name,
            "username" => $username,
            "email" => $email,
            "address" => array(
                "street" => $street,
                "barangay" => $barangay,
                "city" => $city,
            )
        );
        
        array_push($users, $data);
        
        $jsonData = json_encode($users, JSON_PRETTY_PRINT);
        file_put_contents('users.json', $jsonData);
        echo "<script>window.location.href = 'index'</script>";
    }
}

function logout(){
    unset($_SESSION['user']);
    echo "<script>window.location.href = 'index'</script>";
}

function deletePost(){
    $posts = getPostsData();
    
    foreach($posts as $key => $post){
        if($post['id'] == $_POST['postid']){
            unset($posts[$key]);
            break;
        } 
    }

    $posts = json_encode($posts, JSON_PRETTY_PRINT);
    file_put_contents('posts.json', $posts);
    echo "<script>window.location.href = 'index'</script>";
}

function comment(){
    $comments = getCommentsData();

    $comment = array(
        'postId' => $_POST['postid'],
        'id' => count($comments) + 1,
        'name' => $_SESSION['user']['name'],
        'email' => $_SESSION['user']['email'],
        'body' => $_POST['comment'],
    );
    
    array_push($comments, $comment);
    $comments = json_encode($comments, JSON_PRETTY_PRINT);
    file_put_contents('comments.json', $comments);
    echo "<script>window.location.href = 'index'</script>";
}

function deleteComment(){
    $comments = getCommentsData();
    
    foreach($comments as $key => $comment){
        if($comment['id'] == $_POST['commentid']){
            unset($comments[$key]);
            break;
        } 
    }

    $comments = json_encode($comments, JSON_PRETTY_PRINT);
    file_put_contents('comments.json', $comments);
    echo "<script>window.location.href = 'index'</script>";
}

function createPost(){
    $data = getPostsData();

    $post = array(
        'uid' => $_SESSION['user']['id'],
        'id' => count($data) + 1,
        'title' => $_POST['post-title'],
        'body' => $_POST['post-body']
    );

    array_push($data, $post);

    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents('posts.json', $jsonData);
    echo "<script>window.location.href = 'index'</script>";
}


// function get users from json
function getUsersData() {
    global $usersJSON;
    if (!file_exists($usersJSON)) {
        echo 1;
        return [];
    }
    
    $data = file_get_contents($usersJSON);
    return json_decode($data, true);
}

// function get posts from json
function getPostsData() {
    global $postsJSON;
    if (!file_exists($postsJSON)) {
        echo 1;
        return [];
    }
    $data = file_get_contents($postsJSON);
    $data = json_decode($data, true);
    // $data = array_reverse($data);
    return $data;
}

// function get comments from json
function getCommentsData() {
    global $commentsJSON;
    if (!file_exists($commentsJSON)) {
        echo 1;
        return [];
    }

    $data = file_get_contents($commentsJSON);
    return json_decode($data, true);
}


function getPosts(){
    $users = getUsersData();
    $posts = getPostsData();
    $posts = array_reverse($posts);
    $comments = getCommentsData();
    $postarr = array(); 

    foreach($posts as $post){
        foreach($users as $user){
            if($user['id'] == $post['uid']){
                $post['uid'] = $user;
                 
                break;
            }
        }
        $post['comments'] = array();
        foreach($comments as $comment){
            if($post['id']==$comment['postId']){
                $post['comments'][] = $comment;
            }
        }
        $postarr[] = $post;
    }
    $str = "";
    foreach ($postarr as $parr) {
        $str .= '
            <div class="row relative rounded border border-1ggg border-blue-500 w-[70%] mx-auto p-10 gap-5 mb-5">
                <div class = "mb-5">
                    <div class = "flex flex-row items-center gap-4">
                        <img src="https://ui-avatars.com/api/?rounded=true&name=' . $parr['uid']['name'] . '" alt="user" class="profile-photo-md pull-left">

                        <a href="timeline.html" class="font-bold">' . $parr['uid']['name'] . '</a>
                    </div>';

                if(isset($_SESSION['user'])){

                if($parr['uid']['id'] == $_SESSION['user']['id']){
                    $str .= '
                    <form method="POST">
                        <input type="hidden" name="postid" value = "' . $parr['id'] . '"</input>
                        <button name="delete-post" class="absolute top-5 right-5"><i class="fa-solid fa-trash"></i></button>
                    </form>';
                }
                }
        
                $str.='
                    <div class="post-detail">
                        <div class="post-text">
                            <h3>' . $parr['title'] . '</h3>
                            <p>' . $parr['body'] . '</p>
                        </div>
                        <div class="reaction">
                            <a class="btn text-green"><i class="fa fa-thumbs-up"></i> 13</a>
                            <a class="btn text-red"><i class="fa fa-thumbs-down"></i> 0</a>
                        </div>
                    </div>
                </div>
                    ';

                if(isset($_SESSION['user'])){
                    $str.='
                        <div class = "relative">
                            <form method = "POST">
                                <input type="hidden" name = "postid" value = "' .$parr['id'] . '"</input>
                                <textarea
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 min-h-[50px] max-h-[100px] resize-y"
                                    name = "comment"
                                    rows = "2"
                                    placeholder="Comment as ' . $_SESSION['user']['username'] .'"
                                    required
                                ></textarea> 
                                <button class = "px-2 py-1 bg-blue-500 border rounded-md border-white text-white absolute right-0 mt-2" name = "comment-button" type = "submit">Comment</button>
                            </form>
                        </div>
                    ';
                }
            
            if(count($parr['comments']) > 0){
                $str.= '<div class="mt-16 max-h-[165px] overflow-y-auto">';
            }

            foreach ($parr['comments'] as $comm) {
                $str .= '
                <div class="px-5 mb-2 relative ">
                    <div class = "flex flex-row items-center gap-2 mb-2">
                        <img src="https://ui-avatars.com/api/?rounded=true&name=' . $comm['name'] . '" alt="" class="profile-photo-sm">
                        <h1 class = "font-bold">' .$comm["name"]. '</h1>
                    </div>
                    <p>' . $comm['body'] . '</p>';
                
                if(isset($_SESSION['user']) && $_SESSION['user']['name'] == $comm['name'] && $_SESSION['user']['email'] == $comm['email']){
                    $str.= '
                    <form method="post">
                        <input type="hidden" name="commentid" value="' . $comm['id'] . '" />
                        <button name="delete-comment" class="absolute top-5 right-5"><i class="fa-solid fa-trash"></i></button>
                    </form>
                ';
            }

            $str.= '
                </div>';
            }

            $str .= '
                    </div>
                </div>
            </div>';
    }
    return $str;
}
?>