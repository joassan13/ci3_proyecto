<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CRUD de Usuarios</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.11/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.1/css/responsive.bootstrap.min.css">
</head>

<body>
  <div class="container-fluid">
    <h2 class="page-header">CRUD de Usuarios (AJAX + jQuery)</h2>
    <hr>
    <h3>Bienvenido, <?= $this->session->userdata('user')['first_name'] ?> <?= $this->session->userdata('user')['last_name'] ?>. ¿Qué deseas hacer?</h3>
    <p>
      <a href="/dbcheck" class="btn btn-default" id="btn-dbcheck">Verificar conexión BD</a>
      <button class="btn btn-primary" id="btn-add">Crear (modal)</button>
      <a href="<?= base_url('users/create_view') ?>" class="btn btn-success" id="btn-add-view">Crear (vista)</a>
      <a href="<?= site_url('logout') ?>" class="btn btn-danger" id="btn-logout">Cerrar sesión</a>
    </p>
    <hr>
    <p>
      <button class="btn btn-default" id="btn-export-csv">Exportar a Excel (.csv)</button>
      <button class="btn btn-default" id="btn-export-pdf">Exportar a PDF</button>
      <button class="btn btn-default" id="btn-download-template">Descargar plantilla (CSV)</button>
      <label class="btn btn-default" style="margin-left:10px">
        Importar CSV <input type="file" id="importFile" style="display:none" accept=".csv">
      </label>
      <button class="btn btn-primary" id="btn-import">Ejecutar importación</button>
    </p>

    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover" id="users-table">
        <thead>
          <tr>
            <th><input type="checkbox" id="select-all"></th>
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
        <canvas id="genderChart"></canvas>
      </div>
      <div class="col-sm-6">
        <canvas id="totalChart"></canvas>
      </div>
    </div>
    <hr>
  </div>

  <!-- Modal -->
  <div id="userModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="modalHeader">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="modalTitle">Crear usuario</h4>
        </div>
        <div class="modal-body">
          <form id="userForm">
            <input type="hidden" name="id" id="user_id">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Nombre</label>
                  <input type="text" name="first_name" id="first_name" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Apellido</label>
                  <input type="text" name="last_name" id="last_name" class="form-control">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Correo</label>
                  <input type="email" name="email" id="email" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Teléfono</label>
                  <input type="text" name="phone" id="phone" class="form-control" maxlength="10">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label>RFC</label>
                  <input type="text" name="rfc" id="rfc" class="form-control" maxlength="13">
                </div>
              </div>
              <div class="col-sm-5">
                <div class="form-group">
                  <label>CURP</label>
                  <input type="text" name="curp" id="curp" class="form-control" maxlength="18">
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group">
                  <label>Sexo</label>
                  <select name="gender" id="gender" class="form-control">
                    <option value="">Seleccione...</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                    <option value="O">Otro</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-7">
                <div class="form-group">
                  <label>Contraseña</label>
                  <input type="password" name="password" id="password" class="form-control" placeholder="">
                  <small class="form-text text-muted">La contraseña debe tener al menos 6 caracteres. Si se deja en blanco, no se cambiará.</small>
                </div>
              </div>
              <div class="col-sm-5">
                <div class="form-group">
                  <label>Algoritmo de hash</label>
                  <select name="hash_algo" id="hash_algo" class="form-control">
                    <option value="bcrypt" selected>bcrypt (recomendado)</option>
                    <option value="sha256">SHA256</option>
                    <option value="sha1">SHA1</option>
                    <option value="md5">MD5</option>
                  </select>
                </div>
              </div>
            </div>
          </form>
          <div id="formErrors" class="alert alert-danger" style="display:none"></div>
          <div>
            <h4>Algunos ejemplos de RFC y CURP para pruebas:</h4>
            <ul>
              <li>RFC: YKSA211220XF8, CURP: GEGN460209MGRFKA97</li>
              <li>RFC: OFSA321017ER6, CURP: VBNJ801105HMCBYT57</li>
              <li>RFC: MAMM8001011H0, CURP: MAMM800101HMCLNS09</li>
            </ul>
          </div>
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
          { data: 'id', orderable: false, searchable: false, render: function(data, type, row) {
              return '<input type="checkbox" class="row-select" data-id="'+row.id+'">';
            }
          },
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
        pageLength: 5
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
        $('#modalHeader').removeClass('bg-info').addClass('bg-success');
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
            $('#modalHeader').removeClass('bg-success').addClass('bg-info');
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

      // Select all toggle
      $('#select-all').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-select').prop('checked', checked);
      });

      // When table redraws, clear select-all
      table.on('draw', function() { $('#select-all').prop('checked', false); });

      function getSelectedIds() {
        var ids = [];
        $('.row-select:checked').each(function() { ids.push($(this).data('id')); });
        return ids;
      }

      // Export CSV (Excel-compatible)
      $('#btn-export-csv').click(function() {
        var ids = getSelectedIds();
        var url = '<?= site_url('export_csv') ?>';
        if (ids.length) url += '?ids=' + ids.join(',');
        window.location = url;
      });

      // Download template
      $('#btn-download-template').click(function() {
        window.location = '<?= site_url('download_template') ?>';
      });

      // Import flow
      $('#btn-import').click(function() {
        var file = $('#importFile')[0].files[0];
        if (!file) { alert('Seleccione un archivo CSV para importar'); return; }
        var formData = new FormData(); formData.append('file', file);
        $.ajax({
          url: '<?= site_url('import_csv') ?>',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(resp) {
            if (resp.success) {
              alert('Importación finalizada. Insertados: ' + resp.inserted + '. Errores: ' + resp.errors.length);
              table.ajax.reload(null, false);
              loadCharts();
            } else {
              alert('Error: ' + (resp.error || 'Desconocido'));
            }
          },
          error: function() { alert('Fallo en la importación'); }
        });
      });

      // Export PDF placeholder (opens a printable view)
      $('#btn-export-pdf').click(function() {
        var ids = getSelectedIds();
        var url = '<?= site_url('export_pdf') ?>';
        if (ids.length) url += '?ids=' + ids.join(',');
        window.open(url, '_blank');
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