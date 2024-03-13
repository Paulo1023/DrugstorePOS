<?php
session_start();
require_once('DBConnection.php');
extract($_POST);
$code = "";
while(true){
    $code = mt_rand(1,9999999999);
    $code = sprintf("%.10d",$code);
    $check = $conn->query("SELECT count(transaction_id) as `count` from transaction_list where `or_number` = '{$code}'")->fetchArray()['count'];
    if($check <= 0){
        break;
    }
}

date_default_timezone_set('Singapore');
//$d = date("m/d/Y") . " " . date("h:i");
$d = date("Y-m-d");
$save_trans = $conn->query("INSERT INTO `transaction_list` (`or_number`, 
                                        `customer_name`, 
                                        `total_amount`, 
                                        `amount_tendered`, 
                                        `amount_change`, 
                                        `discount_percentage`, 
                                        `discount_amount`, 
										`date_created`,
                                        `user_id`
                                        )
                                VALUES ('{$code}',
                                        '{$customer_name}',
                                        '{$total}',
                                        '{$amount_tendered}',
                                        '{$amount_change}',
                                        '{$disc_perc}',
                                        '{$disc_amount}',
										'{$d}',
                                        '{$_SESSION['user_id']}'
                                )");

$transaction_id = $conn->query("SELECT transaction_id FROM `transaction_list` ORDER BY transaction_id desc limit 1")->fetchArray()['transaction_id'];
$data = "";
$valid = true;
foreach($product_id as $k => $v){

    if(!empty($data)) $data .= ", ";
		$data .= "('{$transaction_id}',
				   '{$v}',
				   '{$unit[$k]}',
				   '{$quantity[$k]}',
				   '{$price[$k]}',
				   '{$d}'
					)";
	$update_qty = $conn->query("UPDATE `product_list` SET quantity = (quantity-{$quantity[$k]}) WHERE product_id = {$v}");
	
		//$sql = "SELECT * FROM `product_list` WHERE `product_id` = '{$prod_id}'";
		//$qry = $conn->query($sql);
		//$row = $qry->fetchArray();
}
if(!empty($data)){
    $save = $conn->query("INSERT INTO `items` (`transaction_id`,
                                               `product_id`,
                                               `unit`,
                                               `quantity`,
                                               `price`,
											   `date_created`) VALUES {$data}");
	//foreach($product_id as $k => $v){
    //if(!empty($data)) $data .= ", ";
    //$data .= "('{$transaction_id}',
    //         '{$v}',
    //           '{$unit[$k]}',
    //           '{$quantity[$k]}',
    //           '{$price[$k]}'
    //            )";
	//$update_qty = $conn->query("UPDATE `product_list` SET quantity = (quantity-{$quantity[$k]}) WHERE product_id = {$v}");
	//}
}
if($save_trans){
    $resp['status']='success';
    $_SESSION['flashdata']['type'] = 'success';
    $_SESSION['flashdata']['msg'] = 'Transaction successfully saved.';
}else{
    $resp['status'] = 'failed';
    $resp['error'] = $conn->lastErrorMsg();
}
echo json_encode($resp);
?>