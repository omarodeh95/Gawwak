<DOCTYPE html>
    <html>
        <head>
            <title>The Project</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
            <script>
                function isfriend(email){
                    document.getElementById(email).innerHTML="Friends";
                    document.getElementById(email).type="button";
                }
                function isfriendd(email){
                    document.getElementById(email).innerHTML="Check Friend requests";
                    document.getElementById(email).type="button";
                }
            </script>
        </head>
        <body>
            <?php
                //Error checking
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);
                session_start();
                include "connection.php";
                include "functions.php";
                //Friend adding request handling
                if (isset( $_POST['addfriend'])){
                    $source = $_SESSION['email'];
                    $target =  $_POST['addfriend'];
                    $sql="INSERT INTO friend_requests (source,target) values('$source','$target')";
                    $sqlcheck="select source, target from friend_requests where source='$target' AND target='$source' union select source, target from friend_int where source = '$source' AND target = '$target' union select source,target from friend_int where source = '$target' AND target='$source';";
                    $result=mysqli_query($conn,$sqlcheck);
                if(mysqli_num_rows($result)==0)
                    mysqli_query($conn,$sql);
                    header("Location: index.php");
                }
                //Friend request accept
                if (isset( $_POST['accept'])){
                    $email=$_SESSION['email'];
                    $source= $_POST['accept'];
                    $sql= "insert into friend_int (source,target) values ('$source','$email');";
                    mysqli_query($conn,$sql);
                    $sql="delete from friend_requests where source='$source' AND target = '$email';";
                    mysqli_query($conn,$sql);
                    header("Location: index.php");
                }
                //Friend deletion 
                if (isset( $_POST['delete'])){
                    $email=$_SESSION['email'];
                    $source= $_POST['delete'];
                    $sql = "delete from friend_int where (source ='$source' AND target= '$email') OR (target = '$source' AND source= '$email');";
                    mysqli_query($conn,$sql);
                    header("Location: index.php");
                }
                //Friend request rejection
                if (isset( $_POST['reject'])){
                    $email=$_SESSION['email'];
                    $source= $_POST['reject'];
                    $sql = "delete from friend_requests where source = '$source' AND target='$email'";
                    mysqli_query($conn,$sql);
                    header("Location: index.php");
                }
                //friend request count query
                function requests($conn,$email){
                    $sql="select count(target) as requests from friend_requests where target = '$email'";
                    $result = mysqli_query($conn,$sql);
                    $req = mysqli_fetch_assoc($result);
                    return $req['requests'];
                }
                if (isset($_POST['friendemail'])){
                    $_SESSION['friendemail']=$_POST['friendemail'];
                    $_SESSION['friendname']=ucfirst($_POST['friendname']);
                    header("Location: msg.php");
                }

                //checking login from included files
                checklogin($conn);
                $name = $_SESSION['name'];
            ?>
            <div class='container-fluid shadow-lg p-3 mb-5 bg-body rounded'>
                <div class='row '>
                    <h3> Gawwak <?php echo $name;?>                
                    <button onclick="window.location.href='logout.php'" class='btn btn-outline-secondary' style ="float:right;">Logout</button>
                    <!-- friend requests function -->
                        <?php
                            $email = $_SESSION['email'];
                            $sql="select source from friend_requests where target = '$email';";
                            $result = mysqli_query($conn,$sql);
                            
                                echo "<div style = 'float:right;'   class='dropdown'><button class='btn btn-secondary dropdown-toggle' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'> Friend requests <span class='badge bg-secondary'>".requests($conn,$_SESSION['email'])."</span> </button> <ul class='dropdown-menu' aria-labelledby='dropdownMeneButton1'>";
                                while ($row = mysqli_fetch_assoc($result)){
                                echo "<li>";
                                echo "<div style ='float:left;'><p>".$row['source']."</p></div>";
                                echo "<div style ='float:left;'><form method='POST' action ='index.php'> <button class='btn btn-outline-secondary' type = 'submit' name='accept' value=".$row["source"].">Accept</button> </form></div>";
                                echo "<div style ='float:left;'><form method='POST' action ='index.php'> <button class='btn btn-outline-secondary' type = 'submit' name='reject' value=".$row["source"].">Reject</button> </form></div>";
                                echo "</li></ul></div>";
                                
                            }
                        ?>                        
                    </div>
                    <button onclick="window.location.href='msg.php'" class='btn btn-outline-secondary' style ="float:right;">Messages</button>
                    </h3>                     
                </div>
            </div>
            <?php
                $email = $_SESSION['email'];
                $sql = "select email,firstname, lastname,reg_date,status from users where NOT '$email' = email";
                $result = mysqli_query($conn,$sql);
                if (mysqli_num_rows($result)==0){
                    echo "<div class='container-fluid shadow-lg p-3 mb-5 bg-body rounded' ><p class='alert alert-dark' role='alert'>There are no other users in the project yet!</p></div>";}
                else{
                    echo "<table class='table shadow-lg p-3 mb-5 bg-body rounded  table-dark table-striped'>";
                    echo "<tr><td> Name </td> <td>Email</td> <td>Status</td><td>Friendship</td></tr>";
                    while ($row = mysqli_fetch_assoc($result)){
                        echo "<tr><td>".$row["firstname"]." ".$row['lastname']."</td><td>".$row["email"]."</td><td>".$row['status']."</td><td>";
                        echo "<form method='POST' action ='index.php' ><button id=".$row['email']." class='btn btn-outline-secondary' type = 'submit' name='addfriend' value=".$row["email"].">Add friend</button></form>";
                    }
                    echo "</table>";
                }
            ?>
            <h2> Friends list</h2>
            <?php
                $email = $_SESSION['email'];
                $sql = "select email, firstname, lastname from users inner join (select target as friend from friend_int where source = '$email' union select source as friend from friend_int where target = '$email') as friends on users.email=friends.friend;";
                $result = mysqli_query($conn,$sql);
                if(mysqli_num_rows($result)>0){
                    echo "<div class = 'row shadow-lg p-3 mb-5 bg-body rounded'>";
                    while ($row=mysqli_fetch_assoc($result)){
                        echo "<div  class ='col-4 shadow-lg p-3 mb-5 bg-body rounded' style ='float:left;'><p >".ucfirst($row['firstname'])." ".ucfirst($row['lastname'])."</p></div>";
                        echo "<div class ='col shadow-lg p-3 mb-5 bg-body rounded' style ='float:left;'><form method='POST' action ='index.php'> <button class='btn btn-outline-secondary' type = 'submit' name='delete' value=".$row['email'].">Delete</button> </form></td>";
                        echo "<form method='POST' action ='index.php'> <button  class='btn btn-outline-secondary' type = 'submit' name='friendemail' value=".$row['email'].">Message</button> <input type='text' name='friendname' style='display:none;' value=".$row['firstname']."></input></form></td></tr></div>";
                        echo "<script type = \"text/javascript\"> isfriend(\"".$row['email']."\");</script>";
                        
                    }
                    echo "</div>";
                    } else {
                        echo "<div class='shadow-lg p-3 mb-5 bg-body rounded'><p class='alert alert-dark' >No friends yet!</p></div>";
                    }
            ?>
            <?php
                //adding code to update users list to accepting or rejecting
                            $email = $_SESSION['email'];
                            $sql="select source from friend_requests where target = '$email';";
                            $result = mysqli_query($conn,$sql);
                                while ($row = mysqli_fetch_assoc($result)){
                                echo "<script type = \"text/javascript\"> isfriendd(\"".$row['source']."\");</script>";
                                                                
                            }
                            ?>
            
            <br>
        </body>
    </html>
