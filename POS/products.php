<style>
	body {
		background-image: url("extra/bg.jpg");
		background-size: cover;
		font-family: Bahnschrift;
	}
	.card-header {
		background-color: #76d5cb;
	}
	.card {
		background-color: #DCF0ED;
		border: 1px solid #76d5cb;
		box-shadow: 10px 10px #76d5cb;
	}
	.btn {
		background-color: #76d5cb;
		border: 1px solid black;
	}
	img {
		width: 24px;
		height: 24px;
	}
	.card-tools button {
		border-radius: 50px;
		width: 40px;
		height: 40px;
		text-position: fixed;
		white-space: nowrap;
		overflow: hidden;
		transition: width 1.5s;
	}
	.card-tools button:hover {
		width: 130px;
	}
	.low {
		font-weight: bold;
		color: red;
		text-align: center;
	}
	.moderate {
		font-weight: bold;
		color: orange;
		text-align: center;
	}
	.high {
		font-weight: bold;
		color: green;
		text-align: center;
	}
	.legend {
		padding: 15px;
		background-color: #76d5cb;
		border: 1px solid #76d5cb;
		margin-bottom: 10px;
		border-radius: 5px;
	}
	.qtylegend, .expirylegend {
		float:left;
		width: 50%;
	}
	ul {
        list-style-type:none;
    }
	.qtylegendlow {
		width: 15px;
		height: 5px;
		background-color: red;
		display: inline-block;
	}
	.qtylegendmod {
		width: 15px;
		height: 5px;
		background-color: orange;
		display: inline-block;
	}
	.qtylegendhigh {
		width: 15px;
		height: 5px;
		background-color: green;
		display: inline-block;
	}
	fieldset {
		width: 100%;
	}
</style>
<?php
$sql = "SELECT * FROM `product_list` order by `name` asc";
$qry = $conn->query($sql);
$i = 1;
$d = date("Y-m-d");
while($row = $qry->fetchArray()):
	if ($row['expiry_date']<$d) {
		echo '<div class="alert alert-danger">
    <strong>Reminder : </strong> '.$row['product_code'].' - '.$row['name'].' has already expired.
  </div>';
	}
endwhile;
?>
<div class="legend w-100 flex-grow-0">
     <fieldset class="border border-dark p-1 align-middle">
        <legend class="w-auto float-none fs-5 align-middle">Color Code</legend>
			<div class="qtylegend">
				<h5>Quantity</h5>
				<ul>
					<li><div class="qtylegendhigh"></div> More than 50</li>
					<li><div class="qtylegendmod"></div>  16-50</li>
					<li><div class="qtylegendlow"></div>  Extremely low or Out of Stock</li>
				</ul>
			</div>
			<div class="expirylegend">
				<h5>Expiry Date</h5>
				<ul>
					<li><div class="qtylegendhigh"></div> Greater than or Equal to 3 years</li>
					<li><div class="qtylegendmod"></div>  1-2 years</li>
					<li><div class="qtylegendlow"></div>  Less than a year or Expired</li>
				</ul>
			</div>
    </fieldset>
</div>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Product List</h3>
        <div class="card-tools align-right">
            <button class="btn btn-primary btn-sm py-1" type="button" id="create_new"><img src="extra/add-item.png">  &nbspAdd New</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
			<colgroup>
                <col width="3%">
                <col width="5%">
                <col width="10%">
                <col width="5%">
				<col width="12%">
                <col width="35%">
                <col width="10%">
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
                    <th class="text-center p-0">Description</th>
					<th class="text-center p-0">Expiry</th>
                    <th class="text-center p-0">Price</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT * FROM `product_list` order by `name` asc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1"><?php echo $row['product_code'] ?></td>
                    <td class="py-0 px-1"><?php echo $row['unit'] ?></td>
					<td class="py-0 px-1"><?php if ($row['quantity']<=15){echo "<p class='low'>" . $row['quantity'] . "</p>";}else if ($row['quantity']>15 and $row['quantity']<50){echo "<p class='moderate'>" . $row['quantity'] . "</p>";} else {echo "<p class='high'>" . $row['quantity'] . "</p>";}?></td>
                    <td class="py-0 px-1"><?php echo $row['name'] ?></td>
                    <td class="py-0 px-1"><?php echo $row['description'] ?></td>
					<td class="py-0 px-1"><?php if (substr($row['expiry_date'], 0, 4)-date("Y")<=0){echo "<p class='low'>" . $row['expiry_date'] . "</p>";} else if ((substr($row['expiry_date'], 0, 4)-date("Y")>0) and (substr($row['expiry_date'], 0, 4)-date("Y"))<3){echo "<p class='moderate'>" . $row['expiry_date'] . "</p>";} else {echo "<p class='high'>" . $row['expiry_date'] . "</p>";} ?></td>
                    <td class="py-0 px-1 text-end"><?php echo $row['price'] ?></td>
                    <th class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item edit_data" data-id = '<?php echo $row['product_id'] ?>' href="javascript:void(0)">Edit</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['product_id'] ?>' data-name = '<?php echo $row['name'] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </th>
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
<script>
    $(function(){
        $('#create_new').click(function(){
            uni_modal('Add New product',"manage_products.php")
        })
        $('.edit_data').click(function(){
            uni_modal('Edit product',"manage_products.php?id="+$(this).attr('data-id'))
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
        })

        $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:7 }
            ]
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'Actions.php?a=delete_product',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                consolre.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>