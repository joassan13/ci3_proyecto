<h2>Editar usuario</h2>
<?php if (!isset($user)) { echo '<p>Usuario no encontrado.</p>'; return; } ?>
<form method="post" action="<?= site_url('users/update/'.$user['id']) ?>">
    <div class="form-group">
        <label for="first_name">Nombre</label>
        <input type="text" name="first_name" id="first_name" class="form-control" value="<?= set_value('first_name', $user['first_name']) ?>">
    </div>

    <div class="form-group">
        <label for="last_name">Apellido</label>
        <input type="text" name="last_name" id="last_name" class="form-control" value="<?= set_value('last_name', $user['last_name']) ?>">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="<?= set_value('email', $user['email']) ?>">
    </div>

    <div class="form-group">
        <label for="rfc">RFC</label>
        <input type="text" name="rfc" id="rfc" class="form-control" maxlength="13" value="<?= set_value('rfc', isset($user['rfc']) ? $user['rfc'] : '') ?>">
    </div>

    <div class="form-group">
        <label for="curp">CURP</label>
        <input type="text" name="curp" id="curp" class="form-control" maxlength="18" value="<?= set_value('curp', isset($user['curp']) ? $user['curp'] : '') ?>">
    </div>

    <div class="form-group">
        <label for="phone">Teléfono</label>
        <input type="text" name="phone" id="phone" class="form-control" maxlength="15" value="<?= set_value('phone', isset($user['phone']) ? $user['phone'] : '') ?>">
    </div>

    <div class="form-group">
        <label for="gender">Sexo</label>
        <select name="gender" id="gender" class="form-control">
            <option value="">Seleccione...</option>
            <option value="M" <?= set_select('gender','M', (isset($user['gender']) && $user['gender']=='M')) ?>>Masculino</option>
            <option value="F" <?= set_select('gender','F', (isset($user['gender']) && $user['gender']=='F')) ?>>Femenino</option>
            <option value="O" <?= set_select('gender','O', (isset($user['gender']) && $user['gender']=='O')) ?>>Otro</option>
        </select>
    </div>

    <div class="form-group">
        <label for="password">Password (dejar en blanco para no cambiar)</label>
        <input type="password" name="password" id="password" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>
