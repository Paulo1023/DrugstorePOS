<?php
date_default_timezone_set('UTC');
$curdate = date("Y-m-d");


function sales($x) {
$conn = new DBConnection();
		$qry = $conn->query("SELECT SUM(total_amount) as total FROM transaction_list WHERE date_created LIKE '{$x}%'")->fetchArray()['total'];
		return $qry;
}
function qty($inv) {
$conn = new DBConnection();
	$vd = date("Y-m-d");
	if($inv == "stock") {
		$qry = $conn->query("SELECT sum(quantity) as stock FROM product_list WHERE julianday(expiry_date) > julianday('{$vd}')")->fetchArray()['stock'];
		return $qry;
	} else if($inv == "exp") {
		$qry = $conn->query("SELECT sum(quantity) as expiredstocks FROM product_list WHERE julianday(expiry_date) < julianday('{$vd}')")->fetchArray()['expiredstocks'];
		return $qry;
	}
}
function invvalue($val) {
$conn = new DBConnection();
	$vd = date("Y-m-d");
	if($val == "stockvalue") {
		$qry = $conn->query("SELECT sum(quantity*price) as stockvalue FROM product_list WHERE julianday(expiry_date) > julianday('{$vd}')")->fetchArray()['stockvalue'];
		return $qry;
	} else if($val == "expvalue") {
		$qry = $conn->query("SELECT sum(quantity*price) as expiredvalue FROM product_list WHERE julianday(expiry_date) < julianday('{$vd}')")->fetchArray()['expiredvalue'];
		return $qry;
	}
}
function convert(int $number) {
    if ($number >= 1E9) {
        return round($number / 1E9, 2).' B';
    } else if ($number >= 1E6) {
        return round($number / 1E6, 2).' M';
    } else if ($number >= 1E3) {
        return round($number / 1E3, 2).' K';
    }
    return $number;
}


?>
<html>
<head>
<script>
var d = new Date(<?php echo time() * 1000 ?>);

function updateClock() {
  d.setTime(d.getTime() + 1000);

  var currentHours = d.getHours();
  var currentMinutes = d.getMinutes();
  var currentSeconds = d.getSeconds();

  currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
  currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;

  var meridian = (currentHours < 12) ? "AM" : "PM";

  currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;
  currentHours = (currentHours == 0) ? 12 : currentHours;

  var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + meridian;

  document.getElementById("clock").firstChild.nodeValue = currentTimeString;
}

window.onload = function() {
  updateClock();
  setInterval('updateClock()', 1000);
}
$(function(){
        $('#daybtn').click(function(){
            uni_modal('Report',"salesReportDaily.php")
        })
		$('#invexpired').click(function(){
            uni_modal('Report',"expiredInventoryReport.php")
        })
})
</script>
<style>
	body {
		background-image: url("extra/bg.jpg");
		background-size: cover;
		font-family: Bahnschrift;
	}
	
	.tiles{
		margin: 20px;
		border: 1px solid #76d5cb;
		box-shadow: 10px 10px #76d5cb;
		border-radius: 5px;
		width: relative;
		min-width: 250px;
		text-align: left;
		background-color: #DCF0ED;
		margin-bottom: 20px;
		display : inline-block;
		cursor: pointer;
		padding: auto;
	}
	.topcontent {
		display: block;
		width: 100%;
	}
	.tiles:active {
		animation-name: press;
		animation-duration: .1s;
	}
	@keyframes press {
		0%{left:0px; top:0px; box-shadow: 10px 10px #76d5cb;}
		50%{left:3px; top:3px; box-shadow: 7px 7px #76d5cb;}
		100%{left:3px; top:3px; box-shadow: 7px 7px #76d5cb;}
	}
	#invstock h6{
		color: #069C10;
	}
	#invexpired h6 {
		color: #CF0D06;
	}
	.iconsales {
		background-image: url("extra/salesicon.png");
		background-size: cover;
		height: 65px;
		width: 70px;
		display: inline-block;
		opacity: .4;
	}
	.iconinv {
		background-image: url("extra/stockicon.png");
		background-size: cover;
		height: 65px;
		width: 70px;
		display: inline-block;
		opacity: .4;
	}
	.info {
		width: 70%;
		display: inline-block;
	}
	.hovcol {}
	.hovcol:hover {
		background-color: green;
	}
	.hovcol1 {}
	.hovcol1:hover {
		background-color: red;
		color: black;
	}
	a {
		text-decoration: none;
	}
</style>
</head>
<div style="width: 100%;">
        <div style="width: 50%; height: relative; float: left;"> 
            <h3>Dashboard</h3>
        </div>
        <div id="clock" style="margin-left: 50%; height: relative; text-align: right;"> 
            <p><?php echo date("l") . " " .date("m/d/Y") ?></p>
        </div>
    </div>
<hr>
<div class="content">
<div class="contentheader">
	<h4>Sale Activity</h4>
</div>
	<div class="topcontent">
		<div class="tiles hovcol" id="daybtn">
			<div class="info">
				<h6><?php echo date("l") ?></h6>
				<h2>PHP <?php echo (is_null(sales($curdate)) ? 0 : convert(sales($curdate))) ?></h2>
				<h5>Today's Sale</h5>
			</div>
			<div class="iconsales">
			</div>
		</div>
		<div class="tiles hovcol" id="monthbtn">
		<a aria-current="page" href="./?page=sales">
			<div class="info">
				<h6><?php echo date("F") ?></h6>
				<h2>PHP <?php $m = date("Y-m"); echo (is_null(sales($m)) ? 0 : convert(sales($m))) ?></h2>
				<h5>Monthly Sale</h5>
			</div>
			<div class="iconsales">
			</div>
		</a>
		</div>
		<div class="tiles hovcol" id="yearbtn">
		<a aria-current="page" href="./?page=sales">
			<div class="info">
				<h6><?php echo date("Y") ?></h6>
				<h2>PHP <?php $y = date("Y"); echo (is_null(sales($y)) ? 0 : convert(sales($y))) ?></h2>
				<h5>Yearly Sale</h5>
			</div>
			<div class="iconsales">
			</div>
		</a>
		</div>
	</div>
<div class="contentheader">
	<h4>Inventory</h4>
</div>
	<div class="midcontent">
		
		<div class="tiles hovcol" id="invstock">
		<a aria-current="page" href="./?page=products">
			<div class="info">
				<h6>PHP <?php echo (is_null(invvalue("stockvalue")) ? 0 : convert(invvalue("stockvalue"))) ?></h6>
				<h1><?php echo (is_null(qty("stock")) ? 0 : qty("stock")) ?></h1>
				<h5>Stock</h5>
			</div>
			<div class="iconinv">
			</div>
		</a>
		</div>
		<div class="tiles hovcol1" id="invexpired">
			<div class="info">
				<h6>PHP <?php echo (is_null(invvalue("expvalue"))? 0 : "-" . convert(invvalue("expvalue"))) ?></h6>
				<h1><?php echo (is_null(qty("exp")) ? 0 : qty("exp")) ?></h1>
				<h5>Expired</h5>
			</div>
			<div class="iconinv">
			</div>
		</div>
	</div>
</div>
<!--
<div class="modal fade" id="reportModal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">testers</h4>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>
-->


<body>
</body>
</html>