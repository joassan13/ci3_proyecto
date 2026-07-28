<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>CRUD de Usuarios</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.11/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.1/css/responsive.bootstrap.min.css">
</head>

<body>
  <div class="container">
    <h2 class="page-header">CRUD de Usuarios (AJAX + jQuery)</h2>
    <p>
      <a href="/dbcheck" class="btn btn-default" id="btn-dbcheck">Verificar conexión BD</a>
      <button class="btn btn-primary" id="btn-add">Crear (modal)</button>
      <a href="<?= base_url('users/create_view') ?>" class="btn btn-success" id="btn-add-view">Crear (vista)</a>
    </p>

    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover" id="users-table">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>RFC</th>
            <th>CURP</th>
            <th>Sexo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <hr>
    <h4>Gráficas</h4>
    <div class="row">
      <div class="col-sm-6">
        <canvas id="genderChart" width="400" height="300"></canvas>
      </div>
      <div class="col-sm-6">
        <canvas id="totalChart" width="400" height="300"></canvas>
      </div>
    </div>
    <hr>
  </div>

  <!-- Modal -->
  <div id="userModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="modalTitle">Crear usuario</h4>
        </div>
        <div class="modal-body">
          <form id="userForm">
            <input type="hidden" name="id" id="user_id">
            <div class="form-group">
              <label>Nombre</label>
              <input type="text" name="first_name" id="first_name" class="form-control">
            </div>
            <div class="form-group">
              <label>Apellido</label>
              <input type="text" name="last_name" id="last_name" class="form-control">
            </div>
            <div class="form-group">
              <label>Correo</label>
              <input type="email" name="email" id="email" class="form-control">
            </div>
            <div class="form-group">
              <label>Teléfono</label>
              <input type="text" name="phone" id="phone" class="form-control">
            </div>
            <div class="form-group">
              <label>RFC</label>
              <input type="text" name="rfc" id="rfc" class="form-control" maxlength="13">
            </div>
            <div class="form-group">
              <label>CURP</label>
              <input type="text" name="curp" id="curp" class="form-control" maxlength="18">
            </div>
            <div class="form-group">
              <label>Sexo</label>
              <select name="gender" id="gender" class="form-control">
                <option value="">Seleccione...</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
                <option value="O">Otro</option>
              </select>
            </div>
            <div class="form-group">
              <label>Contraseña</label>
              <input type="password" name="password" id="password" class="form-control" placeholder="Dejar en blanco para mantener la contraseña actual">
            </div>
            <div class="form-group">
              <label>Algoritmo de hash</label>
              <select name="hash_algo" id="hash_algo" class="form-control">
                <option value="bcrypt" selected>bcrypt (recomendado)</option>
                <option value="sha256">SHA256</option>
                <option value="sha1">SHA1</option>
                <option value="md5">MD5</option>
              </select>
            </div>
          </form>
          <div id="formErrors" class="alert alert-danger" style="display:none"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" id="saveBtn">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.11/js/dataTables.bootstrap.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.1/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.1/js/responsive.bootstrap.min.js"></script>

  <script>
    $(function() {
      // Initialize DataTable using $.ajax in the ajax function
      var table = $('#users-table').DataTable({
        responsive: true,
        processing: true,
        ajax: function(data, callback, settings) {
          $.ajax({
            url: '<?php echo base_url('users/list_users'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
              callback({ data: res });
            },
            error: function() {
              alert('Error al cargar usuarios');
              callback({ data: [] });
            }
          });
        },
        columns: [
          { data: 'first_name' },
          { data: 'last_name' },
          { data: 'email' },
          { data: 'phone', defaultContent: '' },
          { data: 'rfc', defaultContent: '' },
          { data: 'curp', defaultContent: '' },
          { data: 'gender', render: function(d) {
              if (d === 'M') return 'Masculino';
              if (d === 'F') return 'Femenino';
              if (d === 'O') return 'Otro';
              return '';
            }
          },
          { data: null, orderable: false, searchable: false, render: function(data, type, row) {
              return '<button class="btn btn-xs btn-info btn-edit" data-id="'+row.id+'">Editar (modal)</button> '
                + '<a href="<?= base_url('users/edit_view') ?>/'+row.id+'" class="btn btn-xs btn-primary">Editar (vista)</a> '
                + '<button class="btn btn-xs btn-danger btn-delete" data-id="'+row.id+'">Eliminar</button>';
            }
          }
        ],
        lengthMenu: [ [10, 25, 50], [10, 25, 50] ],
        pageLength: 2
      });

      // Charts loader
      function loadCharts() {
        $.ajax({
          url: '<?= base_url('users/chart_data') ?>',
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            var labels = [];
            var counts = [];
            data.by_gender.forEach(function(g) {
              if (g.gender === 'M') labels.push('Masculino');
              else if (g.gender === 'F') labels.push('Femenino');
              else labels.push('Otro');
              counts.push(g.count);
            });
            var ctx = document.getElementById('genderChart').getContext('2d');
            // if (window.genderChart) window.genderChart.destroy();
            window.genderChart = new Chart(ctx, {
              type: 'pie',
              data: {
                labels: labels,
                datasets: [{ data: counts, backgroundColor: ['#FFCE56', '#FF6384', '#36A2EB'] }]
              },
              options: { responsive: true }
            });

            var ctx2 = document.getElementById('totalChart').getContext('2d');
            // if (window.totalChart) window.totalChart.destroy();
            window.totalChart = new Chart(ctx2, {
              type: 'doughnut',
              data: { labels: ['Usuarios'], datasets: [{ data: [data.total], backgroundColor: ['#4BC0C0'] }] },
              options: { responsive: true }
            });
          },
          error: function() { console.warn('No se pudo cargar datos de la gráfica'); }
        });
      }

      loadCharts();

      // New user modal
      $('#btn-add').click(function() {
        $('#modalTitle').text('Crear usuario');
        $('#userForm')[0].reset();
        $('#user_id').val('');
        $('#formErrors').hide();
        $('#userModal').modal('show');
      });

      // Edit (delegated)
      $('#users-table tbody').on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        $.ajax({
          url: '<?php echo base_url('users/get'); ?>/' + id,
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            $('#modalTitle').text('Editar usuario');
            $('#user_id').val(data.id);
            $('#first_name').val(data.first_name);
            $('#last_name').val(data.last_name);
            $('#email').val(data.email);
            $('#phone').val(data.phone);
            $('#rfc').val(data.rfc || '');
            $('#curp').val(data.curp || '');
            $('#gender').val(data.gender || '');
            $('#formErrors').hide();
            $('#userModal').modal('show');
          },
          error: function() { alert('Error al obtener usuario'); }
        });
      });

      // Save (create/update)
      $('#saveBtn').click(function() {
        var id = $('#user_id').val();
        var url = id ? '<?php echo base_url('users/update'); ?>/' + id : '<?php echo base_url('users/create'); ?>';
        $.ajax({
          url: url,
          type: 'POST',
          data: $('#userForm').serialize(),
          dataType: 'json',
          success: function(resp) {
            if (resp.success) {
              $('#userModal').modal('hide');
              table.ajax.reload(null, false);
              loadCharts();
            } else {
              var txt = '';
              if (resp.errors) {
                for (var k in resp.errors) txt += resp.errors[k] + '<br>';
              } else if (resp.error) txt = resp.error;
              else txt = 'Error desconocido';
              $('#formErrors').html(txt).show();
            }
          },
          error: function() { alert('Error al guardar'); }
        });
      });

      // Delete (delegated)
      $('#users-table tbody').on('click', '.btn-delete', function() {
        if (!confirm('¿Eliminar este usuario?')) return;
        var id = $(this).data('id');
        $.ajax({
          url: '<?php echo base_url('users/delete'); ?>/' + id,
          type: 'POST',
          dataType: 'json',
          success: function(resp) {
            if (resp.success) {
              table.ajax.reload(null, false);
              loadCharts();
            } else alert('Error al eliminar');
          },
          error: function() { alert('Fallo en la solicitud de eliminación'); }
        });
      });

      // DB check
      $('#btn-dbcheck').click(function(e) { e.preventDefault();
        $.ajax({ url: '<?php echo base_url('dbcheck'); ?>', type: 'GET', dataType: 'json',
          success: function(r) { if (r.connected) alert('Base de datos conectada'); else alert('BD no conectada: ' + (r.error || 'desconocido')); },
          error: function() { alert('Fallo al verificar BD'); }
        });
      });
    });
  </script>
</body>

</html>