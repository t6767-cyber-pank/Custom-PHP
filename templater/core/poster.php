<?php
$request = json_decode(file_get_contents('php://input'), true);
chdir("..");
require_once("./classes/testUI.php");
$pizdyulator = new pizdyulator();
$operation=$request['oper'];
switch ($operation)
{
    case "editContent":
        $pizdyulator->DB->insertContent($request["fields"][0], $request["fields"][1]);
        break;
    case "editCategory":
        $pizdyulator->DB->insertCategory($request["fields"][0], $request["fields"][1]);
        break;
    case "ChangeNameCategory":
        if($request["fields"]["name"]!="") {
            $pizdyulator->DB->updateNameCategory($request["fields"]["name"], $request["fields"]["id"]);
        }
        break;
    case "changeTemplate":
        $tempname=$request["fields"]["tempName"];
        $id=$request["fields"]["id"];
        $idcat=$request["fields"]["id_category"];
        $pizdyulator->DB->updateTemplate($tempname, $idcat, $id);
        break;
    case "deleteTemplate":
        $id=$request["fields"]["id"];
        $pizdyulator->DB->deleteTemplate($id);
        break;
    case 'deleteCategory':
        $id=$request["fields"]["id"];
        $pizdyulator->DB->deleteCategory($id);
        break;
    case "addBlock":
        var_dump($request);
        $x= $request["mydata"][0];
        $defcontent=$pizdyulator->DB->getDefauldBlockClass($x["id_content"]);
        echo $pizdyulator->DB->insertBlok("Новое поле", "z", $x["id_content"], $x["id_template"], $x["classx"]);
        break;
    case "delete":
        $pizdyulator->DB->deleteBlock($request["mydata"]);
        break;
    case "publish":
        foreach ($request['fields'] as $field) {
            $pizdyulator->DB->saveSortStylesPublish($field["block_id"], $field["block_sort"], $field["block_style0"], $field["block_style1"], json_encode($field["block_sty"]));
            foreach ($field["fields"] as $fld) {
                    $pizdyulator->DB->saveFieldPublich($fld["var_chern"], $fld["id"]);
            }
        }
        echo "Опубликовано";
        break;
    default:
    {
        foreach ($request['fields'] as $field) {
            $pizdyulator->DB->saveSortStyles($field["block_id"], $field["block_sort"], $field["block_style0"], $field["block_style1"], json_encode($field["block_sty"]));
                foreach ($field["fields"] as $fld) {
                    echo $fld["var_chern"];
                    $pizdyulator->DB->saveField($fld["var_chern"], $fld["id"]);

                }
        }
        echo "Сохранено";
    }
}
?>
