<style>
    #uni_modal .modal-footer{
        display:none !important
    }
	.modal-header {
		background-color: #76d5cb;
	}
	.modal-body {
		background-color: #DCF0ED;
	}
	.table {
		border: 1px solid grey;
	}
	
</style>
<?php 
require_once('DBConnection.php');
$sql = "SELECT * FROM `product_list` order by `name` asc";
$qry = $conn->query($sql);
foreach($qry as $k => $v){
    if(!is_numeric($k))
    $$k = $v;
}
?>
<div class="cotainer-fluid">
    <div class="col-12 w-100">
        <div class="row">
            <div class="col-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>EXPIRED</b></label>
                    <span class="border-dark col flex-grow-1 px-2"></span>
                </div>
            </div>
            <div class="col-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Date:</b></label>
                    <span class="border-dark col flex-grow-1 px-2"><?php echo date("Y-m-d") ?></span>
                </div>
            </div>
        </div>
		<div class="card">
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
			<colgroup>
                <col width="3%">
                <col width="5%">
                <col width="10%">
                <col width="5%">
				<col width="12%">
                <col width="10%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Code</th>
                    <th class="text-center p-0">Unit</th>
					<th class="text-center p-0">Quantity</th>
                    <th class="text-center p-0">Name</th>
					<th class="text-center p-0">Expiry</th>
                    <th class="text-center p-0">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php 
					$vd= date('Y-m-d');
					$sql = "SELECT * FROM `product_list` WHERE julianday(expiry_date) < julianday('{$vd}') order by `name` asc";
					$qry = $conn->query($sql);
					$i = 1;
						while($row = $qry->fetchArray()):
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1"><?php echo $row['product_code'] ?></td>
                    <td class="py-0 px-1"><?php echo $row['unit'] ?></td>
					<td class="py-0 px-1"><?php if ($row['quantity']<15){echo "<p class='low'>" . $row['quantity'] . "</p>";}else if ($row['quantity']>15 and $row['quantity']<50){echo "<p class='moderate'>" . $row['quantity'] . "</p>";} else {echo "<p class='high'>" . $row['quantity'] . "</p>";}?></td>
                    <td class="py-0 px-1"><?php echo $row['name'] ?></td>
					<td class="py-0 px-1"><?php if (date("Y-m-d")>$row['expiry_date']){echo "<p class='low'>" . $row['expiry_date'] . "</p>";} else if ((substr($row['expiry_date'], 0, 4)-date("Y"))>0 and (substr($row['expiry_date'], 0, 4)-date("Y"))<3){echo "<p class='moderate'>" . $row['expiry_date'] . "</p>";} else {echo "<p class='high'>" . $row['expiry_date'] . "</p>";} ?></td>
                    <td class="py-0 px-1 text-end"><?php echo $row['price'] ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if(!$qry->fetchArray()): ?>
                    <tr>
                        <th class="text-center p-0" colspan="6">No data display.</th>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
        
        <div class="col-12">
        <div class="row justify-content-end mt-3">
            <button class="btn btn-sm rounded-0 btn-dark col-auto me-3" type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
</div>