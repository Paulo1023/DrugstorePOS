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
$qry = $conn->query("SELECT t.*,u.fullname FROM `transaction_list` t inner join user_list u on t.user_id = u.user_id where t.transaction_id = '{$_GET['id']}'")->fetchArray();
foreach($qry as $k => $v){
    if(!is_numeric($k))
    $$k = $v;
}
//SELECT datetime("m/d/Y H:i a")
//SELECT SUM(total_amount) FROM transaction_list WHERE date_created = (SELECT datetime("m/d/Y H:i a"))
?>
<div class="cotainer-flui">
    <div class="col-12">
        <div class="row">
            <div class="col-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>OR Number:</b></label>
                    <span class="border-bottom border-dark col flex-grow-1 px-2"><?php echo $or_number ?></span>
                </div>
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Customer Name:</b></label>
                    <span class="border-bottom border-dark col flex-grow-1 px-2"><?php echo $customer_name ?></span>
                </div>
            </div>
            <div class="col-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Date:</b></label>
                    <span class="border-bottom border-dark col flex-grow-1 px-2"><?php echo date("Y-m-d",strtotime($date_created)) ?></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="fs-5"><b>Items</b></div>
            </div>
            <hr>
            <table class="table table-hovered table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="py-0 px-1">QTY</th>
                        <th class="py-0 px-1">Unit</th>
                        <th class="py-0 px-1">Product</th>
                        <th class="py-0 px-1">Price</th>
                        <th class="py-0 px-1">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sub = 0;
                        $item_qry = $conn->query("SELECT i.*,p.name FROM `items` i inner join product_list p on i.product_id = p.product_id where transaction_id = '{$transaction_id}'");
                        while($row = $item_qry->fetchArray()):
                            $sub += $row['price'] * $row['quantity'];
                    ?>
                    <tr>
                        <td class="p-1 text-center"><?php echo $row['quantity'] ?></td>
                        <td class="p-1 text-center"><?php echo $row['unit'] ?></td>
                        <td class="p-1 text-start truncate-1" title="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></td>
                        <td class="p-1 text-end"><?php echo number_format($row['price'],2) ?></td>
                        <td class="p-1 text-end"><?php echo number_format($row['price'] * $row['quantity'],2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="py-0 px-1 text-end" colspan="4">SubTotal</th>
                        <th class="py-0 px-1 text-end"><?php echo number_format($sub,2) ?></th>
                    </tr>
                    <tr>
                        <th class="py-0 px-1 text-end" colspan="4">Discount %</th>
                        <th class="py-0 px-1 text-end"><?php echo number_format($discount_percentage,2)  ?>%</th>
                    </tr>
                    <tr>
                        <th class="py-0 px-1 text-end" colspan="4">Discount</th>
                        <th class="py-0 px-1 text-end"><?php echo number_format($discount_amount,2) ?></th>
                    </tr>
                    <tr>
                        <th class="py-0 px-1 text-end" colspan="4">Grand Total</th>
                        <th class="py-0 px-1 text-end"><?php echo number_format($total_amount,2) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row justify-content-end">
            <div class="col-6">
                <div class="w-100 d-flex">
                    <label for="" class="col-auto"><b>Cashier:</b></label>
                    <span class="border-bottom border-dark col flex-grow-1 px-2"><?php echo $fullname ?></span>
                </div>
            </div>
        </div>
        <div class="col-12">
        <div class="row justify-content-end mt-3">
            <button class="btn btn-sm rounded-0 btn-dark col-auto me-3" type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
</div>