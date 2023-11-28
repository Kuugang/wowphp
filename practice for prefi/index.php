<html>
<?php
    include './head.html';
    include("api.php");
    include("header.php");
?>
<body>



<?php
    if(isset($_SESSION['user'])){
        include ("./templates/createPost.html");
    }

    if(array_key_exists('createPost', $_POST)) { 
        createPost();
    }
?>
    <div class="container">
        <?php
            echo getPosts();
            if(array_key_exists('delete-post', $_POST)) { 
                deletePost(); 
            }
            if(array_key_exists('comment-button', $_POST)) { 
                comment(); 
            }
            if(array_key_exists('delete-comment', $_POST)) { 
                deleteComment(); 
            }
        ?>
    </div>


</body>
</html>