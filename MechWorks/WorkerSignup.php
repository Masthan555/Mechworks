<!-- Masthan Swamy -->
<?php include "header.php" ?>
<title>Registration As A Worker.</title>

<?php
    function testInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return($data);
    }
    function checkPassword($password)
    {
        $len = strlen($password);
        $check = true;
        $counter1 = $counter2 = $counter3 = 0;

        for($i=0;$i<$len;$i++)
        {
            if (preg_match("/^(?=.*[a-z])$/", $password[$i])) {
                $counter1 += 1;
            }
            if (preg_match("/^(?=.*[A-Z])$/", $password[$i])) {
                $counter2 += 1;
            }
            if (preg_match("/^(?=.*\d)$/", $password[$i])) {
                $counter3 += 1;
            }
        }
        if($counter1==$len || $counter2==$len || $counter3==$len)
        {
            $check = false;
        }
        if($len<8)
        {
            $check = false;
        }
        return($check);
    }

    $nameErr=$ageErr=$mobileErr=$emailErr=$passwordErr=$wcPasswordErr=$wImage="";
    $name = $age = $mobile = $email = $password = $wcPassword = $wImageErr = "";
    $wid="";
    $fext="";
    $check = true;

    if($_SERVER['REQUEST_METHOD']=="POST")
    {
        if(empty($_POST['uname']))
        {
            $nameErr = "Enter Your Name.";
            $check = false;
        }
        else
        {
            $name = testInput($_POST['uname']);
        }

        if(empty($_POST['uage']) || $_POST['uage']<=5)
        {
            $ageErr = "Please Enter Your Present Age.";
            $check = false;
        }
        else
        {
            $age = testInput($_POST['uage']);
        }

        if(empty($_POST['umobile']) || !preg_match("/^[0-9]{10}$/",$_POST['umobile']))
        {
            $mobileErr = "Kindly Enter 10 Digit Mobile Number.";
            $check = false;
        }
        else
        {
            $mobile = testInput($_POST['umobile']);
        }

        if(empty($_POST['uemail']))
        {
            $emailErr = "Please Enter Your Email.";
            $check = false;
        }
        else
        {
            $email = testInput($_POST['uemail']);
            if(!filter_var($email,FILTER_VALIDATE_EMAIL))
            {
                $emailErr = "Enter Valid Email.";
                $check = false;
            }
        }

        if(empty($_POST['upassword']) || !checkPassword($_POST['upassword']))
        {
            $passwordErr = "Please Enter Strong Password.(8 Chars,1 Capital,1 Small,1 Number).";
            $check = false;
        }
        else
        {
            $password = ($_POST['upassword']);
        }

        if(empty($_POST['wcpasswd'])||!($password == $_POST['wcpasswd']))
        {
            $wcPasswordErr = "Password And Confirm Password Doesn't Match.";
            $check = false;
        }
        else
        {
            $wcPassword = $password;
        }
        if(isset($_FILES['wImage']))
        {
            $fname = $_FILES['wImage']['name'];
            $fsize = $_FILES['wImage']['size'];
            $ftemp = $_FILES['wImage']['tmp_name'];
            $ftype = $_FILES['wImage']['type'];
            $fext = strtolower(pathinfo($fname,PATHINFO_EXTENSION));
            $exts = array("jpeg","jpg","png");

            if(!($fext=="jpeg" || $fext=="jpg" || $fext=="png" || $fext=="jfif" || $fext=="gif"))
            {
                $wImageErr = "Sorry (jpg,jpeg,png,jfif,gif) Are Supported.";
                $check=false;
            }
            if($fsize>(9*1000*1000))
            {
                $wImageErr = "Sorry File Size Is Too Large.";
                $check=false;
            }
        }
        if(!(isset($_FILES['wImage'])))
        {
            $wImageErr="Please Upload Your Good Photo For Users To Continue.";
            $check=false;
        }

        if($check==true)
        {
            $host = "localhost:3306";
            $uname = "scott";
            $passwd = "Masthan555!";
            $dbname = "Mechworks";

            // Creating Connection.
            $conn = new mysqli($host,$uname,$passwd,$dbname);
            // Checking Connection
            if($conn->connect_error)
            {
                die("Database Connection Failed : ".$conn->connect_error);
            }

            $stmt = $conn->prepare("insert into Worker(name,age,mobile,email,password) values(?,?,?,?,?)");
            $stmt->bind_param("sisss",$name,$age,$mobile,$email,$password);

            if($stmt->execute()===TRUE)
            {
                $sql = "select workerid from Worker where email='".$email."'";
                $res = $conn->query($sql);
                if($res->num_rows>0)
                {
                    $row = $res->fetch_assoc();
                    $wid = $row['workerid'];

                    move_uploaded_file($ftemp,"Images/".($wid.".".$fext));

                    session_start();
                    $_SESSION['wid'] = $wid;
                    unset($_SESSION['uid']);

                    $conn->close();
                    // Redirecting Worker To Next Page
                    header('Location: TakeAddress.php?role=worker');
                }
            }
            else
            {
                $emailErr = "This Email Already Exists, Please <a href='WorkerLogin.php'>Login Here</a>.";
            }

        }

    }
?>

<body>
<div class="container-fluid">
    <div id="formContainer">
        <form action="<?php echo(htmlspecialchars($_SERVER['PHP_SELF'])); ?>" class="needs-validation" method="post" id="workerSignupForm" enctype="multipart/form-data" novalidate>
            <h4 class="taskName">Create Worker Account</h4>

            <div class="form-group">
                <label for="wname">Name : </label>
                <input type="text" id="wname" class="form-control" name="uname" value="<?php echo($name); ?>" placeholder="Enter Your Full Name..." required/>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($nameErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Kindly Enter Your Name.</div>
            </div>
            <div class="form-group">
                <label for="wage">Age : </label>
                <input type="number" id="wage" class="form-control" name="uage" placeholder="Select Your Age..." value="<?php echo($age); ?>" required />
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($ageErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Please Select Your Present Age.</div>
            </div>
            <div class="form-group">
                <label for="wmobile">Mobile : </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">+91</span>
                    </div>
                    <input type="text" pattern="[0-9]{10}" value="<?php echo($mobile); ?>" class="form-control" placeholder="Mobile..." id="wmobile" name="umobile" required/>
                    <div class="manual-invalid-feedback serverErrorStyle"><?php echo($mobileErr); ?></div>
                    <div class="valid-feedback">Validated.</div>
                    <div class="invalid-feedback">Enter Valid 10 digit Mobile Number.</div>
                </div>
            </div>
            <div class="form-group">
                <label for="wemail">Email : </label>
                <input type="email" id="wemail" class="form-control" name="uemail" value="<?php echo($email); ?>" placeholder="Enter Your Email..." required/>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($emailErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Please Enter Valid Email Address.</div>
            </div>
            <!--
            <div class="form-group">
                <label for="waddress">Address : </label>
                <div class="input-group">
                    <input type="text" id="waddress" class="form-control" name="uaddress" value="<?php  ?>" placeholder="Start Typing Your Address..."/>
                    <div class="input-group-append">
                        <button class="btn btn-secondary" style="border-radius : 0 5px 5px 0" type="button" onclick="javascript:void(0)" id="searchAddress"><i class="fas fa-location-arrow" ></i>  Locate</button>
                    </div>
                    <div class="valid-feedback">Validated.</div>
                    <div class="invalid-feedback">Please Enter Your Address.</div>
                </div>
            </div>
            -->
            <div class="form-group">
                <label for="wImage">Select Your Good Photo</label>
                <input type="file" id="wImage" name="wImage" datatype="image" accept="image/*" class="form-control-file border" required/>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($wImageErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Please Upload Your Good Photo For Users To Continue.</div>
            </div>
            <div class="form-group">
                <label for="wpasswd">Password : </label>
                <input type="password" id="wpasswd" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" value="<?php echo($password); ?>" class="form-control" name="upassword" placeholder="Enter Your Password..." required/>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($passwordErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Please Choose String Password <b>Check Below Tips</b>.</div>
            </div>
            <div class="form-group">
                <label for="wcpasswd">Confirm Password : </label>
                <input type="password" id="wcpasswd" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="form-control" value="<?php echo($wcPassword); ?>" name="wcpasswd" placeholder="Re-Enter Your Password..." required/>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($wcPasswordErr); ?></div>
                <div class="manual-valid-feedback">Validated.</div>
                <div class="manual-invalid-feedback">Confirm Password Doesn't Match With Original Password.</div>
            </div>

            <ul id="passwordTips">
                <li class="text-info">Password Must Contain A Combination Of : <b>SmallLetters</b>,<b>Capital Letters</b> and <b>Numbers</b>.</li>
                <li class="text-info">Password Should Be At least 8 Letters Long.</li>
            </ul>
            <h6 class="text-danger mt-5">By Creating Account, You are agreeing to our <a href="javascript:void(0)" >Terms And Conditions</a>.</h6>
<!--
            <button id="workersignupbtn" type="submit" class="submitBtn" >Continue <i class='fas fa-long-arrow-alt-right'></i></button>
-->
            <input type="submit" class="submitBtn" value="Create Account" />

            <h6 class="mt-5">Already, Have A Worker Account.Please <a href="WorkerLogin.php" >Login Here</a>.</h6>
            <a href="javascript:void(0)" class="googleBtn mb-3">
                <i class="fab fa-google"></i> Signup With Google
            </a>

        </form>
    </div>
</div>
</body>