<?php
  $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
  error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
  include("$DOCUMENT_ROOT/mysql_connect.php");
?>
<?php include 'templ/header.php'; ?>
<div style="padding-top: 115px;">
<div align="center" style="float: right; width: 100%;">
    <h1 style="margin-bottom: 20px;">Расход</h1>
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
        $qrqcity = mysql_query("SELECT * FROM `pr_tovar` t, skladrash s where t.pokaz>0 and napr=1 and t.id=s.idtovarrash $selname $seldate ORDER BY s.data DESC, t.name ASC");
        $i=0;
        while ($qw=mysql_fetch_array($qrqcity))
        {
            $i++;
            ?>
            <tr>
                <th scope="row"><?=$i; ?></th>
                <td><?=$qw['name']; ?></td>
                <td><?=$qw['kolvorash']; ?></td>
                <td><?=$qw['price']; ?></td>
                <td><?=$qw['kolvorash']*$qw['price']; ?></td>
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
