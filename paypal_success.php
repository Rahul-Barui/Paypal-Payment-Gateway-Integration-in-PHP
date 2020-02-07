<?php 
    /* coded by Rahul Barui ( https://github.com/Rahul-Barui ) */

    // Include paypal configuration file 
    include_once 'paypal_config.php';
 
    // Include database connection file 
    include_once 'dbcon.php';

    // get Current Date and Time
    function getCurrentDate(){
        $timestamp = time();  
        $date = date("Y-m-d", $timestamp);
        return $date;
    }
    function getCurrentTime(){
        date_default_timezone_set("Asia/Kolkata"); 
        $time = date("H:i:s");
        return $time;
    }

    // If transaction data is available in the URL

    if(!empty($_GET['item_number']) && !empty($_GET['tx']) && !empty($_GET['amt']) && !empty($_GET['cc']) && !empty($_GET['st'])){ 

        // Get transaction information from URL 
        $in = $_GET['item_number'];  
        $tid = $_GET['tx']; 
        $pg = $_GET['amt']; 
        $cc = $_GET['cc']; 
        $pst = $_GET['st'];

        $dt = getCurrentDate();
        $tm = getCurrentTime();

        //Get product info from the database table
        $sql_get = "SELECT * FROM `product` WHERE `id`='$id'";
        $data_get = mysqli_query($con,$sql_get) or die('MySQL Error (Paypal Success1'.mysqli_error($con));
        $row = mysqli_fetch_assoc($data_get);

        // Check if transaction data exists with the same TXN ID. 
        $sql_c = "SELECT * FROM `payments` WHERE `txn_id` = '$tid'"; 
        $data_c = mysqli_query($con,$sql_c) or die('MySQL Error (Paypal Success2)'.mysqli_error($con));
        $count = mysqli_num_rows($data_c);

        if($count==0){

            // Insert tansaction data into the database 
            $sql_tr = "INSERT INTO `payments`(`item_number`,`txn_id`,`payment_gross`,`currency_code`,`payment_status`,`dt`,`tm`) VALUES ('$in','$tid','$pg','$cc','$pst','$dt','$tm')";
            $data_tr = mysqli_query($con,$sql_tr) or die('MySQL Error (Insert)'.mysqli_error($con));
            $row_tr = mysqli_fetch_assoc($data_tr);
            
            // Get last tansaction data from database
            $sql_last = "SELECT * FROM `payments`";
            $data_last = mysqli_query($con,$sql_last) or die('MySQL Query Error (Insert1)'.mysqli_error($con));
            while($row_last=mysqli_fetch_assoc($data_last)){
                $last_id = $row_last['payment_id'];
            }

        } else {

            // Update tansaction data into the database 
            $sql_tr = "UPDATE `payments` SET `item_number`='$in',`txn_id`='$tid',`payment_gross`='$pg',`currency_code`='$cc',`payment_status`='$pst',`dt`='$dt',`tm`='$tm' WHERE `txn_id` = '$tid'";
            $data_tr = mysqli_query($con,$sql_tr) or die('MySQL Error (Paypal update-1)'.mysqli_error($con));
            $row_tr = mysqli_fetch_assoc($data_tr);
            
            // Get last tansaction data from database
            $sql_last = "SELECT * FROM `payments`";
            $data_last = mysqli_query($con,$sql_last) or die('MySQL Query Error update-2'.mysqli_error($con));
            while($row_last=mysqli_fetch_assoc($data_last)){
                $last_id = $row_last['payment_id'];
            }
            
        }

        //Get tansaction data from database
        $sql_tr2 = "SELECT * FROM `payments` WHERE `payment_id`='$last_id'";
        $data_tr2 = mysqli_query($con,$sql_tr2) or die('MySQL Error (Paypal Success1'.mysqli_error($con));
        $row_tr2 = mysqli_fetch_assoc($data_tr2);
        
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title> Paypal Payment Gateway Integration in PHP </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="css/paypal.css">
    </head>

    <div class="container">

        <?php if(!empty($last_id)){ ?>
            <h2 style="text-align: center; color: blue;">Thank You !!</h2>
            <h3 style="text-align: center; color: green;">Your Payment has been Successful. </h3>
        <?php } else { ?>
            <h2 style="text-align: center; color: blue;">Sorry !!</h2>
        <?php } ?>

        <br>
        <div class="row">

            <div class="col-lg-12">
                <div class="status">
                    <?php if(!empty($last_id)){ ?>
                        <h4 class="heading">Payment Information - </h4>
                        <br>
                        <p><b>Reference ID : </b> <strong><?php echo $last_id; ?></strong></p>
                        <p><b>Transaction ID : </b> <?php echo $tid; ?></p>
                        <p><b>Paid Amount  : </b> <?php echo $pg;?></p>
                        <p><b>Currency : </b> <?php echo $cc; ?></p>
                        <p><b>Payment Status : </b> <?php echo $pst; ?></p>

                        <h4 class="heading">Product Information - </h4>
                        <br>
                        <p><b>Name : </b> <?php echo $in; ?></p>
                        <p><b>Price : </b> <?php echo $pg?></p>

                        <h4 class="heading">Date & Time</h4>
                        <p><b>Pay Date : </b> <?php echo date("M d, Y", strtotime($row_tr2['dt'])); ?></p>
                        <p><b>Pay Time : </b> <?php echo date("h:i A", strtotime($row_tr2['tm'])); ?></p>

                    <?php } else { ?>

                        <h1 class="error">Sorry !! Your Payment has Failed.</h1>

                    <?php } ?>
                </div>
            </div>

        </div>

        <h3 style="text-align: center;"><a href="index.php" class="btn-continue">Back to Home</a></h3>

    </div>
</html>
