<?

global $f_title;
$f_title .= "
        <a class='additem btn btn-success fancy-nop-reload' href='{$addLink}?template=282'>Добавить запись</a>
        <a class='additem btn btn-info' href='/user/finansi/finance-history/?year=".date("Y")."&nc_ctpl=298' target='_blank'><i class='glyphicon glyphicon-print'></i> Печатная версия</a>
        &nbsp;&nbsp;&nbsp;
        <a class='btn btn-danger fancy' href='/user/gosts/check/?action=bad_fin_logs&print=1'>Ошибки в логах</a>
        &nbsp;
        <small><label><input type='checkbox' name='no_bank' value='1'".($no_bank ? " checked" : "")." /> Без банка</label></small>
        <small><label><input type='checkbox' name='ktr_error' value='1'".($ktr_error ? " checked" : "")." /> Несовпадение контрагента</label></small>
        ";

if($debug){
    $result .= "<pre>{$message_select}</pre>";
}

$a_orderlist = array();

$i_temp_parent_item = 0;
$s_sql = " 
    SELECT 
        Message_ID, 
        Parent_Message_ID, 
        Name 
    FROM Message202 as st 
";
$b_result = mysql_query($s_sql);
if ($b_result)
    while ($a_row = mysql_fetch_assoc($b_result)) {
        $a_sublist[$a_row['Message_ID']] = $a_row['Name'];
        if (empty($a_row['Parent_Message_ID']))
            $a_parentlist[$a_row['Message_ID']] = $a_row['Name'];
        if (!empty($a_row['Parent_Message_ID']))
            $a_ref[$a_row['Parent_Message_ID']][$a_row['Message_ID']] = $a_row['Name'];
        if (!empty($a_row['Parent_Message_ID']))
            $a_ref_st[$a_row['Parent_Message_ID']][$a_row['Message_ID']] = $a_row['Name'];
        if (!empty($id_item) && $a_row['Message_ID'] == $id_item)
            $i_temp_parent_item = (int)$a_row['Parent_Message_ID'];
    }
if (!($tmpl == 'user_profile' || $cc_settings['view_mode'] == 2 || $tmpl == 'sotrudnik')) {
    
    $owner = $current_user['Parent_User_ID'] ? $current_user['Parent_User_ID'] : $current_user['User_ID'];

    echo "
    <form action='{$subLink}' method='get' class='form-horizontal'>
        <h1>{$f_title}</h1>
        <input type='hidden' name='search' value='1'>
        <div class='row'>
        
            <div class='col-sm-2'>
                <div class='form-group'>
                    <label class='col-sm-4 control-label' for='id_zayavka'>ID заявки:</label>
                    <div class='col-sm-8'>
                        <input  type='text' value='" . ($id_zayavka ? $id_zayavka : "") . "' name='id_zayavka' size='7' name='id_zayavka' class='form-control'>
                    </div>
                </div>
            </div>
            
            <div class='col-sm-3'>
                <div class='form-group'>
                    <label class='col-sm-3 control-label' for='id_user'>
                        Контрагент:
                    </label>
                    <div class='col-sm-9'>
                        <div class='input-group input-group-autoselect'>
                            <div class='input-group-addon nopadding'>
                                <input type='text' class='form-control'/>
                            </div>
                            <select name='id_user' id='id_user' class='form-control'>
                                <option value=''>-- выберите --</option>
                                " . pokupatel_options($id_user, true, true, array($owner)) . "
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class='col-sm-2'>
                <div class='form-group'>
                    <label class='col-sm-3 control-label' for='id_item'>Статья:</label>
                    <div class='col-sm-9'> 
                        <select name='id_item' id='id_item' class='form-control'>
                            <option value=''>-- выберите --</option> ";
                            $s_html = "";
                            natsort($a_parentlist);
                            foreach ($a_parentlist as $i_parentst => $s_parentst) {
                                if (count($a_ref_st[$i_parentst]) > 0) {
                                    $s_html .= "<optgroup label='" . $s_parentst . "'>";
                                    natsort($a_ref_st[$i_parentst]);
                                    foreach ($a_ref_st[$i_parentst] as $i_st => $i_temp_st) {
                                        $s_html .= "<option value='" . $i_st . "' " . ($i_st == $id_item ? " selected='selected' " : "") . ">" . $a_sublist[$i_st] . "</option>";
                                    }
                                    $s_html .= "</optgroup>";
                                } else
                                    $s_html .= "<option value='" . $i_parentst . "'>" . $s_parentst . "</option>";
                            }
                            echo $s_html . " 
                        </select>
                    </div>
                </div>
            </div>
            
            <div class='col-sm-3'>
                <div class='form-group'>
                    <label class='col-sm-2 control-label' for='date1'>Период:</label>
                    <div class='col-sm-10'>
                        <div class='input-group'>
                            <label for='updfrom' class='input-group-addon'>с</label>
                            <input id='date1' class='date form-control' type='text' value='" . ($date1 ? $date1 : "") . "' name='date1' size='20'>
                            <label for='updto' class='input-group-addon'>по</label>
                            <input id='date2' class='date form-control' type='text' value='" . ($date2 ? $date2 : "") . "' name='date2' size='20'>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class='col-sm-2'>
                <div class='form-group'>
                    <label class='checkbox-inline'>
                        <input type='checkbox' name='wrong' id='wrong' value='1'" . ($wrong ? " checked='checked'" : '') . ">
                        Ошибочные записи &nbsp;
                    </label>
                    <input type='submit' value='Поиск' class='btn btn-primary'>
                    <a href='{$subLink}' class='btn btn-default'>Сброс</a>
                </div>
            </div>

        </div>
        
    </form>";
}
$a_items = array();
$a_itemlist = array();
$a_total = array();
$f_total = 0;
?>