<?
$a_itemlist[$f_id_item] = $rashodname;
if (!empty($f_id_zayavka) && empty($a_financelogs[$f_RowID]['reportdate'])) {
    $a_order_total[$f_id_item][$f_id_zayavka]['sum'] += $f_sum;
    $a_order_total[$f_id_item][$f_id_zayavka]['tax'] += $f_tax;
    $a_total[$f_id_item]['sum'] += $f_sum;
    $a_total[$f_id_item]['tax'] += $f_tax;
    if ($financetype == 1) {
        $f_total += $f_sum;
        $f_total_tax += $f_tax;
    }
    if ($financetype == 2) {
        $f_total -= $f_sum;
        $f_total_tax -= $f_tax;
    }
}
switch ($cc_settings['view_mode']) {
    case 2 :
        if (!empty($a_ref_date_zayavka[$f_id_item][$f_id_zayavka]))
            if ($a_ref_date_zayavka[$f_id_item][$f_id_zayavka] > mktime(0, 0, 0, $f_reportdate_month, $f_reportdate_day, $f_reportdate_year)) {
                $a_ref_date_zayavka[$f_id_item][$f_id_zayavka] = mktime(0, 0, 0, $f_reportdate_month, $f_reportdate_day, $f_reportdate_year);
            } else {
                $a_ref_date_zayavka[$f_id_item][$f_id_zayavka] = mktime(0, 0, 0, $f_reportdate_month, $f_reportdate_day, $f_reportdate_year);
            }
        else
            $a_ref_date_zayavka[$f_id_item][$f_id_zayavka] = mktime(0, 0, 0, $f_reportdate_month, $f_reportdate_day, $f_reportdate_year);
        break;
}

$a_financelogs[$f_RowID]['editLink'] = $editLink;
$a_financelogs[$f_RowID]['deleteLink'] = $deleteLink;

$a_financelogs[$f_RowID]['financetype'] = $financetype;
$a_financelogs[$f_RowID]['name'] = $f_naimenovanie;
$a_financelogs[$f_RowID]['reportdate'] = $f_reportdate_day . "." . $f_reportdate_month . "." . $f_reportdate_year;
$f_sum = str_replace(",", ".", $f_sum);
$f_tax = str_replace(",", ".", $f_tax);
$a_financelogs[$f_RowID]['sum'] = $f_sum;
$a_financelogs[$f_RowID]['moneytype'] = $f_moneytype_ID;
$a_financelogs[$f_RowID]['tax'] = $f_tax;
$a_financelogs[$f_RowID]['id_cc'] = $f_id_cc;
$a_financelogs[$f_RowID]['platezhka_number'] = $f_platezhka_number;
$a_financelogs[$f_RowID]['reportdate_unix'] = mktime(0, 0, 0, $f_reportdate_month, $f_reportdate_day, $f_reportdate_year);
$a_financelogs[$f_RowID]['taxtype'] = $f_taxtype;
$a_financelogs[$f_RowID]['cc'] = $cc;
$a_financelogs[$f_RowID]['sub'] = $sub;
$a_financelogs[$f_RowID]['id_to'] = $f_id_to;
$a_financelogs[$f_RowID]['id_from'] = $f_id_from;
$a_financelogs[$f_RowID]['Parent_User_ID'] = $parent_company;

$a_financelogs[$f_RowID]['creator'] = $creator;
$a_financelogs[$f_RowID]['editor'] = $editor;
$a_financelogs[$f_RowID]['id_pp'] = $id_pp;

if (!empty($f_id_zayavka))
    $a_companylist[$f_id_zayavka] = $id_deliver;
if (empty($f_id_zayavka))
    $f_id_zayavka = "none";
$a_financelogs[$f_RowID]['id_zayavka'] = $f_id_zayavka;
$a_ref_item_zayavka[$f_id_item][$f_id_zayavka] = $f_id_zayavka;
$a_ref_zayavka_item[$f_id_zayavka][$f_RowID] = $f_RowID;
$a_order_numbers[$f_id_zayavka] = $user_order_id;
$a_ref_item_log[$f_id_item][$f_RowID] = $f_RowID;
$a_itemlist[$f_id_item] = $f_id_item;
$a_order_count[$parent_item_id][$order_id] = 1;
$orders_ids[$order_id] = $order_id;
?>
