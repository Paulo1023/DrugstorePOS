<style>
	body {
		background-image: url("extra/bg.jpg");
		background-size: cover;
		font-family: Bahnschrift;
	}
	.card {
		background-color: #DCF0ED;
		border: 1px solid #76d5cb;
		box-shadow: 10px 10px #76d5cb;
	}
	.btn {
		background-color: #76d5cb;
	}
	.card-header {
		background-color: #76d5cb;
	}
</style>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Sales List</h3>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">OR Number</th>
                    <th class="text-center p-0">Customer</th>
					<th class="text-center p-0">Date</th>
                    <th class="text-center p-0">Amount</th>
                    <th class="text-center p-0">Discount</th>
                    <th class="text-center p-0">Total</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT * FROM `transaction_list` order by strftime('%s', `date_created`) desc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1"><?php echo $row['or_number'] ?></td>
                    <td class="py-0 px-1"><?php echo $row['customer_name'] ?></td>
					<td class="py-0 px-1"><?php echo $row['date_created'] ?></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['total_amount'] + $row['discount_amount'],2)?></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['discount_amount'],2) ?></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['total_amount'],2) ?></td>
                    <th class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item view_data" data-id = '<?php echo $row['transaction_id'] ?>' href="javascript:void(0)">View</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['transaction_id'] ?>' data-name = '<?php echo $row['or_number'] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </th>
                </tr>
                <?php endwhile; ?>
                <?php if(!$qry->fetchArray()): ?>
                    <tr>
                        <th class="text-center p-0" colspan="7">No data display.</th>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('.view_data').click(function(){
            uni_modal('Transaction Details',"view_transaction.php?id="+$(this).attr('data-id'),'large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
        })

        $('table').dataTable({
            columnDefs: [
                { orderable: false, targets:6 }
            ]
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'Actions.php?a=delete_transaction',
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