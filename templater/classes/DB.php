<?php
class DB extends PDO
{
    public function __construct($file = './settings/my_setting.ini')
    {
        // подключаемся к бд через ini файл
        if (!$settings = parse_ini_file($file, TRUE)) throw new exception('Unable11 to open ' . $file . '.');
        $dns = $settings['database']['driver'].':host=' . $settings['database']['host'].((!empty($settings['database']['port'])) ? (';port=' . $settings['database']['port']) : '').';dbname='.$settings['database']['schema'].';charset=utf8';
        parent::__construct($dns, $settings['database']['username'], $settings['database']['password']);
    }

/** SELECT запросы формирование готовых массивов данных **/

    // Формируем JSON массив всех данных которые будет использовать фронт
    public function GetJSONArray($id)
    {
        $arChernovik=array();
        $stmt = $this->query("SELECT * FROM bloks where id_template=$id order by sort");
        $ix=1000000;
        while ($row = $stmt->fetch()) {
            $stmtCont = $this->query("SELECT * FROM block_content where id=".$row["id_content"]);
            $rowCont = $stmtCont->fetch();
            $stmtBlockVars = $this->query("SELECT * FROM bloks_fields where id_block=".$row["id"]." order by id");
            $cfields=array();
            while ($rowBlockVars = $stmtBlockVars->fetch()) {
                $cfield=array(
                    "id"=> $rowBlockVars['id'],
                    "id_block"=> $rowBlockVars['id_block'],
                    "name"=> $rowBlockVars['name'],
                    "var_chern"=> $rowBlockVars['var_chern'],
                    "var"=> $rowBlockVars['var'],
                    "id_type"=> $rowBlockVars['id_type'],
                );
                array_push($cfields, $cfield);
            }
            $xxx=array(
                "block_id_template"=> $id,
                "status"=> "exist",
                "block_id"=> $row["id"],
                "block_direct"=> "dir".$row["id"],
                "block_show"=> "sh".$row["id"],
                "block_name"=> $row["name"],
                "block_id_content"=> $row["id_content"],
                "block_sort"=> $ix,
                "block_style0"=> $row["class0"],
                "block_style1"=> $row["class1"],
                "block_sty"=>  json_decode($row["style"]),
                "content_develop"=> $rowCont["content_develop"],
                "content_name_field"=> $rowCont["name_field"],
                "content_fieldCount"=> $rowCont["fieldCount"],
                "content_default_blok_type"=> $rowCont["default_blok_type"],
                "fields"=>$cfields,
            );
            array_push($arChernovik, $xxx );
            $ix++;
        }
        return $arChernovik;
    }

    // Формируем JSON массив всех данных которые будет использовать фронт
    public function GetJSONArrayToShow($id)
    {
        $arChernovik=array();
        $stmt = $this->query("SELECT * FROM bloks where id_template=$id order by sort");
        while ($row = $stmt->fetch()) {
            $stmtCont = $this->query("SELECT * FROM block_content where id=".$row["id_content"]);
            $rowCont = $stmtCont->fetch();
            $stmtBlockVars = $this->query("SELECT * FROM bloks_fields where id_block=".$row["id"]." order by id");
            $cfields=array();
            while ($rowBlockVars = $stmtBlockVars->fetch()) {
                $vx=str_replace("\n", "<br>", $this->jsaddslashesView($rowBlockVars['var']));
                $cfield=array(
                    "var"=> $vx,
                );
                array_push($cfields, $cfield);
            }
            $xxx=array(
                "block_id"=> $row["id"],
                "block_show"=> "sh".$row["id"],
                "block_sort"=> $row["sort"],
                "block_style0"=>  $row["class0reliz"],
                "block_style1"=>  $row["class1reliz"],
                "block_sty"=>  json_decode($row["stylereliz"]),
                "content"=> $rowCont["content"],
                "fields"=> $cfields,
            );
            array_push($arChernovik, $xxx );
        }
        return $arChernovik;
    }

    // Формируем JSON массив всех данных которые будет использовать фронт
    public function GetJSONComponents($id)
    {
        $arChernovik=array();
        $stmt = $this->query("SELECT * FROM block_content");
        while ($row = $stmt->fetch()) {
            $xxx=array(
                "id"=> $row["id"],
                "name"=> $row["name_field"],
                "defaultclass"=> $row["defaultclass"],
            );
            array_push($arChernovik, $xxx );
        }
        return $arChernovik;
    }

    // Вывести хедер
    public function showHeader($id)
    {
        $stmt = $this->query("SELECT * FROM headers where id=$id");
        $row = $stmt->fetch();
        $style=$this->getStyles();
        $strStyle="<style>\n";
        foreach ($style as $st)
            $strStyle.=$st['css']."\n";
        $strStyle.="</style>\n";
        return $row["begin"].$row["title"].$strStyle.$row["end"]."\n";
    }

    public function getStyles()
    {
        $stmt = $this->query("SELECT * FROM styles");
        $arr=array();
        while ($row = $stmt->fetch()) array_push($arr, $row);
        return $arr;
    }

    public function getStylesSmart($idType)
    {
        $stmt = $this->query("SELECT id, name, nameclass, type FROM styles where type=$idType order by name");
        $arr=array();
        while ($row = $stmt->fetch()) array_push($arr, $row);
        return $arr;
    }

    // вывести футер
    public function showFooters($id)
    {
        $stmt = $this->query("SELECT * FROM footers where id=$id");
        $row = $stmt->fetch();
        return $row["content"];
    }

    /** SELECT запросы **/

    // Данные шаблона по id
    public function showTemplateById($id)
    {
        $stmt = $this->query("SELECT * FROM templates where id=$id");
        return $stmt->fetch();
    }

    public function showAllImages()
    {
        $stmt = $this->query("SELECT * FROM images order by name");
        $arr=array();
        while ($row=$stmt->fetch()) {
        array_push($arr, $row);
        }
        return $arr;
    }

    public function getAllCategoryJSON()
    {
        $arr=array();
        $stmt = $this->query("SELECT * FROM category where parent=0 order by name");
        while ($row=$stmt->fetch())
        {
            array_push($arr, $row);
            $stmt2 = $this->query("SELECT * FROM category where parent=".$row['id']." order by name");
            while ($row2=$stmt2->fetch()) {
                array_push($arr, $row2);
            }
        }
        return $arr;
    }

    public function getTemplatesById($id)
    {
        $stmt = $this->query("SELECT * FROM templates where id=$id");
        $row=$stmt->fetch();
        return $row;
    }

    public function getAllParentCategoryJSON()
    {
        $arr=array();
        $stmt = $this->query("SELECT * FROM category where parent=0 order by name");
        while ($row=$stmt->fetch())
        {
            array_push($arr, $row);
        }
        return $arr;
    }

    public function getCategoryJSON22()
    {
        $stmt = $this->query("SELECT * FROM category where parent=0 order by name");
        $arr=array();
        while ($row=$stmt->fetch())
        {
            $arr1=array();
            $stmt2 = $this->query("SELECT * FROM category c where parent=".$row["id"]." order by name");
            $arr2=array();
            while ($row2=$stmt2->fetch())
            {
                $arrtempl=array();
                $arrtemplSbor=array();
                $stmtTemp = $this->query("SELECT * FROM templates where id_category=".$row2['id']);
                while($rowTemp=$stmtTemp->fetch())
                {
                    array_push($arrtempl, $rowTemp);
                }
                array_push($arrtemplSbor, $row2);
                array_push($arrtemplSbor, $arrtempl);
                array_push($arr2, $arrtemplSbor);
            }
            array_push($arr1, $row);
            array_push($arr1, $arr2);
            array_push($arr, $arr1);
        }
        return $arr;
    }

    public function getCategoryJSON()
    {
        $stmt = $this->query("SELECT *, 'x' as catShow FROM category where parent=0 order by name");
        $arr=array();
        while ($row=$stmt->fetch())
        {
            $arr1=array();
            $arrtemplMain=array();
            $stmtTp = $this->query("SELECT *, 'x' as tempmainshow FROM templates where id_category=".$row['id']." order by tempName");
            while($rowTemp=$stmtTp->fetch())
            {
                array_push($arrtemplMain, $rowTemp);
            }
            $stmt2 = $this->query("SELECT *, 'x' as subcatShow  FROM category c where parent=".$row["id"]." order by name");
            $arr2=array();
            while ($row2=$stmt2->fetch())
            {
                $arrtempl=array();
                $arrtemplSbor=array();
                $stmtTemp = $this->query("SELECT *, 'x' as tempsubshow FROM templates where id_category=".$row2['id']." order by tempName");
                while($rowTemp=$stmtTemp->fetch())
                {
                    array_push($arrtempl, $rowTemp);
                }
                array_push($arrtemplSbor, $row2);
                array_push($arrtemplSbor, $arrtempl);
                array_push($arr2, $arrtemplSbor);
            }
            array_push($arr1, $row);
            array_push($arr1, $arrtemplMain);
            array_push($arr1, $arr2);
            array_push($arr, $arr1);
        }
        return $arr;
    }

    public function getJsonTemplateById()
    {
        $stmt = $this->query("SELECT * FROM templates order by tempName");
        $arr=array();
        while ($row=$stmt->fetch())
        {
            array_push($arr, $row);
        }
        return $arr;
    }

    // Взять блок в виде массива
    function selectBlockById($id, $tempId)
    {
        $stmt = $this->query("SELECT * FROM bloks b, block_content c where c.id=b.id_content and b.id_template=$tempId and b.id=$id order by b.sort");
        $row = $stmt->fetch();
        return $row;
    }

    /** update запросы **/
    // сохранение блока
    public function save($nameBlock, $idBlock, $fields, $sort)
    {
        $this->saveBlock($nameBlock, $idBlock, $sort);
        foreach ($fields as $field)
        {
            $this->saveField($field[1], $field[0], $field[2]);
        }
    }

    // Сортировка
    public function saveSortStyles($idBlock, $sort, $class0, $class1, $style)
    {
        $sql = "update bloks set sort=$sort, class0='$class0', class1='$class1', style='$style' where id=$idBlock";
        $query = $this->prepare($sql);
        $query->execute();
        return "777777";
    }

    // Сортировка
    public function saveSortStylesPublish($idBlock, $sort, $class0, $class1, $style)
    {
        $sql = "update bloks set sort=$sort, class0='$class0', class1='$class1', class0reliz='$class0', class1reliz='$class1', style='$style', stylereliz='$style' where id=$idBlock";
        $query = $this->prepare($sql);
        $query->execute();
        return "777777";
    }

    // сохранение поля
    public function saveField($nameField, $idField)
    {
        $nameField=$this->jsaddslashes($nameField);
        $sql = "update bloks_fields set var_chern=\"$nameField\" where id=$idField";
        $query = $this->prepare($sql);
        $query->execute();
        return $nameField;
    }

    // сохранение поля
    public function saveFieldPublich($nameField, $idField)
    {
        $nameField=$this->jsaddslashes($nameField);
        $sql = "update bloks_fields set var='$nameField', var_chern='$nameField' where id=$idField";
        $query = $this->prepare($sql);
        $query->execute();
        return $nameField;
    }

    public function updateNameCategory($name, $id)
    {
        $sql = "update category set name='$name' where id=$id";
        $query = $this->prepare($sql);
        $query->execute();
    }

    public function updateTemplate($name, $idcateg, $id)
    {
        $sql = "update templates set tempName='$name', id_category=$idcateg where id=$id";
        $query = $this->prepare($sql);
        $query->execute();
    }

    /** insert запросы **/
    // сохранение блока
    public function insertBlok($nameblock, $sort, $idcontent, $idTemplate, $defcontent)
    {
        $dstyle='{"color" : "x", "background-color" : "x", "font-size" : "x", "padding" : "x", "margin" : "x"}';
        $sql = "insert into bloks(name, id_template, sort, id_content, class0, style, class0reliz, stylereliz, class1, class1reliz) values('$nameblock', $idTemplate, '$sort', $idcontent, '$defcontent', '$dstyle', '', '', 'defaultfontfamily', '')";
        $query = $this->prepare($sql);
        $query->execute();
        $id_blok=$this->lastInsertId();
        $blok=$this->selectBlockById($id_blok, $idTemplate);
        for ($i=0; $i<$blok['fieldCount']; $i++)
        {
            $x=$i+1;
            $sql = "insert into bloks_fields(name, id_block, var, var_chern, id_type) values('Поле $x', $id_blok, '', 'изменить значение $x', ".$blok['default_blok_type'].")";
            $query = $this->prepare($sql);
            $query->execute();
        }
        return "0988878777";
    }

    public function insertContent($tempName, $id_category)
    {
        $sql = "insert into templates(id_header, id_footer, tempName, id_category) values(1, 2, '$tempName', $id_category)";
        $query = $this->prepare($sql);
        $query->execute();
    }

    public function insertCategory($name, $parent)
    {
        $sql = "insert into category(name, parent) values('$name', $parent)";
        $query = $this->prepare($sql);
        $query->execute();
    }

    /** delete запросы **/
    // Удалить блок
    public function deleteBlock($idBlock)
    {
        $sql = "delete from bloks where id=$idBlock";
        $query = $this->prepare($sql);
        $query->execute();
        $sql = "delete from bloks_fields where id_block=$idBlock";
        $query = $this->prepare($sql);
        $query->execute();
        return "777777";
    }

    public function deleteTemplate($id)
    {
        $sql = "delete from templates where id=$id";
        $query = $this->prepare($sql);
        $query->execute();
    }

    public function deleteCategory($id)
    {
        $stmt = $this->query("SELECT * FROM  templates where id_category=$id");
        while($row = $stmt->fetch())
        {
            $this->deleteTemplate($row['id']);
        }

        $stmt = $this->query("SELECT * FROM  category where parent=$id");
        while($row = $stmt->fetch())
        {
            $this->deleteTemplate($row['id']);
            $sql = "delete from category where id=".$row['id'];
            $query = $this->prepare($sql);
            $query->execute();
        }
        $sql = "delete from category where id=$id";
        $query = $this->prepare($sql);
        $query->execute();
    }

    public function getDefauldBlockClass($id)
    {
        $stmt = $this->query("SELECT * FROM block_content where id=$id");
        $row = $stmt->fetch();
        return $row["defaultclass"];
    }

    public function jsaddslashes($s)
    {
        $o="";
        $l=strlen($s);
        for($i=0;$i<$l;$i++)
        {
            $c=$s[$i];
            switch($c)
            {
                //case '<': $o.='\\x3C'; break;
                //case '>': $o.='\\x3E'; break;
                case '\'': $o.='\\\''; break;
                case '\\': $o.='\\\\'; break;
                case '"':  $o.='\\"'; break;
                case "\n": $o.='\\n'; break;
                case "\r": $o.='\\r'; break;
                case "'":  $o.='\''; break;
                default:
                    $o.=$c;
            }
        }
        return $o;
    }

    public function jsaddslashesView($s)
    {
        $o="";
        $l=strlen($s);
        for($i=0;$i<$l;$i++)
        {
            $c=$s[$i];
            switch($c)
            {
                case '<': $o.='&lt;'; break;
                case '>': $o.='&gt;'; break;
                default:
                    $o.=$c;
            }
        }
        return $o;
    }
}
?>
