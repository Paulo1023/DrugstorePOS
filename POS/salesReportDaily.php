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
$qry = $conn->query("SELECT * FROM `items`")->fetchArray();
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
                    <label for="" class="col-auto"><b>Items</b></label>
                    <span class="border-dark col flex-grow-1 px-2"></span>
                </div>
            </div>
            <div class="col-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Date:</b></label>
                    <span class="border-dark col flex-grow-1 px-2"><?php echo date("l") ?></span>
                </div>
            </div>
        </div>
		<div class="row ">
            <table class="table table-hovered table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center py-0 px-1">QTY</th>
                        <th class="text-center py-0 px-1">Unit</th>
                        <th class="text-center py-0 px-1">Product</th>
                        <th class="text-center py-0 px-1">Price</th>
                        <th class="text-center py-0 px-1">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sub = 0;
					$tday=date('Y-m-d');
					$item_qry = $conn->query("SELECT i.*,p.name FROM `items` i inner join product_list p on i.product_id = p.product_id WHERE i.date_created LIKE '{$tday}%'");
                        while($row = $item_qry->fetchArray()):
                            $sub += $row['price'] * $row['quantity'];
                    ?>
                    <tr>
                        <td class="p-1 text-center"><?php echo $row['quantity'] ?></td>
                        <td class="p-1 text-center"><?php echo $row['unit'] ?></td>
                        <td class="p-1 text-start" title="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></td>
                        <td class="p-1 text-end"><?php echo number_format($row['price'],2) ?></td>
                        <td class="p-1 text-end"><?php echo number_format($row['price'] * $row['quantity'],2) ?></td>
                    </tr>
                    <?php endwhile; ?>
					<?php if(!$item_qry->fetchArray()): ?>
						<tr>
							<th class="text-center p-0" colspan="6">No data display.</th>
						</tr>
					<?php endif; ?>
                </tbody>
                
            </table>
        </div>
        
        <div class="col-12">
        <div class="row justify-content-end mt-3">
            <button class="btn btn-sm rounded-0 btn-dark col-auto me-3" type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
</div>