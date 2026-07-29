<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3" style="margin-top:40px">
        <div class="panel panel-primary">
          <div class="panel-heading"><h3 class="panel-title">Iniciar sesión</h3></div>
          <div class="panel-body">
            <?php if ($this->session->flashdata('login_error')): ?>
              <div class="alert alert-danger"><?= $this->session->flashdata('login_error') ?></div>
            <?php endif; ?>

            <form id="loginForm" method="post" action="<?= site_url('auth/do_login') ?>" class="form-horizontal" role="form">
              <div class="form-group">
                <label for="email" class="col-sm-3 control-label">Email</label>
                <div class="col-sm-9">
                  <input type="email" name="email" id="email" class="form-control" required>
                </div>
              </div>

              <div class="form-group">
                <label for="password" class="col-sm-3 control-label">Contraseña</label>
                <div class="col-sm-9">
                  <input type="password" name="password" id="password" class="form-control" required>
                </div>
              </div>

              <div id="loginErrors" class="alert alert-danger" style="display:none"></div>

              <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                  <button type="submit" class="btn btn-primary">Entrar</button>
                  <a href="<?= site_url('users') ?>" class="btn btn-default">Volver</a>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    // Optional: submit login via AJAX and show errors inline
    $(function(){
      $('#loginForm').on('submit', function(e){
        e.preventDefault();
        $('#loginErrors').hide().empty();
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: $(this).serialize(),
          dataType: 'json',
          success: function(resp){
            if (resp.success) {
              window.location.href = '<?= site_url('users') ?>';
            } else {
              $('#loginErrors').html(resp.error || 'Error en login').show();
            }
          },
          error: function(){
            $('#loginErrors').html('Fallo en la solicitud').show();
          }
        });
      });
    });
  </script>
</body>
</html>
