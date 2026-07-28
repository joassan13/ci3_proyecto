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
    <?php if (!isset($user)) {
        echo '<div class="container"><p>Usuario no encontrado.</p></div>';
        return;
    } ?>
    <hr>
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Editar usuario</h3>
                    </div>
                    <div class="panel-body">
                        <?php if (validation_errors()) : ?>
                            <div class="alert alert-danger"><?= validation_errors(); ?></div>
                        <?php endif; ?>

                        <form method="post" action="<?= site_url('users/update/' . $user['id']) ?>" class="form-horizontal" role="form">
                            <div class="form-group">
                                <label for="first_name" class="col-sm-3 control-label">Nombre</label>
                                <div class="col-sm-9">
                                    <input type="text" name="first_name" id="first_name" class="form-control" value="<?= set_value('first_name', $user['first_name']) ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="last_name" class="col-sm-3 control-label">Apellido</label>
                                <div class="col-sm-9">
                                    <input type="text" name="last_name" id="last_name" class="form-control" value="<?= set_value('last_name', $user['last_name']) ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">Correo electrónico</label>
                                <div class="col-sm-9">
                                    <input type="email" name="email" id="email" class="form-control" value="<?= set_value('email', $user['email']) ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="rfc" class="col-sm-3 control-label">RFC</label>
                                <div class="col-sm-9">
                                    <input type="text" name="rfc" id="rfc" class="form-control" maxlength="13" value="<?= set_value('rfc', isset($user['rfc']) ? $user['rfc'] : '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="curp" class="col-sm-3 control-label">CURP</label>
                                <div class="col-sm-9">
                                    <input type="text" name="curp" id="curp" class="form-control" maxlength="18" value="<?= set_value('curp', isset($user['curp']) ? $user['curp'] : '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label">Teléfono</label>
                                <div class="col-sm-9">
                                    <input type="text" name="phone" id="phone" class="form-control" maxlength="15" value="<?= set_value('phone', isset($user['phone']) ? $user['phone'] : '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="gender" class="col-sm-3 control-label">Sexo</label>
                                <div class="col-sm-9">
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="">Seleccione...</option>
                                        <option value="M" <?= set_select('gender', 'M', (isset($user['gender']) && $user['gender'] == 'M')) ?>>Masculino</option>
                                        <option value="F" <?= set_select('gender', 'F', (isset($user['gender']) && $user['gender'] == 'F')) ?>>Femenino</option>
                                        <option value="O" <?= set_select('gender', 'O', (isset($user['gender']) && $user['gender'] == 'O')) ?>>Otro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="col-sm-3 control-label">Contraseña (dejar en blanco para no cambiar)</label>
                                <div class="col-sm-9">
                                    <input type="password" name="password" id="password" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                    <a href="<?= site_url('users') ?>" class="btn btn-default">Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>