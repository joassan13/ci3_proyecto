<h2>Crear usuario</h2>
<form method="post" action="<?= site_url('users/create') ?>">
    <div class="form-group">
        <label for="first_name">Nombre</label>
        <input type="text" name="first_name" id="first_name" class="form-control" value="<?= set_value('first_name') ?>">
    </div>

    <div class="form-group">
        <label for="last_name">Apellido</label>
        <input type="text" name="last_name" id="last_name" class="form-control" value="<?= set_value('last_name') ?>">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="<?= set_value('email') ?>">
    </div>

    <div class="form-group">
        <label for="rfc">RFC</label>
        <input type="text" name="rfc" id="rfc" class="form-control" maxlength="13" value="<?= set_value('rfc') ?>">
    </div>

    <div class="form-group">
        <label for="curp">CURP</label>
        <input type="text" name="curp" id="curp" class="form-control" maxlength="18" value="<?= set_value('curp') ?>">
    </div>

    <div class="form-group">
        <label for="phone">Teléfono</label>
        <input type="text" name="phone" id="phone" class="form-control" maxlength="15" value="<?= set_value('phone') ?>">
    </div>

    <div class="form-group">
        <label for="gender">Sexo</label>
        <select name="gender" id="gender" class="form-control">
            <option value="">Seleccione...</option>
            <option value="M" <?= set_select('gender','M') ?>>Masculino</option>
            <option value="F" <?= set_select('gender','F') ?>>Femenino</option>
            <option value="O" <?= set_select('gender','O') ?>>Otro</option>
        </select>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
