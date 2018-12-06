<?php
error_reporting(E_ALL | E_STRICT);
set_time_limit(0);
header("Content-Type: text/plain; charset=utf-8");
mysql_connect('localhost', '', '') or die('Could not connect');
mysql_select_db('') or die('Could not select db');
$str = addslashes('Lorem ipsum dolor sit amet consectetuer adipiscing elit Nam aliquam consectetuer lorem Aliquam viverra tincidunt ipsum Cras eu tortor sed odio');

// OWNER
$cnt_owners = 1; // кол-во хозяинов домов
$max_objects = 50; // максимальное кол-во объектов
// HOUSE
$cnt_houses = 5; // кол-во домов
// PART
$cnt_parts = 50; // кол-во квартир
// OWNER PAYMENT
$cnt_owner_payment = mt_rand(15,20); // кол-во платежек для дома
// RENTER
$cnt_renter = 50; // общее кол-во арендаторов для данной квартиры, верхняя граница - время когда истекает время owner
// DEBT RENTER_PAYMENT
$cnt_debt_renter_payment = mt_rand(5,10);

// TEMPLATE

mysql_query("TRUNCATE TABLE owner");
mysql_query("TRUNCATE TABLE house");
mysql_query("TRUNCATE TABLE part");
mysql_query("TRUNCATE TABLE owner_payment");
mysql_query("TRUNCATE TABLE renter");
mysql_query("TRUNCATE TABLE debt");
mysql_query("TRUNCATE TABLE renter_payment");
mysql_query("TRUNCATE TABLE template");

function get_random_str($range) {
    global $str;
    $max_start = strlen($str)-$range[1];
    if($max_start<0) {$max_start=0;}
    return substr($str, mt_rand(0, $max_start), mt_rand($range[0],$range[1]));
}

// Генерируем данные для таблицы OWNER
print "START owner\n"; flush();
$owner_value = array();
for ($o=1; $o<$cnt_owners+1; $o++) {
    if($o > $cnt_houses) {break;}
    $account_id = mt_rand(1,mt_getrandmax());
    $owner_value[] = 'DEFAULT,' 
                  . "'login{$account_id}',"  // 'login + account_id'
                  . "MD5('password{$account_id}')," // 'password + account_id'
                  . "'" . ucfirst(str_replace(' ', '', get_random_str(array(5,15)))) . "'," // name
                  . 'MAKEDATE(' . mt_rand(2008,2015) . ', ' . mt_rand(1,365) . ")," // valid_till
                  . "'" . mt_rand(5,$max_objects) . "'"; // max_objects
        
        // Генерируем и записываем данные для таблицы HOUSE
        if($o==1) {print "\tSTART house\n"; flush();}
        
        $res = mysql_query("SELECT MAX(owner_id) AS id FROM owner LIMIT 1");
        $rec = mysql_fetch_assoc($res);
        $o_id = $rec['id'] > 0 ? $rec['id'] : 1;
        
        for($h=1; $h<$cnt_houses+1; $h++) {
            
            $points = mt_rand(50,300); // очки
            $square = mt_rand(5,30).'.'.mt_rand(0,100); // площадь
            $square = (float)$square;
            mysql_query("INSERT INTO house SET owner_id=$o_id, points=$points, square=$square");
            print mysql_error();
            flush();
            
            $h_id = mysql_insert_id();
            
            $house_address = 'Street ' . ucfirst(get_random_str(array(10,25))) . ", house $h_id.";
            mysql_query("UPDATE house SET address='$house_address' WHERE house_id=$h_id LIMIT 1");
             print mysql_error();
            flush();
            
                // Генерируем и записываем данные для таблицы PART
                if($o==1 && $h==1) {print "\t\tSTART part\n"; flush();}
                for($p=1; $p<$cnt_parts+1; $p++) {
                    $code = $o.$h.$p; // вид квартиры
                    $title = get_random_str(array(10,30)); // описание вида
                    mysql_query("INSERT INTO part SET owner_id=$o_id, house_id=$h_id, code='$code', title='$title'");
                    print mysql_error();
                    flush();
                    
                        $p_id = mysql_insert_id();
                    
                        // Генерируем и записываем данные для таблицы RENTER
                        if($o==1 && $h==1 && $p==1) {print "\t\t\tSTART renter\n"; flush();}
                        
                        $dt_year = 2007;
                        $dt_day = 1;
                        for($r=1; $r<$cnt_renter+1; $r++) {
                            $sign_date = "MAKEDATE($dt_year,$dt_day)";
                            $arrival = $sign_date;
                            
                            $window = mt_rand(0,1);
                            $diap = mt_rand(90,120);
                            if($window==0) {
                                $dt_day+=($diap*2);
                            }
                            else {
                                $dt_day+=$diap;
                            }
                            $departure = "$arrival + INTERVAL $diap DAY";
                            if($dt_day >= 645) {
                                $variant = mt_rand(0,1);
                                if($variant == 0 && $r=$cnt_renter) {
                                    $departure = "NULL";
                                }
                            }
                            if($dt_day >= 1200) {
                                continue;
                            }
                            $contact_name = ucfirst(str_replace(' ', '', get_random_str(array(5,15))));
                            $contact_email = strtolower(str_replace(' ', '', get_random_str(array(5,10)) . '@' . get_random_str(array(5,10)) . '.' . get_random_str(array(2,3))));
                            $contact_phone = '+'  . mt_rand(1, 300) . ' ' . mt_rand(1, 999) . ' ' . mt_rand(100000,9000000);
                            $deposit = mt_rand(300,500).'.'.mt_rand(0,100); // залог
                            $rent = mt_rand(300,500).'.'.mt_rand(0,100);
                            $gwl_advance = mt_rand(30,50).'.'.mt_rand(0,100);
                            $service_advance = mt_rand(30,50).'.'.mt_rand(0,100);
                            $contract_number = $o.$h.$p.$r;
                            $contract_number = (int)$contract_number;
                            $contract_text = 'Contract text: ' . get_random_str(array(50,100));
                            $comment = get_random_str(array(50,100));
                            mysql_query("INSERT INTO renter SET owner_id=$o_id, part_id=$p_id, sign_date=$sign_date, arrival=$arrival, departure=$departure,
                            contact_name='$contact_name', contact_email='$contact_email', contact_phone='$contact_phone', deposit=$deposit,
                            rent=$rent, gwl_advance=$gwl_advance, service_advance=$service_advance, contract_number=$contract_number,
                            contract_text='$contract_text', comment='$comment'");
                            print mysql_error();
                            flush();
                            
                                $r_id = mysql_insert_id();
                                /*var_dump('house_id: ' . $h);
                                var_dump('part_id: ' . $p);
                                var_dump('renter_id: ' . $r_id);
                            */
                                // Генерируем и записываем данные для таблицы DEBT
                                $money = array();
                                $date = array();
                                if($h==1 && $o==1 && $p==1 && $r==1) {print "\t\t\t\tSTART debt\n"; flush();}
                                for($d=1; $d<$cnt_debt_renter_payment+1; $d++) {
                                    $date[$o.$h.$p.$r.$d] = 'MAKEDATE(' . mt_rand(2008,2015) . ', ' . mt_rand(1,365) . ")";
                                    $money[$o.$h.$p.$r.$d] = mt_rand(30,500).'.'.mt_rand(0,100);
                                    $money[$o.$h.$p.$r.$d] = (float)$money[$o.$h.$p.$r.$d];
                                    $comment = get_random_str(array(50,100));
                                    mysql_query("INSERT INTO debt SET owner_id=$o_id, renter_id=$r_id, date={$date[$o.$h.$p.$r.$d]}, comment='$comment', money={$money[$o.$h.$p.$r.$d]}");
                                    print mysql_error();
                                    flush();
                                }
                                if($o==$cnt_owners && $h==$cnt_houses && $p==$cnt_parts && $r==$cnt_renter) {print "\t\t\t\tEND debt\n"; flush();}
                                
                                // Генерируем и записываем данные для таблицы RENTER_PAYMENT
                                if($h==1 && $o==1 && $p==1 && $r==1) {print "\t\t\t\tSTART renter_payment\n"; flush();}
                                for($rp=1; $rp<$cnt_debt_renter_payment+1; $rp++) {
                                    $date[$o.$h.$p.$r.$rp] = "({$date[$o.$h.$p.$r.$rp]} + INTERVAL " . mt_rand(0,20) . " DAY)";
                                    $money[$o.$h.$p.$r.$rp] = $money[$o.$h.$p.$r.$rp] + mt_rand(-50,50);
                                    $money[$o.$h.$p.$r.$rp] = (float)$money[$o.$h.$p.$r.$rp];
                                    mysql_query("INSERT INTO renter_payment SET owner_id=$o_id, renter_id=$r_id, date={$date[$o.$h.$p.$r.$rp]}, money={$money[$o.$h.$p.$r.$rp]}");
                                    print mysql_error();
                                    flush();
                                }
                                if($o==$cnt_owners && $h==$cnt_houses && $p==$cnt_parts && $r==$cnt_renter) {print "\t\t\t\tEND renter_payment\n"; flush();}
                            
                        }
                        if($o==$cnt_owners && $h==$cnt_houses && $p==$cnt_parts) {print "\t\t\tEND renter\n"; flush();}
                    
                }
                if($o==$cnt_owners && $h==$cnt_houses) {print "\t\tEND part\n"; flush();}
                
                // Генерируем и записываем данные для таблицы OWNER_PAYMENT
                if($h==1 && $o==1) {print "\t\tSTART owner_payment\n"; flush();}
                for($op=1; $op<$cnt_owner_payment+1; $op++) {
                    $period_from = 'MAKEDATE(' . mt_rand(2008,2015) . ', ' . mt_rand(1,365) . ")";
                    $period_till = "($period_from + INTERVAL " . mt_rand(1,12) . " MONTH)";
                    $types = array('GWL', 'SERVICE');
                    $type = $types[mt_rand(0,1)];
                    $comment = get_random_str(array(50,100));
                    $money = mt_rand(1000,30000).'.'.mt_rand(0,100);
                    $money = (float)$money;
                    mysql_query("INSERT INTO owner_payment SET owner_id=$o_id, house_id=$h_id, period_from=$period_from, period_till=$period_till, type='$type', comment='$comment', money=$money");
                    print mysql_error();
                    flush();
                }
                if($h==$cnt_houses && $o==$cnt_owners) {print "\t\tEND owner_payment\n"; flush();}
    
        }
        if($o==$cnt_owners) {print "\tEND house\n"; flush();}  
}
// Записываем данные для таблицы OWNER
mysql_query("INSERT IGNORE INTO owner VALUES (" . implode('),(', $owner_value) . ")");
print mysql_error();
print "END owner\n"; flush();
?>
