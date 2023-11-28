<header class="relative border border-b flex flex-row justify-between items-center h-[70px]">
    <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0 flex flex-row">
        <li><a href="index.php" class="nav-link px-2 text-secondary">Home</a></li>
        <li><a href="#" class="nav-link px-2 text-">Posts</a></li>
    </ul>

    <div class="flex flex-row place-items-center">
        <?php 
        if (empty($_SESSION['user'])) {
            include("./templates/login.html");
            include("./templates/register.html");
        }else{
            echo '
                <a href = "https://www.youtube.com/watch?v=dQw4w9WgXcQ" class = "border rounded-full bg-blue-500 p-3 text-2xl"><i class="fa-solid fa-user text-white"></i></a>
            ';
            include("./templates/logout.html");
        }
        ?>
    </div>
</header>


<?php
    if(array_key_exists('register', $_POST)) { 
        register(); 
    } 
    if(array_key_exists('login', $_POST)) { 
        login(); 
    }
    if(array_key_exists('logout', $_POST)) { 
        logout();
    }
?>
