<?
$AJAX_TIMEOUT = 3000;
$operation    = $_POST[ 'operation' ];
if( $operation == 'show_shop' ) {
    $id = intval( $_POST[ 'id' ] );
    $dt = $_POST[ 'dt' ];
    $dt = preg_replace( '/<.*?>/', '', $dt );
    $dt = str_replace( '"', '', $dt );
    $dt = str_replace( "'", '', $dt );
    $m  = array();
    preg_match( '/(\d{2})\.(\d{2})\.(\d{4})/', $dt, $m );
    $dt = $m[ 3 ] . '-' . $m[ 2 ] . '-' . $m[ 1 ];
    print show_shops( $id, $dt );
    exit;
}
if( $operation == 'show_common' ) {
    $id = intval( $_POST[ 'id' ] );
    $dt = $_POST[ 'dt' ];
    $dt = preg_replace( '/<.*?>/', '', $dt );
    $dt = str_replace( '"', '', $dt );
    $dt = str_replace( "'", '', $dt );
    $m  = array();
    preg_match( '/(\d{2})\.(\d{2})\.(\d{4})/', $dt, $m );
    $dt = $m[ 3 ] . '-' . $m[ 2 ] . '-' . $m[ 1 ];
    print show_common( $id, $dt );
    exit;
}
if( $operation == 'save_shops' ) {
    $dt = $_POST[ 'dt' ];
    $id = $_POST[ 'id' ];

    $r=mysql_query("select count(id) as ttt from ezh_direct where dt='".date("Y-m-d", strtotime($dt))."'");
    $res1=mysql_fetch_array($r);
    $r=mysql_query("select count(id) as ttt from ezh_direct where dt='".date("Y-m-d", strtotime($dt."+1 day"))."'");
    $res2=mysql_fetch_array($r);
    $r=mysql_query("select count(id) as ttt from ezh_direct where dt='".date("Y-m-d", strtotime($dt."+2 day"))."'");
    $res3=mysql_fetch_array($r);
    $r=mysql_query("select count(id) as ttt from ezh_direct where dt='".date("Y-m-d", strtotime($dt."+3 day"))."'");
    $res4=mysql_fetch_array($r);
    $r=mysql_query("select count(id) as ttt from ezh_direct where dt='".date("Y-m-d", strtotime($dt."+4 day"))."'");
    $res5=mysql_fetch_array($r);
    $r=mysql_query("select count(id) as ttt from ezh_direct where dt='".date("Y-m-d", strtotime($dt."+5 day"))."'");
    $res6=mysql_fetch_array($r);
    $r=mysql_query("select count(id) as ttt from ezh_direct where dt='".date("Y-m-d", strtotime($dt."+6 day"))."'");
    $res7=mysql_fetch_array($r);

    if($res1["ttt"]==0)
        mysql_query( "INSERT INTO ezh_direct(direct_kz , direct_ru, dt) values(".(int)$_POST[ 'kaz_1' ].", ".(int)$_POST[ 'rus_1' ].", '".date("Y-m-d", strtotime($dt))."')" );
    else
        mysql_query( "UPDATE ezh_direct set direct_kz=".(int)$_POST[ 'kaz_1' ].", direct_ru=".(int)$_POST[ 'rus_1' ]." where dt='".date("Y-m-d", strtotime($dt))."'" );
    if($res2["ttt"]==0)
        mysql_query( "INSERT INTO ezh_direct(direct_kz , direct_ru, dt) values(".(int)$_POST[ 'kaz_2' ].", ".(int)$_POST[ 'rus_2' ].", '".date("Y-m-d", strtotime($dt."+1 day"))."')" );
    else
        mysql_query( "UPDATE ezh_direct set direct_kz=".(int)$_POST[ 'kaz_2' ].", direct_ru=".(int)$_POST[ 'rus_2' ]." where dt='".date("Y-m-d", strtotime($dt."+1 day"))."'" );
    if($res3["ttt"]==0)
        mysql_query( "INSERT INTO ezh_direct(direct_kz , direct_ru, dt) values(".(int)$_POST[ 'kaz_3' ].", ".(int)$_POST[ 'rus_3' ].", '".date("Y-m-d", strtotime($dt."+2 day"))."')" );
    else
        mysql_query( "UPDATE ezh_direct set direct_kz=".(int)$_POST[ 'kaz_3' ].", direct_ru=".(int)$_POST[ 'rus_3' ]." where dt='".date("Y-m-d", strtotime($dt."+2 day"))."'" );
    if($res4["ttt"]==0)
        mysql_query( "INSERT INTO ezh_direct(direct_kz , direct_ru, dt) values(".(int)$_POST[ 'kaz_4' ].", ".(int)$_POST[ 'rus_4' ].", '".date("Y-m-d", strtotime($dt."+3 day"))."')" );
    else
        mysql_query( "UPDATE ezh_direct set direct_kz=".(int)$_POST[ 'kaz_4' ].", direct_ru=".(int)$_POST[ 'rus_4' ]." where dt='".date("Y-m-d", strtotime($dt."+3 day"))."'" );
    if($res5["ttt"]==0)
        mysql_query( "INSERT INTO ezh_direct(direct_kz , direct_ru, dt) values(".(int)$_POST[ 'kaz_5' ].", ".(int)$_POST[ 'rus_5' ].", '".date("Y-m-d", strtotime($dt."+4 day"))."')" );
    else
        mysql_query( "UPDATE ezh_direct set direct_kz=".(int)$_POST[ 'kaz_5' ].", direct_ru=".(int)$_POST[ 'rus_5' ]." where dt='".date("Y-m-d", strtotime($dt."+4 day"))."'" );
    if($res6["ttt"]==0)
        mysql_query( "INSERT INTO ezh_direct(direct_kz , direct_ru, dt) values(".(int)$_POST[ 'kaz_6' ].", ".(int)$_POST[ 'rus_6' ].", '".date("Y-m-d", strtotime($dt."+5 day"))."')" );
    else
        mysql_query( "UPDATE ezh_direct set direct_kz=".(int)$_POST[ 'kaz_6' ].", direct_ru=".(int)$_POST[ 'rus_6' ]." where dt='".date("Y-m-d", strtotime($dt."+5 day"))."'" );
    if($res7["ttt"]==0)
        mysql_query( "INSERT INTO ezh_direct(direct_kz , direct_ru, dt) values(".(int)$_POST[ 'kaz_7' ].", ".(int)$_POST[ 'rus_7' ].", '".date("Y-m-d", strtotime($dt."+6 day"))."')" );
    else
        mysql_query( "UPDATE ezh_direct set direct_kz=".(int)$_POST[ 'kaz_7' ].", direct_ru=".(int)$_POST[ 'rus_7' ]." where dt='".date("Y-m-d", strtotime($dt."+6 day"))."'" );

    $dt = preg_replace( '/<.*?>/', '', $dt );
    $dt = str_replace( '"', '', $dt );
    $dt = str_replace( "'", '', $dt );
    unset( $_POST[ 'dt' ] );
    $m = array();
    foreach( $_POST as $k => $v ) {
        if( preg_match( '/contacts_(\d+)_(\d+)/', $k, $m ) ) {
            if( $v != '' ) $v = intval( $v ); else $v = 'NULL';
            $id_city = $m[ 1 ];
            $i       = $m[ 2 ];
            $q       = "insert into ezh_city_day set contacts=$v,id_city=$id_city,dt='$dt'+interval $i day  ON DUPLICATE KEY UPDATE contacts=$v";
//      print "$q\n";
            mysql_query( $q );
        }

        if( preg_match( '/rus_(\d+)/', $k, $m ) ) {
            if( $v != '' ) $v = intval( $v ); else $v = 'NULL';
            $i = $m[ 1 ];
//            $q1 = "select paid,summ,orders,amount from ezh_city_week where id_city=$c_id and dt='$dt'";
//            $r1 = mysql_query( $q1 );
//            if( mysql_num_rows( $r1 ) > 0 ) {
//                $a1     = mysql_fetch_array( $r1 );
//                $paid   = $a1[ 'paid' ];
            $q       = "update ezh_direct set direct_ru=$v where date='$dt'+interval $i";
            mysql_query( $q );
        }

        if( preg_match( '/summ(\d+)/', $k, $m ) ) {
            if( $v != '' ) $v = intval( $v ); else $v = 'NULL';
            $id_city = $m[ 1 ];
            $q       = "insert into ezh_city_week set summ=$v,id_city=$id_city,dt='$dt' ON DUPLICATE KEY UPDATE summ=$v";
//      print "$q\n";
            mysql_query( $q );
        }
        if( preg_match( '/orders(\d+)/', $k, $m ) ) {
            if( $v != '' ) $v = intval( $v ); else $v = 'NULL';
            $id_city = $m[ 1 ];
            $q       = "insert into ezh_city_week set orders=$v,id_city=$id_city,dt='$dt' ON DUPLICATE KEY UPDATE orders=$v";
//      print "$q\n";
            mysql_query( $q );
        }
        if( preg_match( '/amount(\d+)/', $k, $m ) ) {
            if( $v != '' ) $v = intval( $v ); else $v = 'NULL';
            $id_city = $m[ 1 ];
            $q       = "insert into ezh_city_week set amount=$v,id_city=$id_city,dt='$dt' ON DUPLICATE KEY UPDATE amount=$v";
//      print "$q\n";
            mysql_query( $q );
        }
    }
    print show_shops( $id, $dt );
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <script type="text/javascript" src="/js/jquery-1.8.3.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.js"></script>
    <script type="text/javascript" src="/js/datepicker-ru.js"></script>
    <link rel="stylesheet" type="text/css" href="/js/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body style="margin: 0;">
<style>
    .p_input {
        width: 50px;
    }
</style>
<script type="text/javascript">
    $( function () {
        set_input_colors();


        var startDate = new Date( '<?=date( "m/d/Y", strtotime( date( 'o-\\WW' ) ) );?>' );
        var endDate = new Date( '<?=date( "m/d/Y", strtotime( date( 'o-\\WW' ) ) + 3600 * 24 * 6 );?>' );

        var selectCurrentWeek = function () {
            window.setTimeout( function () {
                $( '#weekpicker' ).datepicker( 'widget' ).find( '.ui-datepicker-current-day a' ).addClass( 'ui-state-active' )
            }, 1 );
        }

        $( '#weekpicker' ).datepicker( {
            showOtherMonths: true,
            selectOtherMonths: true,
            onSelect: function ( dateText, inst ) {
                var date = $( this ).datepicker( 'getDate' );
                startDate = new Date( date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 1 );
                endDate = new Date( date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 7 );
                var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
                $( '#weekpicker' ).val( $.datepicker.formatDate( dateFormat, startDate, inst.settings ) + ' - ' + $.datepicker.formatDate( dateFormat, endDate, inst.settings ) );
                show_user_block();
                show_common_block();

                selectCurrentWeek();
            },
            beforeShow: function () {
                selectCurrentWeek();
            },
            beforeShowDay: function ( date ) {
                var cssClass = '';
                if ( date >= startDate && date <= endDate )
                    cssClass = 'ui-datepicker-current-day';
                return [ true, cssClass ];
            },
            onChangeMonthYear: function ( year, month, inst ) {
                selectCurrentWeek();
            }
        } ).datepicker( 'widget' ).addClass( 'ui-weekpicker' );
        $( "#weekpicker" ).datepicker( $.datepicker.regional[ "ru" ] );

        $( '#weekbefore' ).click( function () {
            s = $( '#weekpicker' ).val().replace( / .*/, '' );
            arr = s.match( /(\d{2})\.(\d{2})\.(\d{4})/ );
            d = new Date( arr[ 2 ] + '/' + arr[ 1 ] + '/' + arr[ 3 ] );
            t = d.getTime();
            t1 = t - 7 * 24 * 3600 * 1000;
            d1 = new Date( t1 );
            startDate = d1;
            str1 = ( '0' + d1.getDate() ).slice( -2 ) + '.' + ( '0' + parseInt( d1.getMonth() + 1 ) ).slice( -2 ) + '.' + d1.getFullYear();
            t2 = t - 24 * 3600 * 1000;
            d2 = new Date( t2 );
            endDate = d2;
            str2 = ( '0' + d2.getDate() ).slice( -2 ) + '.' + ( '0' + parseInt( d2.getMonth() + 1 ) ).slice( -2 ) + '.' + d2.getFullYear();
            str = str1 + ' - ' + str2;
            $( '#weekpicker' ).val( str );
            show_user_block();
            show_common_block();
            return false;
        } );

        $( '#weekafter' ).click( function () {
            s = $( '#weekpicker' ).val().replace( / .*/, '' );
            arr = s.match( /(\d{2})\.(\d{2})\.(\d{4})/ );
            d = new Date( arr[ 2 ] + '/' + arr[ 1 ] + '/' + arr[ 3 ] );
            t = d.getTime();
            t1 = t + 7 * 24 * 3600 * 1000;
            d1 = new Date( t1 );
            startDate = d1;
            str1 = ( '0' + d1.getDate() ).slice( -2 ) + '.' + ( '0' + parseInt( d1.getMonth() + 1 ) ).slice( -2 ) + '.' + d1.getFullYear();
            t2 = t + 13 * 24 * 3600 * 1000;
            d2 = new Date( t2 );
            endDate = d2;
            str2 = ( '0' + d2.getDate() ).slice( -2 ) + '.' + ( '0' + parseInt( d2.getMonth() + 1 ) ).slice( -2 ) + '.' + d2.getFullYear();
            str = str1 + ' - ' + str2;
            $( '#weekpicker' ).val( str );
            show_user_block();
            show_common_block();
            return false;
        } );

        $( '#ui-datepicker-div .ui-datepicker-calendar tr' ).live( 'mousemove', function () {
            $( this ).find( 'td a' ).addClass( 'ui-state-hover' );
        } );
        $( '#ui-datepicker-div .ui-datepicker-calendar tr' ).live( 'mouseleave', function () {
            $( this ).find( 'td a' ).removeClass( 'ui-state-hover' );
        } );

        $( '.e_date' ).datepicker( {
            showOtherMonths: true,
            selectOtherMonths: true,
        } );
    } );

    function set_input_colors() {
        var color = '#C4D69C';
        $( 'input[type=text]' ).on( 'input', function ( e ) {
            if ( this.id == 'weekpicker' ) return;
            if ( this.disabled ) return;
            if ( this.value == '' ) {
                $( this ).css( 'background-color', '#FFFFFF' );
            } else {
                $( this ).css( 'background-color', color );
            }
        } );
        $( 'input[type=text]' ).each( function ( e ) {
            if ( this.id == 'weekpicker' ) return;
            if ( this.disabled ) return;
            if ( this.value == '' ) {
                $( this ).css( 'background-color', '#FFFFFF' );
            } else {
                $( this ).css( 'background-color', color );
            }
        } );
    }

    function show_user_block() {
        dt = $( "#weekpicker" ).val().replace( / .*/, '' );
        if ( dt != '' ) {
            $.ajax( {
                type: 'POST',
                url: '<?=$PHP_SELF?>',
                data: {
                    dt: dt,
                    id:<?=$id?>,
                    operation: 'show_shop'
                },
                timeout:<?=$AJAX_TIMEOUT?>,
                success: function ( html ) {
                    $( 'body' ).css( 'cursor', 'default' );
                    $( '#user_block' ).html( html );
                    set_input_colors();
                },
                error: function ( html ) {
                    $( 'body' ).css( 'cursor', 'default' );
                    alert( 'Ошибка соединения!' );
                }
            } );
        } else {
            $( '#user_block' ).html( '' );
        }
    }

    function show_common_block() {
        dt = $( "#weekpicker" ).val().replace( / .*/, '' );
        if ( dt != '' ) {
            $.ajax( {
                type: 'POST',
                url: '<?=$PHP_SELF?>',
                data: {
                    id:<?=$id?>,
                    dt: dt,
                    operation: 'show_common'
                },
                timeout:<?=$AJAX_TIMEOUT?>,
                success: function ( html ) {
                    $( 'body' ).css( 'cursor', 'default' );
                    $( '#common_block' ).html( html );
                },
                error: function ( html ) {
                    $( 'body' ).css( 'cursor', 'default' );
                    alert( 'Ошибка соединения!' );
                }
            } );
        } else {
            $( '#common_block' ).html( '' );
        }
    }

    function save_shops() {
        data = {};
        $( '.s_input' ).each( function ( k, o ) {
            o_id = o.id;
            val = o.value;
            data[ o_id ] = val;
        } );
        data[ 'operation' ] = 'save_shops';
        $.ajax( {
            type: 'POST',
            url: '<?=$PHP_SELF?>',
            data: data,
            timeout:<?=$AJAX_TIMEOUT?>,
            success: function ( html ) {
                console.log(html);
                show_user_block();
                show_common_block();
                set_input_colors();
                $( '.e_date' ).datepicker( {
                    showOtherMonths: true,
                    selectOtherMonths: true,
                } );
                $( 'body' ).css( 'cursor', 'default' );
            },
            error: function ( html ) {
                $( 'body' ).css( 'cursor', 'default' );
                alert( 'Ошибка сохранения!' );
            }
        } );
    }

    function myParseInt( s ) {
        s1 = parseInt( s );
        if ( isNaN( s1 ) ) return 0;
        return s1;
    }
</script>
<div class='user_block' style='position:fixed;padding:10px;width: 100%;background-color: #f2f2f2;margin-top: -43px;'>
    <div>
        <a href='' id='weekbefore' style='text-decoration:none;'>&larr;</a>
        Дата: <input type="text" id="weekpicker" style='width:200px;'
                     value='<?= date( "d.m.Y", strtotime( date( 'o-\\WW' ) ) ); ?> - <?= date( "d.m.Y", strtotime( date( 'o-\\WW' ) ) + 3600 * 24 * 6 ) ?>'>
        <a href='' id='weekafter' style='text-decoration:none;'>&rarr;</a>
        <div style='display:inline-block'
             id='common_block'><?= show_common( $id, date( "Y-m-d", strtotime( date( 'o-\\WW' ) ) ) ) ?></div>
    </div>
    <div style='position:absolute;top:10px;right:30px;'><a href='?logout=1' style='padding-left:200px;'>Выход</a></div>
    <div style='clear:both;'></div>
</div>
<div id='user_block' class='user_block'
     style="margin-top: 43px;"><?= show_shops( $id, date( "Y-m-d", strtotime( date( 'o-\\WW' ) ) ) ) ?></div>
</body>
</html>
<?
function show_common( $id, $dt )
{
    ob_start();

    $sum_bonus = 0;
    $q         = "select p.id,p.name,p.bonus from ezh_city p join ezh_shop s on p.id_shop=s.id where s.id_seller=$id";
    $r         = mysql_query( $q );
    while( $a = mysql_fetch_array( $r ) ) {
        $c_id    = $a[ 'id' ];
        $c_name  = $a[ 'name' ];
        $c_bonus = $a[ 'bonus' ];

        $r1 = mysql_query( "select * from pr_city where name='$c_name'" );
        if( mysql_num_rows( $r1 ) == 0 ) continue;
        $a1      = mysql_fetch_array( $r1 );
        $pr_c_id = $a1[ 'id' ];

        $r_top  = mysql_query( "select * from pr_order where done=1 and id_city=$pr_c_id and dt>='$dt' and dt<'$dt'+interval 7 day" );
        $amount = 0;
        while( $a_top = mysql_fetch_array( $r_top ) ) {
            $id_order = $a_top[ 'id' ];
            $r1_top   = mysql_query( "select ot.number,t.price from pr_tovar t,pr_order_tovar ot where t.id=ot.id_tovar and ot.id_order=$id_order" );
            while( $a1_top = mysql_fetch_array( $r1_top ) ) {
                $amount += $a1_top[ 'number' ];
            }
        }
        $sum_bonus += $amount * $c_bonus;
    }
    ?>
    Всего бонусов за неделю: <b><?php $payzp = new payzp( $dt, "", "" );
    echo $payzp->paySellerN( $id ) ?></b>
    <?

    $q        = "SELECT count(*) cnt FROM ezh_city p left join ezh_city_week w on p.id=w.id_city join ezh_shop s on p.id_shop=s.id where s.id_seller=$id and w.dt='$dt' and (w.paid=0 or w.paid is null)";
    $r        = mysql_query( $q );
    $a        = mysql_fetch_array( $r );
    $cnt_paid = $a[ 'cnt' ];
    $q        = "SELECT count(*) cnt FROM ezh_city p left join ezh_city_week w on p.id=w.id_city join ezh_shop s on p.id_shop=s.id where s.id_seller=$id and w.dt='$dt'";
    $r        = mysql_query( $q );
    $a        = mysql_fetch_array( $r );
    $cnt_full = $a[ 'cnt' ];
    if( $cnt_full > 0 && $cnt_paid == 0 ) {
        ?>
        <div style='background-color:green;color:white;display:inline-block;padding:5px;margin-left:5px;'>Оплачено</div>
        <?
    }
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

function show_shops( $id, $dt )
{
    ob_start();
    $r = mysql_query( "select * from ezh_shop where id_seller=$id" );
    while( $a = mysql_fetch_array( $r ) ) {
        $s_id = $a[ 'id' ];
        ?>
        <div id='shop<?= $s_id ?>' style='padding-bottom:20px;'><?= show_shop( $s_id, $dt ) ?></div>
        <?
    }
    ?>
    <div>
        <span style='display: inline-block;height: 100%;vertical-align: middle;'></span>
        <input style='vertical-align: middle;' type='button' value='Сохранить' onclick='save_shops()' class='orange'>
    </div>
    <?
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

function show_shop( $id, $dt )
{
    $q      = "select name from ezh_shop where id=$id";
    $r      = mysql_query( $q );
    $a      = mysql_fetch_array( $r );
    $s_name = $a[ 'name' ];

    ob_start();
    ?>
    <div style='padding-left:10px;'><b><?= $s_name ?></b></div>
    <div>
        <?
        $arr = array( '', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс', 'Итого' );

        $q = "select id,name from ezh_city where id_shop=$id and shown>0";
        $r = mysql_query( $q );
        echo '<div style="margin:10px;border:1px solid;">';
        echo "<table style='width: 100%; padding:10px;'>";
        echo "<tr>";
        echo "<td style='width: 200px; border-right: 1px solid;'></td>";
        echo "<td style='text-align: center'>" . $arr[ 1 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 2 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 3 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 4 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 5 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 6 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 7 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 8 ] . "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td style='width: 200px; border-right: 1px solid;'>Direct Казахстан</td>";

        $q1 = "select * from ezh_direct where dt between '$dt' and '".date('Y-m-d', strtotime($dt."+6 day"))."'";
        $r1 = mysql_query( $q1 );
        $icz=0;
        $cxz=0;
        while( $ax1 = mysql_fetch_array( $r1 ) ) {
            $icz++;
            $cxz=$cxz+(int)$ax1[ 'direct_kz' ];
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='kaz_$icz' class='s_input' value='".$ax1[ 'direct_kz' ]."'></td>";
        }
        if ($icz==0)
        {
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='kaz_1' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='kaz_2' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='kaz_3' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='kaz_4' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='kaz_5' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='kaz_6' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='kaz_7' class='s_input' value=''></td>";
        }
        echo "<td style='text-align: center'><input type='text' style='width:30px;' value='$cxz' class='s_input' disabled></td>";
        echo "</tr>";
        echo "</table>";
        echo "</div>";


        while( $a = mysql_fetch_array( $r ) ) {
            $c_id   = $a[ 'id' ];
            $c_name = $a[ 'name' ];

            $q1 = "select paid,summ,orders,amount from ezh_city_week where id_city=$c_id and dt='$dt'";
            $r1 = mysql_query( $q1 );
            if( mysql_num_rows( $r1 ) > 0 ) {
                $a1     = mysql_fetch_array( $r1 );
                $paid   = $a1[ 'paid' ];
                $summ   = $a1[ 'summ' ];
                $orders = $a1[ 'orders' ];
                $amount = $a1[ 'amount' ];
            } else {
                $paid   = 0;
                $summ   = '';
                $orders = '';
                $amount = '';
            }
            ?>
            <div style='margin:10px;border:1px solid;'>
                <table style='padding:10px;'>
                    <tr>
                        <td width='200' align='center' style='vertical-align:middle;border-right:1px solid;'
                            rowspan=4><?= $c_name ?></td>
                        <?
                        for( $i = 1; $i < count( $arr ) - 1; $i++ ) {
                            ?>
                            <td align='center' style='padding-bottom:30px;' class='header'><?= $arr[ $i ] ?></td>
                            <?
                        }
                        ?>
                    </tr>
                    <tr>
                        <?
                        $records_sum = 0;
                        for( $i = 1; $i <= 7; $i++ ) {
                            $i2x              = $i - 2;
                            $q2x              = "select contacts from ezh_city_day where id_city=$c_id and dt='$dt'+interval $i2x day";
                            $r2x              = mysql_query( $q2x );
                            $a2x              = mysql_fetch_array( $r2x );
                            $contactsOriginal = $a2x[ 'contacts' ];


                            $i1       = $i - 1;
                            $q2       = "select contacts from ezh_city_day where id_city=$c_id and dt='$dt'+interval $i1 day";
                            $r2       = mysql_query( $q2 );
                            $a2       = mysql_fetch_array( $r2 );
                            $contacts = $a2[ 'contacts' ];
                            ?>
                            <td style='padding-bottom:30px;padding-right:10px;padding-left:10px;text-align: center;'>
                                <input type="text" style='width:30px;' id="contacts_<?= $c_id ?>_<?= $i1 ?>"
                                       value="<?= $contacts ?>" class='s_input'<?
                                if( $paid == 1 ) {
                                    ?> disabled<?
                                } ?>>
                                <br>
                                Прирост
                                <br>
                                <input type="text" style='width:30px;' id="itog_<?= $c_id ?>_<?= $i1 ?>"
                                       value="<?php if( ( $contacts - $contactsOriginal ) > 0 ) echo( $contacts - $contactsOriginal ); else echo 0; ?>"
                                       class='s_a' disabled>
                            </td>
                            <?
                        }
                        ?>
                    </tr>
                </table>
            </div>
            <?
        }
        echo '<div style="margin:10px;border:1px solid;">';
        echo "<table style='width: 100%; padding:10px;'>";
        echo "<tr>";
        echo "<td style='width: 200px; border-right: 1px solid;'></td>";
        echo "<td style='text-align: center'>" . $arr[ 1 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 2 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 3 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 4 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 5 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 6 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 7 ] . "</td>";
        echo "<td style='text-align: center'>" . $arr[ 8 ] . "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td style='width: 200px; border-right: 1px solid;'>Direct Россия</td>";

        $q1 = "select * from ezh_direct where dt between '$dt' and '".date('Y-m-d', strtotime($dt."+6 day"))."'";
        $r1 = mysql_query( $q1 );
        $icz=0;
        $cxz=0;
        while( $ax1 = mysql_fetch_array( $r1 ) ) {
        $icz++;
        $cxz=$cxz+(int)$ax1[ 'direct_ru' ];
        echo "<td style='text-align: center'><input type='text' style='width:30px;' id='rus_$icz' class='s_input' value='".$ax1[ 'direct_ru' ]."'></td>";
        }

        if ($icz==0)
        {
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='rus_1' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='rus_2' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='rus_3' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='rus_4' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='rus_5' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='rus_6' class='s_input' value=''></td>";
            echo "<td style='text-align: center'><input type='text' style='width:30px;' id='rus_7' class='s_input' value=''></td>";
        }

        echo "<td style='text-align: center'><input type='text' style='width:30px;' value='$cxz' class='s_input' disabled></td>";
        echo "</tr>";
        echo "</table>";
        echo "</div>";
        ?>
    </div>
    <input type='hidden' id='dt' value='<?= $dt ?>' class='s_input'>
    <input type='hidden' id='id' value='<?= $id ?>' class='s_input'>
    <?
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

?>
