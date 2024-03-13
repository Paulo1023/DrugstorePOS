<style>
	body {
		background-image: url("extra/bg.jpg");
		background-size: cover;
		font-family: Bahnschrift;
	}
	.btn {
		background-color: #76d5cb;
		border: 1px solid black;
	}
	.card-header {
		background-color: #76d5cb;
	}
	.card {
		background-color: #DCF0ED;
		border: 1px solid #76d5cb;
		box-shadow: 10px 10px #76d5cb;
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
</style>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Users List</h3>
        <div class="card-tools align-middle">
            <button class="btn btn-primary btn-sm py-1" type="button" id="create_new"><img src="extra/add-user.png">  &nbspAdd New</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="25%">
                <col width="25%">
                <col width="25%">
                <col width="20%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Name</th>
                    <th class="text-center p-0">Username</th>
                    <th class="text-center p-0">Type</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT * FROM `user_list` where user_id != 1 order by `fullname` asc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1"><?php echo $row['fullname'] ?></td>
                    <td class="py-0 px-1"><?php echo $row['username'] ?></td>
                    <td class="py-0 px-1"><?php echo ($row['type'] == 1)? 'Administrator':'Cashier' ?></td>
                    <th class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item edit_data" data-id = '<?php echo $row['user_id'] ?>' href="javascript:void(0)">Edit</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['user_id'] ?>' data-name = '<?php echo $row['fullname'] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </th>
                </tr>
                <?php endwhile; ?>
                <?php if(!$qry->fetchArray()): ?>
                    <tr>
                        <th class="text-center p-0" colspan="5">No data display.</th>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('#create_new').click(function(){
            uni_modal('Add New User',"manage_user.php")
        })
        $('.edit_data').click(function(){
            uni_modal('Edit User Details',"manage_user.php?id="+$(this).attr('data-id'))
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'Actions.php?a=delete_user',
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