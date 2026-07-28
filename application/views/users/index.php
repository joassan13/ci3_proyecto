<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users CRUD</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="page-header">Users CRUD (AJAX + jQuery)</h2>
    <p>
      <a href="/dbcheck" class="btn btn-default" id="btn-dbcheck">Check DB connection</a>
      <button class="btn btn-primary" id="btn-add">Crear (modal)</button>
      <a href="<?= base_url('users/create_view') ?>" class="btn btn-success" id="btn-add-view">Crear (vista)</a>
    </p>

    <table class="table table-bordered" id="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>First name</th>
                <th>Last name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

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
  </div>

<!-- Modal -->
<div id="userModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="modalTitle">Add User</h4>
      </div>
      <div class="modal-body">
        <form id="userForm">
          <input type="hidden" name="id" id="user_id">
          <div class="form-group">
            <label>First name</label>
            <input type="text" name="first_name" id="first_name" class="form-control">
          </div>
          <div class="form-group">
            <label>Last name</label>
            <input type="text" name="last_name" id="last_name" class="form-control">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="email" class="form-control">
          </div>
          <div class="form-group">
            <label>Phone</label>
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
            <label>Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep existing on edit">
          </div>
          <div class="form-group">
            <label>Hash algorithm</label>
            <select name="hash_algo" id="hash_algo" class="form-control">
              <option value="bcrypt" selected>bcrypt (recommended)</option>
              <option value="sha256">SHA256</option>
              <option value="sha1">SHA1</option>
              <option value="md5">MD5</option>
            </select>
          </div>
        </form>
        <div id="formErrors" class="alert alert-danger" style="display:none"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
function loadUsers() {
  $.ajax({
    url: '<?php echo base_url('users/list'); ?>',
    type: 'GET',
    dataType: 'json',
    success: function(data){
      var tbody = '';
      $.each(data, function(i, u){
        tbody += '<tr>'+
          '<td>'+u.id+'</td>'+
          '<td>'+u.first_name+'</td>'+
          '<td>'+u.last_name+'</td>'+
          '<td>'+u.email+'</td>'+
          '<td>'+ (u.phone||'') +'</td>'+
          '<td><button class="btn btn-xs btn-info btn-edit" data-id="'+u.id+'">Editar (modal)</button> '
          +'<a href="<?= base_url('users/edit_view') ?>/'+u.id+'" class="btn btn-xs btn-primary">Editar (vista)</a> '
          +'<button class="btn btn-xs btn-danger btn-delete" data-id="'+u.id+'">Delete</button></td>'+
          '</tr>';
      });
      $('#users-table tbody').html(tbody);
    },
    error: function(){
      alert('Error loading users');
    }
  });
}

$(function(){
  loadUsers();

  // Load chart data and render charts
  function loadCharts() {
    $.ajax({
      url: '<?= base_url('users/chart_data') ?>',
      type: 'GET',
      dataType: 'json',
      success: function(data){
        var labels = [];
        var counts = [];
        data.by_gender.forEach(function(g){ labels.push(g.gender||'N/A'); counts.push(g.count); });
        var ctx = document.getElementById('genderChart').getContext('2d');
        // if (window.genderChart) window.genderChart.destroy();
        window.genderChart = new Chart(ctx, {
          type: 'pie',
          data: { labels: labels, datasets: [{ data: counts, backgroundColor: ['#36A2EB','#FF6384','#FFCE56'] }] },
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
      error: function(){ console.warn('Failed to load chart data'); }
    });
  }

  // Initial charts
  loadCharts();

    $('#btn-add').click(function(){
        $('#modalTitle').text('Add user');
        $('#userForm')[0].reset();
        $('#user_id').val('');
        $('#formErrors').hide();
        $('#userModal').modal('show');
    });

    $(document).on('click', '.btn-edit', function(){
      var id = $(this).data('id');
      $.ajax({
        url: '<?php echo base_url('users/get'); ?>/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data){
          $('#modalTitle').text('Edit user');
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
        error: function(){ alert('Failed to fetch user'); }
      });
    });

    $('#saveBtn').click(function(){
      var id = $('#user_id').val();
      var url = id ? '<?php echo base_url('users/update'); ?>/'+id : '<?php echo base_url('users/create'); ?>';
      $.ajax({
        url: url,
        type: 'POST',
        data: $('#userForm').serialize(),
        dataType: 'json',
        success: function(resp){
          if (resp.success) {
            $('#userModal').modal('hide');
            loadUsers();
            loadCharts();
          } else {
            var txt = '';
            if (resp.errors) { for (var k in resp.errors) txt += resp.errors[k] + '<br>'; }
            else if (resp.error) txt = resp.error;
            else txt = 'Unknown error';
            $('#formErrors').html(txt).show();
          }
        },
        error: function(){ alert('Save failed'); }
      });
    });

    $(document).on('click', '.btn-delete', function(){
      if (!confirm('Delete this user?')) return;
      var id = $(this).data('id');
      $.ajax({
        url: '<?php echo base_url('users/delete'); ?>/' + id,
        type: 'POST',
        dataType: 'json',
        success: function(resp){ if (resp.success) { loadUsers(); loadCharts(); } else alert('Delete failed'); },
        error: function(){ alert('Delete request failed'); }
      });
    });

    $('#btn-dbcheck').click(function(e){
      e.preventDefault();
      $.ajax({
        url: '<?php echo base_url('dbcheck'); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(r){ if (r.connected) alert('DB connected'); else alert('DB not connected: '+(r.error||'unknown')); },
        error: function(){ alert('DB check failed'); }
      });
    });
});
</script>
</body>
</html>
