<!-- Masthan Swamy -->

<?php include("header.php"); ?>
<title>Enter Address</title>

<?php
    session_start();
    $id="";
    $role="";

    if(isset($_REQUEST['role']))
    {
        if($_REQUEST['role']=="user")
        {
            $role="user";
            $id = $_SESSION['uid'];
        }
        else if($_REQUEST['role']=="worker")
        {
            $role = "worker";
            $id = $_SESSION['wid'];
        }
    }
    else
    {
        if(isset($_SESSION['uid']))
        {
            $id = $_SESSION['uid'];
            $role = "User";
        }
        else if($_SESSION['wid'])
        {
            $id = $_SESSION['wid'];
            $role = "Worker";
        }
        else
        {
            header("Location: UserLogin.php");
        }
    }
?>

<?php
    function testInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return($data);
    }

    // Validation Starts From Here
    $country = $state = $city = $pcode = $al = "";
    $countryErr = $stateErr = $cityErr = $pcodeErr = $alErr = "";

    if($_SERVER['REQUEST_METHOD']=='POST')
    {
        $country = "India";
        $state = $_POST['state'];
        $city = $_POST['city'];
        $pcode = $_POST['pincode'];
        $al = $_POST['addressLine'];

        $check = true;

        if(empty($country))
        {
            $countryErr = "Please Select Your Country";
            $check = false;
        }
        else
        {
            $country = testInput($country);
        }
        if(empty($state)||$state=="SELECT STATE")
        {
            $stateErr = "Please Select Your State";
            $check = false;
        }
        else
        {
            $state = testInput($state);
        }
        if(empty($city))
        {
            $cityErr = "Please Select Your City";
            $check = false;
        }
        else
        {
            $city = testInput($city);
        }
        if(empty($pcode))
        {
            $pcodeErr = "Please Select Your Country";
            $check = false;
        }
        else
        {
            if(!preg_match("/^[0-9]{6}$/",$pcode))
            {
                $pcodeErr = "Please Enter Valid 6 Digit Pincode For Worker. ";
                $check = false;
            }
            else
            {
                $pcode = testInput($pcode);
            }
        }
        if(empty($al))
        {
            $alErr = "Please Provide Your Nearby Details...";
            $check = false;
        }
        else
        {
            $al = testInput($al);
        }

        if($check==true)
        {
            //Creating Mysql Connection
            $conn = new mysqli("localhost:3306","scott","Masthan555!","Mechworks");
            // Checking Connection
            if($conn->connect_error)
            {
                die("Database Connection Failed : ".$conn->connect_error);
            }

            // If Taking Full Address use this
            $sql = "update ".$role." set country=?,state=?,city=?,pincode=?,addressLine=? where ".($role."id")."=".$id;
            // else use this
            //$sql = "update ".$role." set pincode=?,addressLine=? where ".($role."id")."=".$id;
            $stmt = $conn->prepare($sql);
            // For Full Address
            $stmt->bind_param("sssss",$country,$state,$city,$pcode,$al);
            // else
            //$stmt->bind_param("ss",$pcode,$al);
            $stmt->execute();

            if(strcasecmp($role,"user")==0)
            {
                header("Location: UserIndex.php");
            }

            if(strcasecmp($role,"worker")==0)
            {
                $_SESSION['pincode'] = $pcode;
                header("Location: WorkerSignup2.php");
            }

            $conn->close();

        }
    }

?>

<script>
    $(document).ready(function()
    {
     //   $("#country").load("AjaxFiles/Countrys.txt");

        $("#addressForm").submit(function()
        {
            let country = document.getElementById("country");
            let state = document.getElementById("state");
            let city = document.getElementById("city");
            let pcode = document.getElementById("pincode");
            let al = document.getElementById("addressLine");
            let check=true;

            // add this code : (!country.validity.valid || !state.validity.valid || !city.validity.valid || ) if you need total address
            if(!pcode.validity.valid || !al.validity.valid)
            {
                $("#addressForm").addClass("was-validated");
                check=false;
            }
            else
            {
                $("#addressForm").removeClass("was-validated");
            }

            if(state.value === "SELECT STATE")
            {
                $("#stateCrr").hide();
                $("#stateErr").show();
                $("#state").css({"border":"1px solid red"});
                check=false;
            }
            else
            {
                $("#stateCrr").show();
                $("#stateErr").hide();
                $("#state").css({"border":"1px solid green"});
            }

            return(check);
        });
    });
</script>

<body>
<div class="container-fluid">
    <div id="formContainer">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" method="post" id="addressForm" novalidate>
            <h4 class="taskName">Please Enter Your Address</h4>

            <div class="form-group">
                <label for="country">Country : </label>
                <select class="form-control" id="country" name="country" required disabled>
                    <option value="India" selected>India</option>
                </select>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($countryErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Please Select Your Country.</div>
            </div>
            <div class="form-group">
                <label for="state">State : </label>
                <select class="form-control" id="state" name="state" onchange="selct_district(this.value)" required>
                </select>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($stateErr); ?></div>
                <div id="stateCrr" class="valid-feedback">Validated.</div>
                <div id="stateErr" class="invalid-feedback">Please Select Your State.</div>
            </div>
            <div class="form-group">
                <label for="city">City : </label>
                <select class="form-control" name="city" id="city" required>
                </select>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($passwordErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Please Select Your State</div>
            </div>
            -->
            <div class="form-group">
                <label for="pincode">PIN Code : </label>
                <input type="text" id="pincode" pattern="[0-9]{6}" class="form-control" name="pincode" placeholder="Enter Your Pincode Here..." required/>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($pcodeErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Please Enter Valid Pincode Of 6 Digits.</div>
            </div>
            <div class="form-group">
                <label for="addressLine">Address Line : </label>
                <textarea rows="4" id="addressLine" class="form-control" name="addressLine" placeholder="Ex: Nearby Road Name, Center Name, Landmarks e.t.c.," required></textarea>
                <div class="manual-invalid-feedback serverErrorStyle"><?php echo($alErr); ?></div>
                <div class="valid-feedback">Validated.</div>
                <div class="invalid-feedback">Please Enter Address For Workers To Find You.</div>
            </div>

            <input type="submit" class="submitBtn" value="Submit Address" />
        </form>
    </div>
</div>
</body>