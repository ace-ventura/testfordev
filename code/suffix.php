<?php

$company = $current_user['company'];

$result .= "
<div class='" . ($tmpl == "user_profile" ? "accounts" : "tabs") . "' role='tabpanel'>
    <ul class='nav nav-tabs' role='tablist'>";

if ($tmpl != "user_profile") {
    if (count($a_companylist) > 0) {
        /*$s_sql = " 
            SELECT  DISTINCT
                a.Message_ID as Order_ID,
                Order_Status.zakaz_type_list_Name as Order_Status_Name,
                b.id_user, 
                CONCAT (sobst.sobstvennost_form_Name,' «',b.companyname, '»') as company
            FROM Message149 as a
                LEFT JOIN Classificator_zakaz_type_list as Order_Status
                    ON a.Status = Order_Status.zakaz_type_list_ID
                LEFT JOIN Message122 as b 
                    ON a.id_deliver = b.id_user 
                LEFT JOIN Classificator_Sobstvennost_Form as sobst 
                    ON sobst.sobstvennost_form_ID = b.sobstvennost_form
            WHERE b.id_user IN (" . implode(",", $a_companylist) . ") 
                ";*/
        $s_sql = " 
            SELECT  DISTINCT
                a.Message_ID as Order_ID,
                Order_Status.zakaz_type_list_Name as Order_Status_Name
            FROM Message149 as a
                LEFT JOIN Classificator_zakaz_type_list as Order_Status
                    ON a.Status = Order_Status.zakaz_type_list_ID
            WHERE a.Message_ID IN (" . implode(", ", $orders_ids) . ")";
            
        $b_result = $nc_core->db->get_results($s_sql, ARRAY_A);
        if(!empty($b_result)){
            foreach ($b_result as $a_row) {
                //$a_company_data[$a_row['id_user']] = stripslashes($a_row['company']);
                $a_order_status[$a_row['Order_ID']] = $a_row['Order_Status_Name'];
            }
        }
        
        $a_company_data = company_by_ids2($a_companylist);
    }
}
if (count($a_parentlist) > 0) {
    natsort($a_parentlist);
    $i_counter = 1;
    $first_active = 0;
    foreach ($a_parentlist as $i_parentid => $s_parentname) {
        if (!empty($i_temp_parent_item) && ($i_parentid == $i_temp_parent_item) || empty($i_temp_parent_item)) {
            if(count($a_order_count[$i_parentid]) > 0 && $first_active == 0){
                $first_active = $i_counter;
            }
            $result .= "
            <li role='presentation'" . ($i_counter == $first_active ? " class='active'" : "") . ">
                <a role='tab' data-toggle='tab' href='#" . ($tmpl == "user_profile" ? "account" : "tab") . "-" . $i_counter . "' " . (count($a_order_count[$i_parentid]) > 0 ? " class='text-danger'" : "") . ">
                    " . $s_parentname . (count($a_order_count[$i_parentid]) ? ' (' . count($a_order_count[$i_parentid]) . ')' : '') . "
                </a>
            </li>";
            $i_counter++;
        }
    }
}
$result .= "</ul><div class='tab-content'>";
$i_counter = 1;

if (count($a_parentlist) > 0) {
    foreach ($a_parentlist as $i_parentid => $s_parentname) {
        if (!empty($i_temp_parent_item) && ($i_parentid == $i_temp_parent_item) || empty($i_temp_parent_item)) {
            $result .= "
            <div  role='tabpanel' class='tab-pane" . ($i_counter == $first_active ? " active" : "") . "' id='" . ($tmpl == "user_profile" ? "account" : "tab") . "-" . $i_counter . "'>
                <table class='table minetable' id='minetable'>
                    <thead>
                        <tr class='active'>
                            <th class='id'>№</th> 
                            <th class='date'>Дата операции</th> 
                            <th class='name'>Наименование</th> 
                            <th class='date'>Номер платежки</th> 
                            <th class='company'>Списано</th>
                            <th class='company'>Зачислено</th>
                            <th class='sum'>Сумма (без&nbsp;налога)</th> 
                            <th class='sum'>Налог</th> 
                            <th class='sum'>Сумма (с&nbsp;налогом)</th>
                            <th class='nobr'>По банку</th>
                            " . ($tmpl == 'userprofile' ? "" : "<th>Объект</th>")."
                            <th>Создатель<br/>Редактор</th>
                            " . ($tmpl == 'userprofile' ? "" : "<th class='buttons'></th>")." 
                        </tr>
                    </thead>
                    <tbody>";
            if (count($a_ref_st[$i_parentid]) > 0) {
                ksort($a_ref_st[$i_parentid]);
                foreach ($a_ref_st[$i_parentid] as $i_id_item => $s_st) {
                    if (!empty($id_item) && ($i_id_item == $id_item) || empty($id_item)) {

                        $result .= "<tr><td colspan='" . ($tmpl == 'userprofile' ? "9" : "12") . "'>";
                        $result .= "<h3 class='text-center'>" . $s_st . "</h3>";
                        $result .= "</td></tr>";
                        if (count($a_ref_item_log[$i_id_item]) > 0) {
                            $a_temp_log_list = array();
                            foreach ($a_ref_item_log[$i_id_item] as $i_id_log => $i_temp_id_log) {
                                $a_temp_log_list[$a_financelogs[$i_id_log]['id_zayavka']][$i_id_log] = $i_id_log;
                            }
                            krsort($a_temp_log_list);
                            foreach ($a_temp_log_list as $i_id_zayavka => $a_id_log) {

                                if ($_GET['debug3'] and $i_counter == 5) {
                                    print_r($a_temp_log_list);
                                }

                                if (!empty($i_id_zayavka) && $i_id_zayavka <> "none") {
                                    $result .= "
                                    <tr class='active'>
                                        <td colspan='" . ($tmpl == 'userprofile' ? "10" : "13") . "'>
                                            <h4>".($i_id_zayavka == 1000000 ? "Без привязки к заказу" : "
                                                <a class='popup' href='/user/crm/scheta/?&order={$i_id_zayavka}&nc_ctpl=257'>" . $a_order_numbers[$i_id_zayavka] . "</a>&nbsp;{$a_company_data[$a_companylist[$i_id_zayavka]]['name']}, статус заказа: {$a_order_status[$i_id_zayavka]}
                                                ")."</h4>
                                        </td>
                                    </tr>";
                                }
                                if (count($a_id_log) > 0) {
                                    foreach ($a_id_log as $i_item => $i_temp_item) {
                                        $disabled = 1;
                                        $log = $a_financelogs[$i_item];
                                        if($log['Parent_User_ID'] == $company){
                                            $disabled = 0;
                                        }
                                        
                                        $result .= " 
                                                <tr> ";
                                        $result .= " 
                                                    <td class='id'>" . $i_item . "</td> 

                                                    <td class='date'>" . $log['reportdate'] . "</td> ";
                                        $result .= " 
                                                    <td class='name'>
                                                        <span class=''>{$log['name']}</span>
                                                        ".($log['id_from'] != $company && $log['id_to'] != $company ? "<b class='text-danger' style='font-size:14px;'>Ошибка: и оправитель и получатель не маткомпания</b>" : '')."
                                                    </td>
                                                    <td class='date'><span class=''>" . $log['platezhka_number'] . "</span></td>
                                                    <td class='company'>";
                                        $result .= ($log['id_from'] ? $a_userlist_name[$log['id_from']] : "") . "</td>
                                                                                   <td class='company'>" . ($log['id_to'] ? $a_userlist_name[$log['id_to']] : "");
                                        $result .= "</td>";
                                        $log['tax'] = (float)$log['tax'];
                                        $log['sum'] = (float)$log['sum'];
                                        $result .= "<td class='sum nobr'><b>" . (!empty($log['tax']) || ($log['taxtype'] == 1) ? "<span class='text-success'>" . number_format($log['sum'], 2, ",", " ") . "</span>" : "<span class='text-danger'>" . number_format($log['sum'], 2, ",", " ") . "</span>") . "</b></td>
                                                    <td class='sum nobr'><b>" . (!empty($log['tax']) || ($log['taxtype'] == 1) ? "<span class='text-success'>" . number_format($log['tax'], 2, ",", " ") . "</span>" : "<span class='text-danger'>" . number_format($log['tax'], 2, ",", " ") . "</span>") . "</b></td>
                                                    <td class='sum nobr'><b>" . (!empty($log['tax']) || ($log['taxtype'] == 1) ? "<span class='text-success'>" . number_format(($log['sum'] + $log['tax']), 2, ",", " ") . "</span>" : "<span class='text-danger'>" . number_format(($log['sum'] + $log['tax']), 2, ",", " ") . "</span>") . "</b>
                                                        ".($log['id_pp'] ? '<br>привязан' : '')."
                                                    </td>
                                                    <td class='nobr'>".($log['bank_report_sum'] ? "<span".((float)$log['bank_report_sum'] == $log['sum'] + $log['tax'] ? " class='label label-success'" : "").">".number_format($log['bank_report_sum'], 2, ",", " ")."</span>" : "")."</td>";
                                        
                                        if($tmpl != 'userprofile'){
                                            $result .= "<td>" . ($a_cc_list[$log['id_cc']]) . "</td> ";
                                        }
                                        $result .= "<td class='nobr'>
                                            {$log['creator']}
                                            ".($log['editor'] != $log['creator'] ? "<br/>{$log['editor']}" : "")."
                                        </td>";
                                        if($tmpl != 'userprofile'){
                                            $result .= "
                                                                    <td class='buttons text-center'>
                                                        " . ($template == 195 || $tmpl == "user_profile" || $cc_settings['view_mode'] == 2 ? "" : " 
                                                        ".(0 ? "<a href='/netcat/message.php?&message={$i_item}&sub={$log['sub']}&cc={$log['cc']}&catalogue=1&template=281' class='btn btn-default btn-xs ttip' title='Редактировать'><i class='glyphicon glyphicon-pencil'></i></a>
                                                            <a href='/netcat/message.php?&message={$i_item}&sub={$log['sub']}&cc={$log['cc']}&catalogue=1&template=281&delete=1' class='btn btn-default btn-xs ttip' title='Удалить'><i class='glyphicon glyphicon-trash'></i></a>
                                                        " : "
                                                            <span".($disabled ? " class='ttip' title='Вы не можете изменить этот лот'" : "").">
                                                                <a href='".($disabled ? "" : "{$log['editLink']}?template=282")."' class='btn btn-default btn-xs fancy-nop-reload ttip".($disabled ? " disabled" : "")."' title='Редактировать'><i class='glyphicon glyphicon-pencil'></i></a>
                                                                <a href='".($disabled ? "" : "{$log['deleteLink']}?template=282")."' class='btn btn-default btn-xs fancy-nop ttip".($disabled ? " disabled" : "")."' title='Удалить'><i class='glyphicon glyphicon-trash'></i></a>
                                                            </span>
                                        
                                                        ")." 
                    
                                                        ") . " 
                                                    </td>";
                                        }
                                                    
                                        $result .= " 
                                                </tr> 
                                                     ";
                                    }
                                }

                                $result .= (empty($a_order_total[$i_id_item][$i_id_zayavka]['sum']) && empty($a_order_total[$i_id_item][$i_id_zayavka]['tax']) ? "" : "
                                    <tr> 
                                        <td colspan='6' align='right'>Итого по заявке:</td>
                                        <td class='sum text-left nobr'><b>" . number_format($a_order_total[$i_id_item][$i_id_zayavka]['sum'], 2, ",", " ") . "</b></td> 
                                        <td class='sum nobr'><b>" . number_format($a_order_total[$i_id_item][$i_id_zayavka]['tax'], 2, ",", " ") . "</b></td> 
                                        <td class='sum nobr'><b>" . number_format($a_order_total[$i_id_item][$i_id_zayavka]['sum'] + $a_order_total[$i_id_item][$i_id_zayavka]['tax'], 2, ",", " ") . "</b></td>
                                        
                            " . ($tmpl == 'userprofile' ? "" : " 
                                        <td colspan='4'></td>") . " 
                                    </tr>");
                            }
                        }
                        $result .= " 

                                    <tr> 
                                        <td colspan='6' align='right'>Итого по статье:</td>
                                        <td class='sum text-left nobr' colspan=''><b>" . number_format($a_total[$i_id_item]['sum'], 2, ",", " ") . "</b></td> 
                                        <td class='sum nobr'><b>" . number_format($a_total[$i_id_item]['tax'], 2, ",", " ") . "</b></td> 
                                        <td class='sum text-left nobr'><b>" . number_format($a_total[$i_id_item]['sum'] + $a_total[$i_id_item]['tax'], 2, ",", " ") . "</b></td> 
                            " . ($tmpl == 'userprofile' ? "" : " 
                                        <td colspan='4'></td> 
                                    ") . " 
                                    </tr> 

                                    ";
                    }
                }
            }
            $result .= "
                </tbody>
            </table></div>";
            $i_counter++;
        }
    }
} else {
    $result .= "<p class='alert alert-info'>Записей нет.</p>";
}
/*
 $result .= "
 </div> ";
 */
if ($tmpl == 'user_profile') {
    $result .= "";
} else {
}

$result .= "
    </div>
</div>";

if($totRows == 0 && $search){
    $result .= "<div class='alert alert-danger'>Ничего не найдено</div>";
}
