
<style>
    #computation-pane {
        font-size:1.9rem !important;
    }
	body {
		background-image: url("extra/bg.jpg");
		background-size: cover;
		font-family: Bahnschrift;
	}
	.card {
		background-color: #DCF0ED;
		border: 1px solid #76d5cb;
		box-shadow: 10px 10px #76d5cb;
		border-right-width: 0px;
		width: 100%;
	}
	tr {
		background-color: #76d5cb;
	}
	#table-cont {
		background-color: #DCF0ED;
		border: 1px solid #76d5cb;
	}
	.col-4 {
		background-color: #76d5cb;
	}
</style>
<div class="d-flex flex-column h-100">
<div class="w-100 d-flex justify-content-between" id="pos-header">
<h3>POS</h3>
</div>
<form action="" id="pos-form" class="h-100 flex-grow-1">
<div class="card h-100">
    <div class="card-body d-flex w-100 h-100 py-0 px-0">
        <div class="col-8 d-flex flex-column p-2">
        <div class="d-flex align-items-center mb-1">
                <label for="customer_name" class="control-label col-3 me-2">Customer Name</label>
                <input type="text" autocomplete="off" class="form-control form-control-sm py-0 control-sm rounded-0" id="customer_name" name="customer_name" value="Guest">
            </div>
            <div class="d-flex align-items-center">
                <label for="product_code" class="control-label col-3 me-2">Enter Product Code</label>
                <input type="text" autocomplete="off" autofocus class="form-control form-control-sm control-sm rounded-0" id="product_code">
            </div>
            <div class="flex-grow-1 bg-gradient bg-opacity-25 mt-4" id="table-cont">
                <table class="table table-hover table-striped table-bordered" id="item-list">
                    <colgroup>
                        <col width="5%">
                        <col width="10%">
						<col width="10%">
                        <col width="10%">
                        <col width="25%">
                        <col width="20%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr class="bg-gradient">
                            <th class="py-0 px-1"></th>
                            <th class="py-0 px-1">QTY</th>
							<th class="py-0 px-1">Available</th>
                            <th class="py-0 px-1">Unit</th>
                            <th class="py-0 px-1">Product</th>
                            <th class="py-0 px-1">Unit Price</th>
                            <th class="py-0 px-1">Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="col-4 d-flex flex-column bg-gradient h-100 p-2">
            <div class="w-100 flex-grow-0">
                <fieldset class="border border-light p-1">
                    <legend class="w-auto float-none fs-5">Keyboard Shortcuts</legend>
                    <label for="" class="fs-6">Ctrl + F1 = Focuses the Product Code Text Field.</label>
                    <label for="" class="fs-6">Ctrl + F2 = Focuses the Discount % Text Field.</label>
                    <label for="" class="fs-6">Ctrl + F3 = Tender Amount.</label>
                </fieldset>
            </div>
            <div class="w-100 flex-grow-1 computaion-pane">
                <div class="w-100 d-flex align-items-end h-100">
                    <div class="col-12">
                        <div class="row gx-0 mb-3">
                            <div class="col-3">SubTotal</div>
                            <div class="col-9 text-end bg-light px-1" id="sub_total">0.00</div>
                        </div>
                        <div class="row gx-0 mb-3">
                            <div class="col-3">Discount %</div>
                            <div class="col-9 text-end bg-light px-1" contenteditable id="disc_perc">0</div>
                            <input type="hidden" name="disc_perc" value="0">
                        </div>
                        <div class="row gx-0 mb-3">
                            <div class="col-3">Discount</div>
                            <div class="col-9 text-end bg-light px-1" id="disc_amount">0</div>
                            <input type="hidden" name="disc_amount" value="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="pt-5 flex-grow-0">
                <h3 class="">Grand Total</h3>
                <div class="w-100 px-1 bg-light text-end" id="grand-total" style="height:10vh;font-size:3rem">0.00</div>
                <input type="hidden" name="total" value="0">
                <input type="hidden" name="amount_tendered" value="0">
                <input type="hidden" name="amount_change" value="0">
            </div>
        </div>
    </div>
</div>
</form>
</div>
	
<script>
$(function(){
    $('#pos-form').submit(function(e){
        e.preventDefault()
        $.ajax({
            url:"save_transaction.php",
            method:'POST',
            data:$(this).serialize(),
            dataType:'json',
            error:err=>{
                console.log(err)
                alert('An error occured.')
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    console.log(resp)
                    alert('An error occured.')
                }
            }
        })
    })
    $(window).on('keydown',function(e){
        if($.inArray(e.which,[112,113,114,115]) > -1 && e.ctrlKey == true){
            e.preventDefault()
            if(e.which == 112){
                $('#product_code').val('').focus()
            }
            if(e.which == 113){
                $('#disc_perc').focus().select()
                document.execCommand('selectAll', false, null)
            }
            if(e.which == 114){
                uni_modal("Payment", "tender_amount.php?total="+$('[name="total"]').val())
            }
        }
    })
    $('#disc_perc').on('input keypress',function(){
        $("input[name='disc_perc']").val($(this).text().replace(/,/gi,''))
        calc_total()
    })
    $('#disc_perc').on('blur',function(){
        var perc = $(this).text() > 0 ? $(this).text() : 0;
        $(this).text(perc)
        $("input[name='disc_perc']").val(perc)
    })
   $('#product_code').autocomplete({
       source:function(request, response){
           $.ajax({
               url:"actions.php?a=searh_prod",
               method:"POST",
               data: {t:request.term},
               dataType:'json',
               error:err=>console.log(err),
               success:function(resp){
                   response(resp)
               }
           })
       },
      create:function(event,ui){
          $(this).data("ui-autocomplete")._renderItem = function(ul,item){
             if(item.id == ''){
                return $('<li class="ui-state-disabled px-1" style="opacity:1 !important">'+item.label+'</li>').appendTo(ul);
             }else{
                 return $('<li>').append("<div>"+item.label+"</div>").appendTo(ul);
             }
          }
      },
      select:function(event,ui){
          var data = ui.item.data
          add_item(data)
          setTimeout(() => {
            $('#product_code').val('')
          }, 100);
      },
        minLength: 2
   })
})
function calc_total(){
    $('input[name="quantity[]"]').each(function(){
        var _total = 0
        var tr = $(this).closest('tr')
        var qty = $(this).val()
        var unit_price = tr.find("input[name='price[]']").val()
            unit_price = unit_price.replace(/,/gi,'')
            _total = parseFloat(qty) * parseFloat(unit_price)
            _total = parseFloat(_total).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:3})
            tr.find('.total-price').text(_total)
    })
    var total = 0;
    $('#item-list tbody .total-price').each(function(){
        var _total = $(this).text()
            _total = _total.replace(/,/gi,'')

        total += parseFloat(_total)
    })
    $('#sub_total').text(parseFloat(total).toLocaleString("en-US",{style:'decimal',maximumFractionDigits:2, minimumFractionDigits:2}))

    var disc_amount = 0 ;
    var disc_perc = $('[name="disc_perc"]').val()
        disc_perc = disc_perc > 0 ? disc_perc : 0;
        disc_amount = parseFloat(total) * parseFloat(disc_perc/100);
    $('[name="disc_amount"]').val(disc_amount.toFixed(2))
    $('#disc_amount').text(parseFloat(disc_amount).toLocaleString("en-US",{style:'decimal',maximumFractionDigits:2, minimumFractionDigits:2}))
    total = parseFloat(total) - parseFloat(disc_amount.toFixed(2))
    $('#grand-total').text(parseFloat(total).toLocaleString("en-US",{style:'decimal',maximumFractionDigits:2, minimumFractionDigits:2}))
    $('input[name="total"]').val(parseFloat(total))
}
function rem_item(_this){
    _this.closest('tr').remove();
    calc_total()
}

function add_item($data){
    var tr = $('<tr>')
        tr.attr('data-id',$data.product_id)
    if($('#item-list tbody tr[data-id="'+$data.product_id+'"]').length > 0){
        var o_qty = $('#item-list tbody tr[data-id="'+$data.product_id+'"]').find('input[name="quantity[]"]').val()
        $('#item-list tbody tr[data-id="'+$data.product_id+'"]').find('[name="quantity[]"]').val(parseFloat(o_qty)+parseFloat($data.qty))
        calc_total()
        return false;
    }
    var inputs = $('<div class="d-none">')
        inputs.append('<input type="hidden" name="product_id[]" value="'+$data.product_id+'"/>')
        inputs.append('<input type="hidden" name="unit[]" value="'+$data.unit+'"/>')
        inputs.append('<input type="hidden" name="price[]" value="'+$data.price+'"/>')
    tr.append('<td class="p-1 text-center"><a class="btn btn-danger btn-sm rounded-0" onclick="rem_item($(this))">X</a>'+inputs.html()+'</td>')
    tr.append('<td class="p-1 text-center"><input class="w-100 text-center" type="number" name="quantity[]" value="'+$data.qty+'" min="0" max="'+$data.quantity+'"/></td>')
	tr.append('<td class="p-1 text-end">'+(parseFloat($data.quantity).toLocaleString('en-US'))+'</td>')
    tr.append('<td class="p-1 text-center">'+$data.unit+'</td>')
    tr.append('<td class="p-1"><p class="truncate-1">'+$data.name+'</p></td>')
    tr.append('<td class="p-1 text-end">'+(parseFloat($data.price).toLocaleString('en-US'))+'</td>')
    tr.append('<td class="p-1 text-end total-price">'+(parseFloat($data.price * $data.qty).toLocaleString('en-US'))+'</td>')
    $('#item-list tbody').append(tr)
    calc_total()
    tr.find('input[name="quantity[]"]').on('input keypress',function(){
        calc_total()
    })
}
</script>