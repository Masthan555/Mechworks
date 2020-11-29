<!-- Masthan Swamy -->
<?php include "header.php"; ?>
<title>Login As User.</title>

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
// Validating Input
$email=$password="";
$emailErr=$passwordErr="";
$uid="";
$check=true;

if($_SERVER['REQUEST_METHOD']=="POST")
{
    if(empty($_POST['uemail']))
    {
        $emailErr = "Please Enter Your Registered Email.";
        $check = false;
    }
    else
    {
        $email = testInput($_POST['uemail']);
        if(!filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            $emailErr = "Please Enter Valid Email.";
            $check = false;
        }
    }

    if(empty($_POST['upassword']) || !checkPassword($_POST['upassword']))
    {
        $passwordErr = "Please Enter Your Registered Password.";
        $check = false;
    }
    else
    {
        $password = ($_POST['upassword']);
    }

    if($check == true)
    {
        $host="localhost:3306";
        $uname = "scott";
        $passwd = "Masthan555!";
        $dbname = "Mechworks";

        // Creating Database Connection.
        $conn = new mysqli($host,$uname,$passwd,$dbname);
        // Checking Database Connection
        if($conn->connect_error)
        {
            die("Database Connection Failed".$conn->connect_error);
        }

        $stmt = $conn->prepare("select userid from User where email=? and password=?");
        $stmt->bind_param("ss",$email,$password);
        $stmt->execute();
        // Getting The Result Set From Statement Object
        $result = $stmt->get_result();

        if($result->num_rows>0)
        {
            $row = $result->fetch_assoc();
            $uid = $row['userid'];

            session_start();
            $_SESSION['uid'] = $uid;
            unset($_SESSION['wid']);

            $remember = $_POST['remember'];
            if($remember=="remember")
            {
                setcookie("worker","",time()-100);
                setcookie("user","breathing_".$uid,0);
            }

            header("Location: UserIndex.php");

            $conn->close();

        }
        else
        {
            $passwordErr = "Email or Password Invalid, Kindly Check Them Once.";
        }

    }

}

?>

<body>
<div class="container-fluid">
    <div id="formContainer">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" method="post" id="loginForm" novalidate>
            <h4 class="taskName">Login As User</h4>

            <div class="form-group">
                <label for="email">Email : </label>
                <input type="email" id="email" class="form-control" value="<?php echo($email);?>" name="uemail" placeholder="Enter Your Email Address..." required/>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($emailErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Please Enter Valid Email Address Registered With Us.</div>
            </div>
            <div class="form-group">
                <label for="passwd">Password : </label>
                <input type="password" id="passwd" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" value="<?php echo($password);?>" class="form-control" name="upassword" placeholder="Enter Your Password..." required/>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($passwordErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Password Doesn't Meet The Requirements.</div>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="remember" id="remember" value="remember" checked/>
                <label for="remember" class="custom-control-label mt-2 mb-2">Remember Me.</label>
            </div>

            <a href="javascript:void(0)">Forgot Password?</a>

            <input type="submit" class="submitBtn" value="Login Here" />

            <h6 class="mt-5">Don't Have An Account.Please <a href="UserSignup.php">Create Your User Account</a>.</h6>
            <a href="javascript:void(0)" class="googleBtn mb-3">
                <i class="fab fa-google"></i> Login With Google
            </a>

        </form>
    </div>
</div>
</body>
