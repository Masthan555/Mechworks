<!-- Masthan Swamy -->

<?php include "header.php" ?>
<title>Registration As A User.</title>

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

    $nameErr=$emailErr=$passwordErr=$cPasswordErr="";
    $name = $email = $password = $cPassword = "";
    $uid="";
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

        if(empty($_POST['cpasswd'])||!($password == $_POST['cpasswd']))
        {
            $cPasswordErr = "Password And Confirm Password Doesn't Match.";
            $check = false;
        }
        else
        {
            $cPassword= ($password);
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

            $stmt = $conn->prepare("insert into User(name,email,password) values(?,?,?)");
            $stmt->bind_param("sss",$name,$email,$password);

            if($stmt->execute()===TRUE)
            {
                $sql = "select userid from User where email='".$email."'";
                $res = $conn->query($sql);
                if($res->num_rows>0)
                {
                    $row = $res->fetch_assoc();
                    $uid = $row['userid'];

                    session_start();
                    $_SESSION['uid'] = $uid;
                    unset($_SESSION['wid']);

                    header("Location: TakeAddress.php?role=user");

                }
            }
            else
            {
                $emailErr = "This Email Already Exists, Please <a href='UserLogin.php'>Login Here</a>.";
            }

            $conn->close();
        }

    }

?>

<body>
    <div class="container-fluid">
        <div id="formContainer">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" method="post" id="signupForm" novalidate>
                <h4 class="taskName">Create User Account</h4>

                <div class="form-group">
                    <label for="name">Name : </label>
                    <input type="text" id="name" class="form-control" name="uname" placeholder="Enter Your Full Name..." value="<?php echo($name); ?>" required/>
                    <div class="manual-invalid-feedback serverErrorStyle"><?php echo($nameErr); ?></div>
                    <div class="valid-feedback">Validated.</div>
                    <div class="invalid-feedback">Please Enter Name Correctly.</div>
                </div>
                <div class="form-group">
                    <label for="email">Email : </label>
                    <input type="email" id="email" class="form-control" name="uemail" value="<?php echo($email); ?>" placeholder="Enter Your Email Address..." required/>
                    <div class="manual-invalid-feedback serverErrorStyle"><?php echo($emailErr); ?></div>
                    <div class="valid-feedback">Validated.</div>
                    <div class="invalid-feedback">Please Enter Valid Email Address.</div>
                </div>
                <div class="form-group">
                    <label for="passwd">Password : </label>
                    <input type="password" id="passwd" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" value="<?php echo($password); ?>" class="form-control" name="upassword" placeholder="Enter Your Password..." required/>
                    <div class="manual-invalid-feedback serverErrorStyle"><?php echo($passwordErr); ?></div>
                    <div class="valid-feedback">Validated.</div>
                    <div class="invalid-feedback">Please Choose String Password <b>Check Below Tips</b>.</div>
                </div>
                <div class="form-group">
                    <label for="cpasswd">Confirm Password : </label>
                    <input type="password" id="cpasswd" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="form-control" value="<?php echo($cPassword); ?>" name="cpasswd" placeholder="Re-Enter Your Password..." required/>
                    <div class="manual-invalid-feedback serverErrorStyle"><?php echo($cPasswordErr); ?></div>
                    <div class="manual-valid-feedback">Validated.</div>
                    <div class="manual-invalid-feedback">Confirm Password Doesn't Match With Original Password.</div>
                </div>

                <ul id="passwordTips">
                    <li class="text-info">Password Must Contain A Combination Of : <b>SmallLetters</b>,<b>Capital Letters</b> and <b>Numbers</b>.</li>
                    <li class="text-info">Password Should Be At least 8 Letters Long.</li>
                </ul>
                <h6 class="text-danger mt-4">By Creating Account, You are agreeing to our <a href="javascript:void(0)">Terms And Conditions</a>.</h6>

                <input type="submit" class="submitBtn" value="Create Account" />

                <h6>Already, Have An Account.Please <a href="UserLogin.php">Login</a>.</h6>
                <a href="javascript:void(0)" class="googleBtn mb-3">
                    <i class="fab fa-google"></i> Signup With Google
                </a>

            </form>
        </div>
    </div>
</body>