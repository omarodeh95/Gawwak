<DOCTYPE html>
    <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        </head>
        <body>
            <?php 
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);
                session_start();
                echo "<div class='container-fluid shadow-lg p-3 mb-5 bg-body rounded'><h2>welcome to login page</h2></div>";
                include "connection.php";
                function basicvalidate($data){
                    $data=htmlspecialchars($data);
                    $data=stripslashes($data);
                    $data=trim($data);
                    return $data;
                }
                if($_SERVER['REQUEST_METHOD']=="POST"){
                    $email=basicvalidate($_POST['email']);
                    $password = basicvalidate($_POST['password']);
                    $sql="select email,password,firstname from users where '$email' = email limit 1";
                    $result = mysqli_query($conn,$sql);
                if ($result->num_rows>0){
                    $resultt = mysqli_fetch_assoc($result);
                    $emaill=$resultt['email'];
                    $passwordd=$resultt['password'];
                    $name=$resultt['firstname'];
                if ($password === $passwordd){
                    $_SESSION['email']=$email;
                    $_SESSION['name']=$name;
                    header("Location: index.php");
                }
                else {
                    echo "<div class='alert alert-danger' role='alert'><p>email or password is invalid</p></div>";
                }
                }
                else {
                    echo "<div class='alert alert-danger' role='alert'><p>email does not exist</p></div";
                }}
            ?>
            <div class="container">
                <div class="row">
                    <div class="col">
                    </div>
                    <div class="col container-fluid shadow-lg p-3 mb-5 bg-body rounded">
                        <form method = "POST" action = "<?php htmlspecialchars($_SERVER['PHP_SELF']);?>" >
                            <label for="floatingInputInvalid">email</label>
                            <input type="email" class="form-control is-invalid" id="floatingInputInvalid" name= "email" placeholder="name@example.com">
                            <input type="password" class="form-control is-invalid" id="floatingInputInvalid" name= "password" placeholder="password">
                            <input class='btn btn-outline-secondary' type="submit" value="Login">
                        </form>
                        <p style="float:left; padding-right:20px; font-size:20px;">don't have an account?  </p>
                        <button onclick="window.location.href='signup.php'" class='btn btn-outline-secondary' style ="float:left; font-size:15px;">Signup</button> 
                    </div>
                    <div class="col">
                    </div>
                </div>
            </div>

        </body>
    </html>