<style>
    #uni_modal .modal-content>.modal-footer,#uni_modal .modal-content>.modal-header{
        display:none;
    }
</style>

<?php 
    require_once('config.php'); 
    $action = "";
    $remeber = "";
    if (!isset($_GET['remember_token']))
        header("Location: index.php");
    else{
        $remeber = $_GET['remember_token'];
        $action = 'http://localhost/farm_ville/reset_password.php?remember_token='.$_GET['remember_token'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php') ?>
<body>
<?php 
    require_once('inc/topBarNav.php');
    // $page = isset($_GET['p']) ? $_GET['p'] : 'home';

    // if(!file_exists($page.".php") && !is_dir($page)){
    //     include '404.html';
    // }else{
    //     if(is_dir($page))
    //         include $page.'/reset_password.php';
    //     // else
    //     //     include $page.'.php';

    // }

    if (isset($_POST['reset'])){
        
    }else{
        echo "Test 2";
    }
        // print_r($_POST);
?>

<header class="bg-dark py-5" id="main-header">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Health is the New Wealth.</h1>
            <p class="lead fw-normal text-white-50 mb-0">Shop Now!</p>
        </div>
    </div>
</header>

  <div class="container-fluid py-5">
      
      <div class="row">
          <div class="col-lg-12">
              <h3 class="text-center">Reset Password</h3>
              <hr>
              <form action="" id="reset-password">
                  <div class="form-group">
                      <label for="password" class="control-label">New Password</label>
                      <input type="password" class="form-control form" name="password" id="password" required>
                  </div>
                  <div class="form-group">
                      <label for="conf_password" class="control-label">Confirm Password</label>
                      <input type="password" class="form-control form" name="conf_password" id="conf_password" required>
                  </div>
                  <input type="hidden" name="remember_token" value="<?php echo $remeber?>">
                  <div class="form-group d-flex justify-content-between">
                      <input class="btn btn-primary btn-flat" type="submit" name="reset" value="Reset Password">
                      <!-- <button class="btn btn-primary btn-flat" type="submit" name="reset">Reset Password</button> -->
                  </div>
              </form>
          </div>
      </div>
  </div>

  <?php require_once('inc/footer.php') ?>
  <script>
      $(function(){
          $('#login-show').click(function(){
              uni_modal("","login.php")
          })
          $('#reset-password').submit(function(e){
              e.preventDefault();
              start_loader()
              if($('.err-msg').length > 0)
                  $('.err-msg').remove();
                  console.log(_base_url_+"classes/Login.php?f=reset_password");
              $.ajax({
                  url:_base_url_+"classes/Login.php?f=reset_password",
                  method:"POST",
                  data:$(this).serialize(),
                  dataType:"json",
                  error:err=>{
                      console.log("This is an error")
                      console.log(err)
                      alert_toast("an error occured",'error')
                      end_loader()
                  },
                  success:function(resp){
                      console.log(resp);
                      if(typeof resp == 'object' && resp.status == 'success'){
                          alert_toast("Your password has been successfully reset",'success')
                          setTimeout(function(){
                              location.reload()
                          },2000)
                      }else if(resp.status == 'incorrect'){
                          var _err_el = $('<div>')
                              _err_el.addClass("alert alert-danger err-msg").text("Incorrect Credentials.")
                          $('#login-form').prepend(_err_el)
                          end_loader()
                          
                      }else{
                          console.log(resp)
                          alert_toast("an error occured",'error')
                          end_loader()
                      }
                  }
              })
          })
      })
  </script>
</body>
</html>