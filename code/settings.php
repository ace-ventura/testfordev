<?
global $cc_keyword;

$a_cc_list = array("224" => "входящие накладные", "223" => "ттн", "221" => "исходящие накладные", "232" => "заявка снабжения", "241" => "Бонусы", "219" => "входящие счета");
$id_zayavka = (int)$id_zayavka;
$id_user = (int)$id_user;
$id_item = (int)$id_item;
if ($tmpl == "user_profile") {
    $cc_settings['view_mode'] = 3;
    $i_id_user = (int)$i_id_user;
} elseif ($tmpl == "sotrudnik") {
    $cc_settings['view_mode'] = 4;
    $i_id_user = (int)$i_id_user;
} else {
}
$a_ref = array();
$a_userlist = array();
$s_sql = " 
                SELECT 
                    IF (company.companyname IS NULL, 
                        IF( ch_l.surname IS NULL, 
                            IF( ip_l.surname IS NULL, 
                                IF( st_l.surname IS NULL, 
                                        IF( own.companyname IS NULL, 
                                            CONCAT(  'ИП ', ip_l.surname), 
                                            own.companyname 
                                         ), 
                                        CONCAT( st_l.surname,' ', user.ForumName ) 
                                ), 
                                CONCAT( ip_l.surname,' ', user.ForumName ) 
                             ), 
                                                        CONCAT(ch_l.surname,' ',user.ForumName) 
                                                ), 
                        CONCAT(company.companyname) 
                    ) AS company, 
                    user.User_ID AS id_user, 
                    user.Parent_User_ID AS parent, 
                    IFNULL(ch_l.orguser,IFNULL(ip_l.orguser,IFNULL(company.orguser,''))) as orguser, 
                    user.PermissionGroup_ID 
                FROM User AS user 
                    LEFT JOIN Message122 AS company ON company.id_user = user.User_ID 
                    LEFT JOIN Classificator_Sobstvennost_Form as sobst 
                        ON sobst.sobstvennost_form_ID = company.sobstvennost_form 
                    LEFT JOIN Message123 AS ch_l ON ch_l.id_user = user.User_ID 
                    LEFT JOIN Message120 AS ip_l ON ip_l.id_user = user.User_ID 
                    LEFT JOIN Message121 AS st_l ON st_l.id_user = user.User_ID 
                    LEFT JOIN Message145 AS own ON own.id_user = user.User_ID 
";
$b_result = mysql_query($s_sql);
if ($b_result)
    while ($a_row = mysql_fetch_assoc($b_result)) {
        if (!empty($a_row['orguser']))
            $a_ref_company_orguser[$a_row['orguser']][$a_row['id_user']] = $a_row['id_user'];
        if ($a_row['PermissionGroup_ID'] == 9 || $a_row['PermissionGroup_ID'] == 16 || $a_row['PermissionGroup_ID'] == 19 || $a_row['PermissionGroup_ID'] == 22) {
            $a_userlist['ur'][$a_row['id_user']] = stripslashes($a_row['company']);
        } elseif ($a_row['PermissionGroup_ID'] == 8 || $a_row['PermissionGroup_ID'] == 17 || $a_row['PermissionGroup_ID'] == 20 || $a_row['PermissionGroup_ID'] == 23) {
            $a_userlist['ip'][$a_row['id_user']] = stripslashes($a_row['company']);
        } elseif ($a_row['PermissionGroup_ID'] == 7 || $a_row['PermissionGroup_ID'] == 18 || $a_row['PermissionGroup_ID'] == 21 || $a_row['PermissionGroup_ID'] == 24) {
            $a_userlist['fz'][$a_row['id_user']] = stripslashes($a_row['company']);
        } elseif ($a_row['PermissionGroup_ID'] == 1 || $a_row['PermissionGroup_ID'] == 11) {
            $a_userlist['internet'][$a_row['id_user']] = stripslashes($a_row['company']);
        } else {
            $a_sotr[$a_row['id_user']] = "&nbsp;&nbsp;" . stripslashes($a_row['company']);
            if (!empty($a_row['parent']))
                $a_ref_company_sotr[$a_row['parent']][$a_row['id_user']] = $a_row['id_user'];
        }
        $a_userlist_name[$a_row['id_user']] = stripslashes($a_row['company']);
    }
natsort($a_sotr);
$a_statuslist = array();
$s_sql = " 
    select 
            b.rashod_Name as rashodname, 
            b.rashod_ID as rashodid 
    FROM     Classificator_rashod as b 
";
$b_result = mysql_query($s_sql);
if ($b_result)
    while ($a_row = mysql_fetch_assoc($b_result)) {
        $a_statuslist[$a_row['rashodid']] = $a_row['rashodname'];
    }
$query_join .= " 
    LEFT JOIN Message200 as financetype
       ON financetype.id_item = a.Message_ID 
    LEFT JOIN Message149 as ordertable 
        ON ordertable.Message_ID = a.id_zayavka
    LEFT JOIN Order_Numbers ON (a.id_zayavka = Order_Numbers.Real_Order_ID)
    INNER JOIN Message202 st
        ON a.id_item = st.Message_ID
    LEFT JOIN User as parents
        ON parents.User_ID = a.User_ID
    LEFT JOIN Message121 as parents_data
        ON parents_data.id_user = a.User_ID
    LEFT JOIN User as editor
        ON editor.User_ID = a.LastUser_ID
    LEFT JOIN Message121 as editor_data
        ON editor_data.id_user = a.LastUser_ID
    LEFT JOIN Message242 as pp
        ON a.Message_ID = pp.id_log
    ".(!$ktr_error ? "
    LEFT JOIN TochkaAccInfo as bank_report ON (
        bank_report.BankRecordID = a.bank_report_0 OR
        bank_report.BankRecordID = a.bank_report_1 OR
        bank_report.BankRecordID = a.bank_report_2 OR
        bank_report.BankRecordID = a.bank_report_3 OR
        bank_report.BankRecordID = a.bank_report_4
    )
    " : "")."
";

if ($ktr_error) {
    // Счет связанный с логом через таблицу платежных поручений
    $query_join .= "
        LEFT JOIN Message219 inv
            ON (pp.id_schet = inv.Message_ID)
    ";
    // Счет связанный с логом по номеру заказа, получателю и статье
    $query_join .= "
        LEFT JOIN Message219 inv2
            ON (a.id_item = 7 AND a.id_zayavka = inv2.id_order AND a.id_to = inv2.id_user)
    ";
}



$query_select .= " 
    financetype.id_type as financetype,
    ordertable.id_deliver,
    Order_Numbers.User_Order_ID,
    st.Parent_Message_ID,
    Order_Numbers.Real_Order_ID,
    parents.Parent_User_ID,
    CONCAT(parents_data.surname, ' ', parents.ForumName) as creator,
    CONCAT(editor_data.surname, ' ', editor.ForumName) as editor,
    pp.Message_ID as id_pp
    ".(!$ktr_error ? ",SUM(bank_report.Summa) as bank_report_sum" : "")."
";
$result_vars .= ' 
    $financetype, 
    $id_deliver,
    $user_order_id,
    $parent_item_id,
    $order_id,
    $parent_company,
    $creator,
    $editor,
    $id_pp
    '.(!$ktr_error ? ", $bank_report_sum" : "").'
';

$company = ($current_user['Parent_User_ID'] > 0 ? $current_user['Parent_User_ID'] : $current_user['User_ID']);
$where = array();
$where[] = "a.id_item IS NOT NULL";

if($AUTH_USER_ID != 6148){
    //$where[] = "(a.id_from = $company OR a.id_to = $company)";
    $where[] = "parents.Parent_User_ID = {$company}";
}

$ignore_sub = 1;
$ignore_cc = 1;
$query_order .= " a.reportdate DESC ";
$query_group = "a.Message_ID";

if ($date1 && $date2) {
    $searchdate = $date1 . " - " . $date2;
}

switch ($cc_settings['view_mode']) {
    case 1 :
    default :
        if ($search == 1) {
            if (!empty($id_user) || !empty($id_zayavka) || !empty($id_item) || !empty($searchdate) || $wrong || $no_bank || $ktr_error) {
                if ($searchdate) {
                    $a_temp_search_date = explode("+-+", $searchdate);
                    $a_temp_search_date = explode("-", $a_temp_search_date[0]);
                    $a_temp_sub = explode(".", trim($a_temp_search_date[0]));
                    $where[] = "reportdate >= '" . $a_temp_sub[2] . "-" . $a_temp_sub[1] . "-" . $a_temp_sub[0] . "'";
                    $a_temp_sub = explode(".", trim($a_temp_search_date[1]));
                    $where[] = "reportdate <= '" . $a_temp_sub[2] . "-" . $a_temp_sub[1] . "-" . $a_temp_sub[0] . "'";
                }
                if (!empty($id_user)) {
                    $where[] = "( a.id_to = " . $id_user . " OR a.id_from = " . $id_user . " )";
                }
                if (!empty($id_zayavka)) {
                    $where[] = "( Order_Numbers.User_Order_ID = " . $id_zayavka . " )";
                }
                if (!empty($id_item)) {
                    $id_item = (int)$id_item;
                    if (!empty($id_item))
                        $where[] = "( a.id_item = " . $id_item . " )";
                }
                if ($wrong) {
                    $where[] = "(a.id_from = 0 OR a.id_to = 0 OR (a.id_from <> $company AND a.id_to <> $company))";
                }
                if($no_bank){
                    $where[] = "(
                        a.bank_report_0 IS NULL AND
                        a.bank_report_1 IS NULL AND
                        a.bank_report_2 IS NULL AND
                        a.bank_report_3 IS NULL AND
                        a.bank_report_4 IS NULL
                    )";
                }

                if ($ktr_error) {
                    $where[] = "(
                    (inv.Message_ID IS NOT NULL AND a.id_to <> inv.id_user) OR
                    (a.id_item = 11 AND (a.id_from <> ordertable.id_deliver OR a.id_to <> Order_Numbers.User_ID)) OR
                    (a.id_item = 7 AND (a.id_from <> Order_Numbers.User_ID OR a.id_to <> inv2.id_user))
                    )";
                }
            } else {
                $where[] = "( 1 OR ordertable.Message_ID IS NOT NULL ) ";
                //    $query_where .= "AND DATE_FORMAT(a.reportdate,'%Y') = ".date("Y");

            }
        } else {
            //    $query_where .= " AND  ( 1 OR ordertable.Message_ID IS NOT NULL ) AND ( a.tax ='' OR a.tax IS NULL ) ";
            $where[] = "( 1 OR ordertable.Message_ID IS NOT NULL ) ";
            //        $query_where .= "AND DATE_FORMAT(a.reportdate,'%Y') = ".date("Y");
            $ignore_all = 1;
        }
        break;
    case 2 :
        $where[] = "TO_DAYS(NOW()) - TO_DAYS(a.Created) <= 3";
        break;
    case 3 :
        if (!empty($i_id_user))
            $where[] = "a.id_to = " . $i_id_user . " OR a.id_from = " . $i_id_user;
        break;
    case 4 :
        if (!empty($i_id_user))
            $where[] = "a.id_to = " . $i_id_user . " OR a.id_from = " . $i_id_user . " AND parentst.Message_ID IN (1)";
        break;
}
$query_where = implode(' AND ', $where);

if ($_GET['debug3']) {
    echo 'where :' . $query_where;
}
?>