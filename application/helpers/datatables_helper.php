<?php

function edit_button($link) {
    return '<a href="' . $link . '" class="btn btn-warning " title="Editar"><span class="fa fa-edit"></span></a>';
}

function view_button($link) {
    return '<a href="' . $link . '" class="btn btn-info hidden-xs" title="Ver"><span class="fa fa-eye"></span></a>';
}

function money_formating($precio, $nodecimal = false, $dollarsign = true) {
    return ($dollarsign ? '$ ' : '') . ($precio < 0 ? '(-' : '') . number_format(abs($precio), $nodecimal ? 0 : 2, ',', '.') . ($precio < 0 ? ')' : '');
}

function remove_button($onclick) {
    return '<a class="btn btn-danger" onclick="' . $onclick . '" title="Eliminar"><span class="fa fa-remove"></span></a>';
}

function user_datatable($list) {
    $final_list = array();
    if (!empty($list)) {
        foreach ($list as $row) {
            $actions = '';
            if (can("edit_user")) {
                $actions .= edit_button(site_url('manager/user/' . $row['user_id']));
            }

            if (can("manage_cash") && !empty($row['user_payment_type'])) {
                $actions .= '<a class="btn btn-info" title="Liquidar pago" href="' . site_url('settlements/form/' . $row['user_id']) . '"><span class="fa fa-money"></span></a>';
            }

            if (can("delete_user") && $row['user_id'] > 2 && $row['user_active']) {
                $actions .= remove_button('app.deleteUserConfirm(' . $row['user_id'] . ')');
                $actions .= '<a class="btn btn-info" title="Ver Como" href="' . site_url('manager/sessionchange/' . $row['user_id']) . '"><span class="fa fa-eye"></span></a>';
            } else if (!$row['user_active']) {
                $actions .= '<a class="btn bg-olive" onclick="app.activeUserConfirm(' . $row['user_id'] . ')" title="Activar"><span class="fa fa-check"></span></a>';
            }


            $final_list[] = array(
                $row['user_firstname'] . ' ' . $row['user_lastname'],
                $row['user_email'],
                $row['user_rol_label'],
                '<span class="">' . (!empty($row['user_password_reset_time']) && $row['user_password_reset_time'] != '0000-00-00 00:00:00') ? date('d/m/Y G', strtotime($row['user_password_reset_time'])) . ' hs' : '-' . '</span>',
                ($row['user_active'] == 1 ? '<span class="status-center"><i class="fa  fa-check"></i></span>' : '<span class="status-center"><i class="fa  fa-close"></i></span>'),
                '<div class="btn-group">' . $actions . '</div>'
            );
        }
    }
    return $final_list;
}
