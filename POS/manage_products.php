<?php
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `product_list` where rowid = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="product-form">
        <input type="hidden" name="id" value="<?php echo isset($product_id) ? $product_id : '' ?>">
        <div class="form-group">
            <label for="product_code" class="control-label">Code</label>
            <input type="text" name="product_code" id="product_code" required class="form-control form-control-sm rounded-0" value="<?php echo isset($product_code) ? $product_code : '' ?>">
        </div>
        <div class="form-group">
            <label for="unit" class="control-label">Unit</label>
            <input type="text" name="unit" id="unit" required class="form-control form-control-sm rounded-0" value="<?php echo isset($unit) ? $unit : '' ?>">
        </div>
        <div class="form-group">
            <label for="quantity" class="control-label">Quantity</label>
            <input type="quantity" name="quantity" id="quantity" required class="form-control form-control-sm rounded-0" value="<?php echo isset($quantity) ? $quantity : '' ?>">
        </div>
        <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" name="name" id="name" required class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : '' ?>">
        </div>
        <div class="form-group">
            <label for="description" class="control-label">Description</label>
            <textarea name="description" id="description" cols="30" rows="3" class="form-control form-control-sm rounded-0"><?php echo isset($description) ? $description : '' ?></textarea>
        </div>
        <div class="form-group">
            <label for="expiry_date" class="control-label">Expiry Date</label>
            <input type="date" name="expiry_date" id="expiry_date" required class="form-control form-control-sm rounded-0" value="<?php echo isset($expiry_date) ? $expiry_date : '' ?>">
        </div>
        <div class="form-group">
            <label for="price" class="control-label">Price</label>
            <input type="number" step="any" name="price" id="price" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($price) ? $price : '' ?>">
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#product-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'Actions.php?a=save_product',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($product_id) ?>" != 1)
                        _this.get(0).reset();
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>