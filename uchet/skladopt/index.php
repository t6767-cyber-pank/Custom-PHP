<?php
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
  error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
include("$DOCUMENT_ROOT/mysql_connect.php");
if ($_POST['tovars']!="" && $_POST['kolvosp']!="" && $_POST['kolvotek']>=$_POST['kolvosp']) {
    $tovar = $_POST['tovars'];
    $kolvos = $_POST['kolvosp'];
    $napr = $_POST['nazn'];
    $dat = $_POST['dates'];
    $kolvotek = $_POST['kolvotek'];
    $q = "insert into skladrash(kolvorash, napr, data, idtovarrash) values($kolvos, $napr, '$dat', $tovar)";
    mysql_query($q);

    if($napr==1) {
        $qq = "select * from skladrozn where idtovar=$tovar";
        $r = mysql_query($qq);
        $a=mysql_fetch_array($r);
            $r=(float)$a['kolvorozn'];
            $r-=$kolvos;
            if ($r!=0) {
                $q = "update skladrozn set kolvorozn=$r where idtovar=$tovar";
            }
            else {
                $q = "delete from skladrozn where idtovar=$tovar";
            }
            mysql_query($q);
    }
    else
    {
        $qq = "select * from sklad where idtovar=$tovar";
        $r = mysql_query($qq);
        $a=mysql_fetch_array($r);
            $o=(float)$a['kolvoopt'];
            $o-=$kolvos;
        if ($o!=0) {
            $q = "update sklad set kolvoopt=$o where idtovar=$tovar";
        } else
        { $q = "delete from sklad where idtovar=$tovar";}
            mysql_query($q);
    }
    header('Location: /skladopt/');
}

?>
<?php include 'templ/header.php'; ?>
<div style="padding-top: 85px;">
<div align="center" style="float: left; width: 100%;">
    <h1>Склад опт</h1>
    <br>
    <table class="table table-hover" style="width: 90%; background: floralwhite; border-radius: 1%;">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Название</th>
            <th scope="col">Количество</th>
            <th scope="col">Цена</th>
            <th scope="col">Сумма</th>
            <th scope="col">Списать</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $qrqcity = mysql_query("SELECT * FROM `pr_tovar` t, sklad s where t.pokaz>0 and t.id=s.idtovar ORDER BY t.name ASC");
        $i=0;
        while ($qw=mysql_fetch_array($qrqcity))
        {
            $i++;
            if ($qw['kolvo']>0) $style="style='background: antiquewhite;'"; else $style="";
            ?>
            <tr <?=$style ?>>
                <th scope="row"><?=$i; ?></th>
                <td><?=$qw['name']; ?></td>
                <td><?=$qw['kolvoopt']; ?></td>
                <td><?=$qw['price']; ?></td>
                <td><?=$qw['kolvoopt']*$qw['price']; ?></td>
                <td><form method="POST">
                        <input type="text" class="form-control" id="validationTooltip02" placeholder="Количество" name="kolvosp" value="0" style="width: 40px; font-size: 11px; height: 18px; float: left; margin-right: 7px;" required>
                        <button type="submit" style="width: 55px; height: 20px; font-size: 11px; float: left">Списать</button>
                        <input type='hidden' name='nazn' value='2'>
                        <input type='hidden' name='tovars' value='<?=$qw['idtovar']; ?>'>
                        <input type='hidden' name='dates' value='<?=date("Y-m-d"); ?>'>
                        <input type='hidden' name='kolvotek' value='<?=$qw['kolvoopt']; ?>'>
                    </form>
                </td>
            </tr>
        <?php }
        ?>
        </tbody>
    </table>
    <br>
</div>
</div>
<?php include 'templ/footer.php'; ?>
