
<script src="https://www.payfast.co.za/onsite/engine.js"></script>




<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<?php 
$total = 0;
    $qry = $conn->query("SELECT c.*,p.title,i.price,p.id as pid from `cart` c inner join `inventory` i on i.id=c.inventory_id inner join products p on p.id = i.product_id where c.client_id = ".$_settings->userdata('id'));
    while($row= $qry->fetch_assoc()):
        $total += $row['price'] * $row['quantity'];
    endwhile;
?>
<section class="py-5">
    <div class="container">
        <div class="card rounded-0">
            <div class="card-body"></div>
            <h3 class="text-center"><b>Checkout</b></h3>
            <hr class="border-dark">
            <form action="" id="place_order">
                <input type="hidden" name="amount" value="<?php echo $total ?>">
                <input type="hidden" name="payment_method" value="cod">
                <input type="hidden" name="paid" value="0">
                <div class="row row-col-1 justify-content-center">
                    <div class="col-6">
                    <div class="form-group col mb-0">
                    <label for="" class="control-label">Order Type</label>
                    </div>
                    <div class="form-group d-flex pl-2">
                        <div class="custom-control custom-radio">
                          <input class="custom-control-input custom-control-input-primary" type="radio" id="customRadio4" name="order_type" value="2" checked="">
                          <label for="customRadio4" class="custom-control-label">For Delivery</label>
                        </div>
                        <!-- <div class="custom-control custom-radio ml-3">
                          <input class="custom-control-input custom-control-input-primary custom-control-input-outline" type="radio" id="customRadio5" name="order_type" value="1">
                          <label for="customRadio5" class="custom-control-label">For Pick up</label>
                        </div> -->
                      </div>
                        <div class="form-group col address-holder">
                            <label for="" class="control-label">Delivery Address</label>
                            <textarea required id="" cols="30" rows="3" name="delivery_address" class="form-control" style="resize:none"><?php echo $_settings->userdata('default_delivery_address') ?></textarea>
                        </div>
                        
                        <div class="col">
                            <h6><b>Delivery:
                                <?php
                                    $delivery = 0;
                                    if($total >= 350){
                                        $delivery = 0;
                                        echo "R $delivery";
                                    }
                                    else if($total >= 250){
                                        $delivery = 20;
                                        echo "R $delivery";
                                    }
                                    else if($total >= 150){
                                        $delivery = 25;
                                        echo "R $delivery";
                                    }
                                    else {
                                        $delivery = 30;
                                        echo "R $delivery";
                                    }
                                ?>
                            </b></h6>
                        </div>
                <!-- <hr class="border-dark"> -->
                        <div class="col">
                            <span><h4><b>Grand Total:</b> R <?php 
                                $grandTotal = $total + $delivery;
                            echo number_format($grandTotal) ?></h4></span>
                        </div>
                        <hr>
                        <div class="col my-3">
                        <h4 class="text-muted">Payment Method</h4>
                            <div class="d-flex w-100 justify-content-between">
                                <button class="btn btn-flat btn-dark">Card on Delivery</button> <b>OR</b>
                                
                                <!--  -->
                                <form action="https://sandbox.payfast.co.za/eng/process" method="post">
                                    <input type="hidden" name="merchant_id" value="10000100">
                                    <input type="hidden" name="merchant_key" value="46f0cd694581a">
                                    <input type="hidden" name="return_url" value="https://986cdaa86a51.ngrok.io/return.php">
                                    <input type="hidden" name="cancel_url" value="https://986cdaa86a51.ngrok.io/cancel.php">
                                    <input type="hidden" name="notify_url" value="https://986cdaa86a51.ngrok.io/notify.php">
                                    <input type="hidden" name="amount" value="1">
                                    <input type="hidden" name="item_name" value="Test Product">
                                    <input id="paypal-button" type="submit" value="Pay Now">
                                </form>
                                <!--  -->
                                <!-- <span id="paypal-button"></span> -->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
paypal.Button.render({
    env: 'sandbox', // change for production if app is live,
 
        //app's client id's
	client: {
        sandbox:    'AdDNu0ZwC3bqzdjiiQlmQ4BRJsOarwyMVD_L4YQPrQm4ASuBg4bV5ZoH-uveg8K_l9JLCmipuiKt4fxn',
        //production: 'AaBHKJFEej4V6yaArjzSx9cuf-UYesQYKqynQVCdBlKuZKawDDzFyuQdidPOBSGEhWaNQnnvfzuFB9SM'
    },
 
    commit: true, // Show a 'Pay Now' button
 
    style: {
    	color: 'blue',
    	size: 'small'
    },
 
    payment: function(data, actions) {
        return actions.payment.create({
            payment: {
                transactions: [
                    {
                    	//total purchase
                        amount: { 
                        	total: '<?php echo $grandTotal; ?>', 
                        	currency: 'PHP' 
                        }
                    }
                ]
            }
        });
    },
 
    onAuthorize: function(data, actions) {
        return actions.payment.execute().then(function(payment) {
    		// //sweetalert for successful transaction
    		// swal('Thank you!', 'Paypal purchase successful.', 'success');
            payment_online()
        });
    },
 
}, '#paypal-button');




function payment_online(){
    $('[name="payment_method"]').val("Online Payment")
    $('[name="paid"]').val(1)
    $('#place_order').submit()
}
$(function(){
    $('[name="order_type"]').change(function(){
        if($(this).val() ==2){
            $('.address-holder').hide('slow')
        }else{
            $('.address-holder').show('slow')
        }
    })
    $('#place_order').submit(function(e){
        e.preventDefault()
        start_loader();
        $.ajax({
            url:'classes/Master.php?f=place_order',
            method:'POST',
            data:$(this).serialize(),
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("an error occured","error")
                end_loader();
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                    alert_toast("Order Successfully placed.","success")
                    setTimeout(function(){
                        location.replace('./')
                    },2000)
                }else{
                    console.log(resp)
                    alert_toast("an error occured","error")
                    end_loader();
                }
            }
        })
    })
})
</script>