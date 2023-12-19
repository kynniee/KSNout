<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `sale_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k)){
                $$k = $v;
            }
        }
        if(isset($user_id) && is_numeric($user_id)){
            $user = $conn->query("SELECT concat(firstname,' ', lastname) as `name` FROM `users` where id = '{$user_id}' ");
            if($user->num_rows > 0){
                $user_name = $user->fetch_array()['name'];
            }
        }
    }else{
        echo '<script> alert("Unknown sale\'s ID."); location.replace("./?page=sales"); </script>';
    }
}else{
    echo '<script> alert("sale\'s ID is required to access the page."); location.replace("./?page=sales"); </script>';
}
?>
<div class="content py-3">
    <div class="card card-outline card-navy rounded-0 shadow">
        <div class="card-header">
            <h4 class="card-title">Số hóa đơn: <b><?= isset($code) ? $code : "" ?></b></h4>
            <div class="card-tools">
                <a href="./?page=sales" class="btn btn-default border btn-sm"><i class="fa fa-angle-left"></i> Quay lại</a>
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid row justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12"  id="printout">
                    <div class="d-flex">
                        <div class="col-auto"><b>Mã hóa đơn:</b></div>
                        <div class="col-auto ps-1 flex-shrink-1 flex-grow-1 border-bottom border-dark"><?= isset($code) ? $code: "" ?></div>
                    </div>
                    <div class="d-flex">
                        <div class="col-auto"><b>Ngày:</b></div>
                        <div class="col-auto ps-1 flex-shrink-1 flex-grow-1 border-bottom border-dark"><?= isset($date_created) ? date("Y-m-d h:i A", strtotime($date_created)): "" ?></div>
                    </div>
                    <div class="mb-2"></div>
                    <h4 class="d-flex border-bottom border-dark">
                        <div class="col-2 text-center">Số lượng</div>
                        <div class="col-7 text-center">Sản phẩm</div>
                        <div class="col-3 text-center">Tổng</div>
                    </h4>
                    <?php if(isset($id)): ?>
                    <?php 
                        $sp_query = $conn->query("SELECT sp.*, p.name as `product` FROM `sale_products` sp inner join `product_list` p on sp.product_id =p.id where sp.sale_id = '{$id}'");
                        while($row = $sp_query->fetch_assoc()):
                    ?>
                    <div class="d-flex border-bottom border-dark">
                        <div class="col-2 text-center"><?= $row['qty'] ?></div>
                        <div class="col-7" style="line-height:.9em">
                            <p class="m-0"><?= $row['product'] ?></p>
                            <p class="m-0"><small>x <?= format_num($row['price']) ?></small></p>
                        </div>
                        <div class="col-3 text-right"><?= format_num($row['price'] * $row['qty']) ?></div>
                    </div>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    <h3 class="d-flex border-top border-dark">
                        <div class="col-4">Tổng</div>
                        <div class="col-8 text-right"><?= isset($amount) ? format_num($amount) : 0 ?></div>
                    </h3>
                    <h5 class="d-flex">
                        <div class="col-5">Chiết khấu</div>
                        <div class="col-7 text-right"><?= isset($tendered) ? format_num($tendered) : 0 ?></div>
                    </h5>
                    <h5 class="d-flex">
                        <div class="col-4">Còn lại</div>
                        <div class="col-8 text-right"><?= isset($amount) && isset($tendered) ? format_num($tendered - $amount) : 0 ?></div>
                    </h5>
                    <h5 class="d-flex">
                        <div class="col-4">Thanh toán</div>
                        <div class="col-8 text-right">
                            <?php 
                            $payment_type = isset($payment_type) ? $payment_type : 0;
                            switch($payment_type){
                                case 1:
                                    echo "Tiền mặt";
                                    break;
                                case 2:
                                    echo "Ví điện tử";
                                    break;
                                case 3:
                                    echo "Thẻ";
                                    break;
                                default:
                                    echo "N/A";
                                    break;
                                }
                            ?>    
                        </div>
                    </h5>
                    <?php if($payment_type > 1): ?>
                    <h5 class="d-flex">
                        <div class="col-4">Payment Code</div>
                        <div class="col-8 text-right"><?= isset($payment_code) ? $payment_code : "" ?></div>
                    </h5>
                    <?php endif; ?>
                    <div class="d-flex">
                        <div class="col-auto"><b>Người thực hiện:</b></div>
                        <div class="col-auto ps-1 flex-shrink-1 flex-grow-1 border-bottom border-dark"><?= isset($user_name) ? ucwords($user_name): "" ?></div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row justify-content-center">
                <a class="btn btn-primary bg-gradient-primary border col-lg-3 col-md-4 col-sm-12 col-xs-12 rounded-pill" href="./?page=sales/manage_sale&id=<?= isset($id) ? $id : '' ?>"><i class="fa fa-edit"></i> Sửa</a>
                <button class="btn btn-light bg-gradient-light border col-lg-3 col-md-4 col-sm-12 col-xs-12 rounded-pill" id="print"><i class="fa fa-print"></i> In</button>
                <button class="btn btn-danger bg-gradient-danger border col-lg-3 col-md-4 col-sm-12 col-xs-12 rounded-pill" id="delete_sale" type="button"><i class="fa fa-trash"></i> Xóa hóa đơn</button>
            </div>
        </div>
    </div>
</div>
<noscript id="print-header">
    <style>
        html, body{
            background:unset !important;
            min-height:unset !important
        }
    </style>
    <div class="d-flex w-100">
        <div class="col-2 text-center">
        </div>
        <div class="col-8 text-center">
            <h4 class="tex-center"><?= $_settings->info('name') ?></h4>
            <h3 class="text-center"><b>Sales Invoice</b></h3>
        </div>
    </div>
    <hr>
</noscript>
<script>
$(function(){
    $('#print').click(function(){
        var head = $('head').clone()
        var p = $($('#printout').html()).clone()
        var phead = $($('noscript#print-header').html()).clone()
        var el = $('<div class="container-fluid">')
        head.find('title').text("Sale Details-Print View")
        el.append(phead)
        el.append(p)
        el.find('.bg-gradient-navy').css({'background':'#001f3f linear-gradient(180deg, #26415c, #001f3f) repeat-x !important','color':'#fff'})
        el.find('.bg-gradient-secondary').css({'background':'#6c757d linear-gradient(180deg, #828a91, #6c757d) repeat-x !important','color':'#fff'})
        el.find('tr.bg-gradient-navy').attr('style',"color:#000")
        el.find('tr.bg-gradient-secondary').attr('style',"color:#000")
        start_loader();
        var nw = window.open("", "_blank", "width=1000, height=900")
                 nw.document.querySelector('head').innerHTML = head.prop('outerHTML')
                 nw.document.querySelector('body').innerHTML = el.prop('outerHTML')
                 nw.document.close()
                 setTimeout(()=>{
                     nw.print()
                     setTimeout(()=>{
                        nw.close()
                        end_loader()
                     },300)
                 },500)
    })
    $('#delete_sale').click(function(){
        _conf("Bạn chắc chắn muốn hóa đơn này?","delete_sale",[])
    })
})
function delete_sale($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_sale",
			method:"POST",
			data:{id: '<?= isset($id) ? $id : "" ?>'},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.replace('./?page=sales');
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>