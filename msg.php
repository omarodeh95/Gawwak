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
        <body style="height:100%; width:100%;">
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
                $_SESSION['friendname']=ucfirst($_POST['friendname']);
                $_SESSION['friendemail']=$_POST['friendemail'];
                }
                

                
                if (isset($_POST['send'])){
                    $email=$_SESSION['email'];
                    $friend=$_SESSION['friendemail'];
                    $txt=$_POST['send'];
                    $date = Date('Y-m-d H:i:s');
                    if(!empty($txt)){
                    basicvalidate($txt);
                    $sql="insert into messages(source,target,msg,msg_date) values ('$email','$friend','$txt','$date')";
                    mysqli_query($conn,$sql);
                    }
                }
                //checking login from included files
                checklogin($conn);
                $name = $_SESSION['name'];
            ?>
            <div class='container-fluid shadow-lg p-3 mb-5 bg-body rounded' style="height:10%">
                <div class='row '>
                    <h3> Welcome to The Project <?php echo $name;?>                
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
                    <button onclick="window.location.href='index.php'" class='btn btn-outline-secondary' style ="float:right;">Home</button>
                    </h3>                     
                </div>
            </div>

            <div class="row" style="height:90%;">
                <div class ='col-2 shadow-lg p-3 mb-5 bg-body rounded' style="height:100% overflow:'scroll'" >
                <?php
                    $email = $_SESSION['email'];
                    $sql="select email, firstname, lastname,status from users inner join (select source as friend from friend_int where target = '$email' union select target from friend_int where source ='$email') as friends on friends.friend=users.email order by status desc";
                    $result = mysqli_query($conn,$sql);
                    if(!mysqli_num_rows($result)>0){
                        echo "<h3> You have no friends </h3>";
                        
                    }else{
                        while ($row=mysqli_fetch_assoc($result)){
                            
                            echo "<form action='msg.php' method='POST'><input type='text' style ='display:none' name='friendemail' value=".$row['email']."></input><input type='text' style ='display:none' name='friendname' value=".$row['firstname']."></input> <button class ='btn btn-outline-secondary' style='width:100%' type='submit' value='submit'><h3 style='text-align:left;'>".ucfirst($row['firstname'])." ".ucfirst($row['lastname'])."</h3> <h6 style='text-align:right;'>".$row['status']."</h6></button></form>";
                            if(!isset($_SESSION['friendemail'])){                                
                                $_SESSION['friendname']=ucfirst($row['firstname']);
                                $_SESSION['friendemail']=$row['email'];
                            }   
                        }
                    }
                    ?>
                </div>
                <div class ='col-10 shadow-lg p-3 mb-5 bg-body rounded' style = "height: 100%">
                    <div class='row' style="height:10%">                    
                    <h3> <?= $_SESSION['friendname']?> </h3>
                    </div>                    
                    <div class='row overflow-scroll' style="height:60%;">
                    <?php 
                    $friend_email= $_SESSION['friendemail'];
                    $sql="select msg, source, target, msg_date from messages where source = '$email' and target= '$friend_email' union select msg, source ,target,msg_date from messages where target='$email' and source = '$friend_email' order by msg_date";
                    $result = mysqli_query($conn,$sql);
                    if(mysqli_num_rows($result)>0){
                        while ($row=mysqli_fetch_assoc($result)){
                            echo "<div class='row'>";
                            if ($row['source']==$email){
                                echo "<div class='col-sm-9 bg-light shadow-lg p-3 border' style='height:auto; border-radius: 25px;'><p style='vertical-align:middle; word-wrap:break-word;'>".$row['msg']."</p></div>";
                            } else {
                                echo "<div class='col-sm-3' style='height:auto; border-radius: 25px;'></div>"."<div class='col-sm-9 shadow-lg bg-secondary p-3 border' style='height:auto;  border-radius: 25px;'><p style=' color:white; word-wrap:break-word;  text-align:right; vertical-align:middle;'>".$row['msg']."</p></div>";
                            }
                            echo "</div>";                            
                        }
                    }
                    else {
                        echo "<h2> No messages yet</h2>";
                    }
                    ?>                    
                    </div>                    
                    <div class='row' style="height:30%">
                    <form action="msg.php" method="POST" name = "send">
                    <div class=" row form-floating mb-3">
                      <input name ='send' type="textarea" class="form-control" id="floatingInput" placeholder="Write a message.." style="height:90%">
                      <label for="floatingInput">Write a message</label>
                    </div>
                    <div>
                    <button style="float:right; width:15%;" type="submit" class="btn btn-outline-secondary">Send</button>    
                    </div>                    
                    </form>
                    </div>
                </div>
            </div>
        </body>
    </html>
    <?php 
    echo $_SESSION['email'].$_SESSION['friendname'].$_SESSION['friendemail'];
    
    ?>