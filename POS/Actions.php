<?php 
session_start();
require_once('DBConnection.php');

Class Actions extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM user_list where username = '{$username}' and `password` = '".md5($password)."' ";
        $qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function logout(){
        session_destroy();
        header("location:./");
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `user_list` set {$data} where user_id = '{$_SESSION['user_id']}'";
            $save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_product(){
        extract($_POST);
        if(empty($id))
            $sql = "INSERT INTO `product_list` (`product_code`,`unit`, 	`quantity`,`name`,`description`, `expiry_date`, `price`)VALUES('{$product_code}','{$unit}','{$quantity}','{$name}','{$description}','{$expiry_date}','{$price}')";
        else{
            $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id'))){
                     if(!empty($data)) $data .= ", ";
                     $data .= " `{$k}` = '{$v}' ";
                 }
             }
            $sql = "UPDATE `product_list` set {$data} where `product_id` = '{$id}' ";
        }
        $check = $this->query("SELECT COUNT(product_id) as count from `product_list` where `product_code` = '{$product_code}' ".($id > 0 ? " and product_id != '{$id}'" : ""))->fetchArray()['count'];
        if($check > 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Product Code already exists.';
        }else{
            $save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Product successfully saved.";
                else
                    $resp['msg'] = "Product successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Product Failed.";
                else
                    $resp['msg'] = "Updating Product Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_product(){
        extract($_POST);

        $delete = $this->query("DELETE FROM `product_list` where product_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Product successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_user(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(empty($id)){
            $cols[] = 'password';
            $values[] = "'".md5($username)."'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        

       
        $check = $this->query("SELECT count(user_id) as `count` FROM user_list where `username` = '{$username}' ".($id > 0 ? " and user_id != '{$id}' " : ""))->fetchArray()['count'];
        if($check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `user_list` {$data}";
            }else{
                $sql = "UPDATE `user_list` set {$data} where user_id = '{$id}'";
            }
            $save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'New User successfully saved.';
                else
                $resp['msg'] = 'User Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_user(){
        extract($_POST);

        $delete = $this->query("DELETE FROM `user_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function searh_prod(){
        extract($_POST);
        $qty = 1;
        if(strpos($t,'*') !== false){
            $ex = explode("*",$t);
            $term = (isset($ex[1]))? $ex[1] : "";
            $qty = $ex[0];
        }else{
            $term = $t;
        }
		$vd = date("Y-m-d");
        $qry = $this->query("SELECT * FROM `product_list` where `product_code` LIKE '%{$term}%' OR `name` LIKE '%{$term}%' AND julianday(expiry_date)>julianday('{$vd}') AND quantity>0");
        $data = array();
        if($term !=""){
            
            while($row = $qry->fetchArray()){
                $row['qty'] = $qty;
                $data[] = array("label"=>$row['product_code']." - ".$row['name']." - EXP:".$row['expiry_date'],
                                "value"=>$row['product_code'],
                                "id"=>$row['product_id'],
                                "data"=>$row
                                );
            }
            if(!$qry->fetchArray()){
                $data[] = array("label"=>"Product Code is Unknown.",
                                "value"=>"",
                                "id"=>""
                                );
            }
        }
        
        return json_encode($data);
    }
    function delete_transaction(){
        extract($_POST);

        $delete = $this->query("DELETE FROM `transaction_list` where transaction_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Transaction successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'save_product':
        echo $action->save_product();
    break;
    case 'delete_product':
        echo $action->delete_product();
    break;
    case 'save_user':
        echo $action->save_user();
    break;
    case 'delete_user':
        echo $action->delete_user();
    break;
    case 'searh_prod':
        echo $action->searh_prod();
    break;
    case 'delete_transaction':
        echo $action->delete_transaction();
    break;
    default:
    // default action here
    break;
}