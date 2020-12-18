<?php
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
  error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
include("$DOCUMENT_ROOT/mysql_connect.php");
if ($_POST['tovar']!="" && $_POST['tovar']!="Выберите товар" && $_POST['kolvo']!="" && $_POST['kolvo']!="0" && $_POST["search-to-date"]!="")
{
    $tovar=$_POST['tovar'];
    $kolvoopt=$_POST['kolvo'];
    $napr=$_POST['napr'];
    $dat=$_POST['search-to-date'];
    $q = "insert into skladprih(kolvoprih, napr, data, idtovarprih) values($kolvoopt, $napr, '$dat', $tovar)";
    mysql_query($q);


if($napr==1) {
    $qq = "select * from skladrozn where idtovar=$tovar";
    $r = mysql_query($qq);
    if (($a=mysql_fetch_array($r))>0)
    {
        $r=(float)$a['kolvorozn'];
        $r+=$kolvoopt;
        $q = "update skladrozn set kolvorozn=$r where idtovar=$tovar";
        mysql_query($q);
    }
    else
    {
        $r=0;
        $r+=$kolvoopt;
        $q = "insert into skladrozn(idtovar, kolvorozn) values($tovar, $r)";
        mysql_query($q);
    }
}
else
{
    $qq = "select * from sklad where idtovar=$tovar";
    $r = mysql_query($qq);
    if (($a=mysql_fetch_array($r))>0)
    {
        $o=(float)$a['kolvoopt'];
        $o+=$kolvoopt;
        $q = "update sklad set kolvoopt=$o where idtovar=$tovar";
        mysql_query($q);
    }
    else
    {
        $o=0;
        $o+=$kolvoopt;
        $q = "insert into sklad(idtovar, kolvoopt) values($tovar, $o)";
        mysql_query($q);
    }
}
    header('Location: /skladopt/prihod.php');
}
?>
<?php include 'templ/header.php'; ?>
<div style="padding-top: 85px;">
<div align="center" style="float: right; width: 100%;">
    <h1 style="margin-bottom: 20px;">Приход</h1>
    <div align="center">
    <form class="needs-validation" method="POST">
        <table style="width: 90%">
            <tr>
            <td style="padding: 10px;">
                <label for="validationTooltip01">Наименование товара</label>
                <select class="custom-select mr-sm-2" id="inlineFormCustomSelect" name="tovar" style="font-size: 15px;">
                    <option selected>Выберите товар</option>
                    <?php
                    $q = mysql_query("SELECT * FROM `pr_tovar` where pokaz>0 and active>0 ORDER BY `pr_tovar`.`name` ASC");
                    while ($qsrav = mysql_fetch_array($q))
                    {
                    ?>
                    <option value="<?=$qsrav['id']; ?>"><?=$qsrav['name']; ?></option>
                    <?php } ?>
                </select>
        </td>
        <td>
                <label for="validationTooltip02">Количество</label>
                <input type="text" class="form-control" id="validationTooltip02" placeholder="Количество" name="kolvo" value="0" style="width: 85px; font-size: 15px;" required>
                <input type='hidden' name='napr' value='2'>
        </td>
        <td style="padding: 10px;">
            <label for="search-to-date">Дата</label><br>
            <input type="text" name="search-to-date" id="search-to-date" value="<?=date("Y-m-d"); ?>"/>
        </td>
        <td style="padding: 10px;">
            <button class="btn btn-primary" type="submit">Сохранить</button>
            </td>
        </tr>
        </table>
    </form>
        </div>
    <table class="table table-hover" style="width: 90%; background: floralwhite; border-radius: 1%;">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Название</th>
            <th scope="col">Количество</th>
            <th scope="col">Цена</th>
            <th scope="col">Сумма</th>
            <th scope="col">Дата</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $qrqcity = mysql_query("SELECT * FROM `pr_tovar` t, skladprih s where t.pokaz>0 and napr=2 and t.id=s.idtovarprih ORDER BY s.data DESC, t.name ASC");
        $i=0;
        while ($qw=mysql_fetch_array($qrqcity))
        {
            $i++;
            ?>
            <tr <?=$style ?>>
                <th scope="row"><?=$i; ?></th>
                <td><?=$qw['name']; ?></td>
                <td><?=$qw['kolvoprih']; ?></td>
                <td><?=$qw['price']; ?></td>
                <td><?=$qw['kolvoprih']*$qw['price']; ?></td>
                <td><?=date("d.m.Y", strtotime($qw['data'])) ?></td>
            </tr>
        <?php }
        ?>
        </tbody>
    </table>
    <br>
</div>
</div>
<?php include 'templ/footer.php'; ?>
