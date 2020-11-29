<!-- Masthan Swamy -->

<?php include("header.php"); ?>

<?php
    $id = $role="";
    $unknownStat = false;
    $check = false;
    $text = "";
    $unknownPincode="";
    $start = 0;
    $total = 10;
    session_start();

    if(isset($_SESSION['uid']))
    {
        $id = $_SESSION['uid'];
        $role = "User";
    }/*
    else if($_SESSION['wid'])
    {
        $id = $_SESSION['wid'];
        $role = "Worker";
    }*/
?>

<style>
    .container-fluid
    {
        width : 55%;
    }
    .workerData
    {
        width : 205px;
        margin : 2% 2% 2% 5%;
        float : left;
        border : 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
    }
    .workerData:hover
    {
        border : 1px solid #3c3131;
    }
    .workerImage
    {
        width : 100%;
        height : 230px;
        border-radius: 5px 5px 0 0;
        object-fit: cover;
    }
    .workerName
    {
        /*
        font-weight: bold;
        margin-top:10px;
        margin-bottom : 5px;
        text-align : center;
        */
        font-weight: bold;
        padding : 10px 0 10px 10px;
    }
    @media screen and (max-width : 600px)
    {
        .workerData
        {
            width : 42%;
        }
        .workerImage
        {
            height : 230px;
        }
        .workerName
        {
            font-size: 0.9rem;
        }
        .container-fluid
        {
            width : 100%;
        }
    }
</style>

<?php
    function testInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return($data);
    }

    $title = $_REQUEST['title'];

    if(isset($_REQUEST['pincode']))
    {
        $unknownPincode = $_REQUEST['pincode'];
        if(empty($_REQUEST['pincode']))
        {
            header("Location: UserIndex.php");
            exit();
        }
        else
        {
            $unknownPincode = testInput($_REQUEST['pincode']);
            $unknownStat = true;
        }
    }

    if(isset($_REQUEST['title']))
    {
        if(empty($title))
        {
            header("Location: UserIndex.php");
            exit();
        }
        else
        {
            $check = true;
            $title = testInput($title);
        }
    }
    else
    {
        header("Location: UserIndex.php");
        exit();
    }
    if(isset($_REQUEST['text']))
    {
        $text = $_REQUEST['text'];
        if(empty($text))
        {
            header("Location: UserIndex.php");
            exit();
        }
        else
        {
            $text = testInput($text);
            $check = true;
        }
    }
    else
    {
        header("Location: UserIndex.php");
        exit();
    }

    // Checking For Start and end (Pagination)
    if(isset($_REQUEST['start']))
    {
        if(empty($_REQUEST['start']) && $_REQUEST['start']!=0)
        {
            header("Location: UserIndex.php");
            exit();
        }
        else
        {
            $start = $_REQUEST['start'];
        }
    }
?>
<div class="container-fluid">
<h3 class="mt-2 mb-3" style="text-align: center;color:chocolate">"<span style="text-transform: uppercase"><?php echo($text); ?></span>" Near You </h3>

<?php

    if($check==true)
    {
        $op = new Operations();

        if($unknownStat!=true)
        {
            $unknownPincode = $op->setUserPcode($id);
        }
        else
        {
            $op->setPcode($unknownPincode);
        }

        if($start<0)
            $start=0;

        $op->setWorkId($title);
        $op->setCity();
        $total = $op->getTotalRecords();
        $op->setPincodes();
//        $op->arrangePincodes();
//        $op->displayRecords();

        // Printing Previous And Next Pincode Records
/*
        if(($start+10)>$total)
        {
            $tpcode = (int) $unknownPincode;
            $op->setPcode($tpcode-1);
            $total+=$op->getTotalRecords();
            $op->setworkerIds(0);
            $op->displayRecords();
        }*/
    }

?>

<?php
    class Operations
    {
        public $conn;
        public $pcode;
        public $workid;
        public $total;
        public $city;
        public $pincodes = array();

        function __construct()
        {
            // Creating Mysql Connection
            $this->conn = new mysqli("localhost:3306","scott","Masthan555!","Mechworks");
            if($this->conn->connect_error)
            {
                die("Database Connection Not Established : ".$this->conn->connect_error);
            }
        }
        function setUserPcode($id)
        {
            $res = $this->conn->query("select pincode from user where userid=".$id);
            if($res->num_rows>0)
            {
                $row = $res->fetch_assoc();
                $this->pcode = $row['pincode'];
            }
            return($this->pcode);
        }
        function setPcode($pcode)
        {
            $this->pcode = $pcode;
        }
        function setWorkId($wname)
        {
            // Firstly Getting The WorkID
            $stmt = $this->conn->prepare("select workid from works where workname=?");
            $stmt->bind_param("s",$wname);
            $stmt->execute();
            $res = $stmt->get_result();

            if($res->num_rows>0)
            {
                $row = $res->fetch_assoc();
                $this->workid = $row['workid'].",";
            }
            else
            {
                header("Location: UserIndex.php");
                exit();
            }
        }
        function setCity()
        {
            $res = $this->conn->query("select city from worker where pincode='$this->pcode' and INSTR(works,'$this->workid')");
            if($res->num_rows>0)
            {
                $row = $res->fetch_assoc();
                $this->city = $row['city'];
            }
            else
            {
                // There Are No Workers In Your City Starts Here.
            }
            // Setting Total No Of Records.
            $this->total = $res->num_rows;
        }
        function getTotalRecords()
        {
            $res = $this->conn->query("select count(*) from worker where INSTR(works,'$this->workid') and city='$this->city'");
            if($res->num_rows>0)
            {
                $row = $res->fetch_array();
                $this->total = $row[0];
            }
            return($this->total);
        }
        function setPincodes()
        {
            $res = $this->conn->query("select distinct pincode from worker where INSTR(works,'$this->workid') and city='$this->city' order by pincode");
            if($res->num_rows>0)
            {
                while($row = $res->fetch_assoc())
                {
                    $this->pincodes[count($this->pincodes)] = (int) $row['pincode'];
                }
            }
            else
            {
                // There Are No Workers In Your City.
            }
            $this->arrangePincodes();
        }

        function arrangePincodes()
        {
            $tpcodes = $this->pincodes;
            for($i=0;count($tpcodes);$i++)
            {
                $tpcodes[$i] = ($tpcodes[$i]-($this->pcode));
                if($tpcodes[$i]<0)
                {
                    $tpcodes[$i] = (-$tpcodes[$i])-0.5;
                }
            }

            $ar3 = $tpcodes;
            $ar4 = array();
            sort($ar3);
            for($i=0;$i<count($ar3);$i++)
            {
                $ind = array_search($ar3[$i],$tpcodes);
                $ar4[count($ar4)] = $this->pincodes[$ind];
            }
            $this->pincodes = $ar4;

            print_r($this->pincodes);
        }
        /*
        function setworkerIds($start)
        {
            $res = $this->conn->query("select workerid from workerworks where workid=$this->workid and pincode='".$this->pcode."' limit $start,10");

            if($res->num_rows>0)
            {
                while($row=$res->fetch_assoc())
                {
                    $this->workerid[count($this->workerid)] = $row['workerid'];
                }
            }
        }
        */
        /*
        function displayRecords()
        {
            if(count($this->workerid)>0)
            {
                for ($i = 0; $i < count($this->workerid); $i++)
                {
                    $workerid = $this->workerid[$i];
                    $imageName = "";
                    $res = $this->conn->query("select name,pincode from worker where workerid=".$workerid);
                    if($res->num_rows>0)
                    {
                        if(file_exists("Images/".$workerid.".png"))
                            $imageName = "Images/".$workerid.".png";
                        else if(file_exists("Images/".$workerid.".jpg"))
                            $imageName = "Images/".$workerid.".jpg";
                        else if(file_exists("Images/".$workerid.".jpeg"))
                            $imageName = "Images/".$workerid.".jpeg";
                        else if(file_exists("Images/".$workerid.".jfif"))
                            $imageName = "Images/".$workerid.".jfif";

                        $row = $res->fetch_assoc();
?>
                    <div class='workerData'>
                        <img src='<?php echo($imageName); ?>' class="workerImage" alt='Worker Details' >
                        <div class="workerName">
                            <span style="color:#3c3131"> Name : <span style="color:chocolate"><?php echo($row['name']); ?></span></span><br>
                            <span style="color:#3c3131"> Mobile : <span style="color:chocolate">+91 <?php echo($row['pincode']); ?></span></span>
                        </div>
                    </div>
<?php
                    }
                }
            }
            else
            {
                echo("Sorry No Records Found.");
            }
        }
        */

        function displayRecords()
        {
            $imageName = "";
            $res = $this->conn->query("select workerid,name,pincode from worker where INSTR(works,'$this->workid') and city='$this->city'");
            if($res->num_rows>0)
            {
                while($row=$res->fetch_assoc())
                {
                    $workerid = $row['workerid'];
                    if(file_exists("Images/".$workerid.".png"))
                        $imageName = "Images/".$workerid.".png";
                    else if(file_exists("Images/".$workerid.".jpg"))
                        $imageName = "Images/".$workerid.".jpg";
                    else if(file_exists("Images/".$workerid.".jpeg"))
                        $imageName = "Images/".$workerid.".jpeg";
                    else if(file_exists("Images/".$workerid.".jfif"))
                        $imageName = "Images/".$workerid.".jfif";

?>
    <div class='workerData'>
    <img src='<?php echo($imageName); ?>' class="workerImage" alt='Worker Details' >
        <div class="workerName">
            <span style="color:#3c3131"> Name : <span style="color:chocolate"><?php echo($row['name']); ?></span></span><br>
            <span style="color:#3c3131"> Pincode : <span style="color:chocolate"> <?php echo($row['pincode']); ?></span></span>
        </div>
    </div>
<?php
                }
            }

        }

    }
?>
        <style>
            #prev
            {
                width : 20%;
                float:left;
                font-size : 1.3rem;
                margin : 10px auto 20px 10px;
                border-radius: 5px;
                clear : both;
            }
            #next
            {
                width : 20%;
                float: right;
                font-size : 1.3rem;
                margin : 10px 10px 20px auto;
                border-radius: 5px;
            }
        </style>

        <button class="btn btn-secondary" id="prev">Prev</button>
        <button class="btn btn-secondary" id="next">Next</button>
    <script>
        $(document).ready(function()
        {
            let start,end,total;
            start = Number("<?php echo($start);?>");
            total = Number("<?php echo($total);?>");

            if(start==0)
            {
                $("#prev").css("visibility","hidden");
            }
            if((start+10)>total)
            {
                $("#next").css("visibility","hidden");
            }
            $("#next").click(function()
            {
                start+=10;
                let url = "<?php echo(htmlspecialchars($_SERVER['PHP_SELF']));?>"+"?title="+"<?php echo($title);?>"+"&pincode="+"<?php echo($unknownPincode);?>"+"&text="+"<?php echo($text);?>"+"&start="+start;
                window.location.assign(url);
            });
            $("#prev").click(function()
            {
                start-=10;
                let url = "<?php echo(htmlspecialchars($_SERVER['PHP_SELF']));?>"+"?title="+"<?php echo($title);?>"+"&pincode="+"<?php echo($unknownPincode);?>"+"&text="+"<?php echo($text);?>"+"&start="+start;
                window.location.assign(url);
            });
        });
    </script>
</div>
