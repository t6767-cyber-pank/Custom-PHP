<?php
require_once("DB.php");
class pizdyulator
{
/** Глобальные переменные в основном массивы сборщики **/
    public $scriptsArray;
    public $headersArray;
    public $scriptsVarsArray;
    public $chernovik;
    public $styles;
    public $DB;
    public $idtemplater;

/** Конструктор подключает файл с данными о базе данных и объявляет глобальные переменные **/
	public function __construct($fix=0)
    {
        $this->scriptsArray=array();
        $this->headersArray=array();
        $this->scriptsVarsArray=array();
        $this->chernovik=array();
        $this->DB=new DB();
        $this->idtemplater=$fix;
   }

/** Шаблон отображения без редактирования **/
    public function showTemplate()
    {
        $row = $this->DB->showTemplateById($this->idtemplater);
        echo $this->DB->showHeader($row["id_header"]);
        echo $this->showMenuTemplate();
        echo '<hr style="border-width: 17px;">';
        echo $this->showBlocks($row["id"]);
        echo '<hr style="border-width: 17px;">';
        echo $this->showControllersArrayToShow();
        echo $this->DB->showFooters($row["id_footer"]);
    }

/** Шаблон отображения в режиме редактирования **/
    public function showDevelopTemplate()
    {
        $row = $this->DB->showTemplateById($this->idtemplater);
        echo $this->DB->showHeader($row["id_header"]);
        echo $this->showMenuTemplate();
        echo '<hr style="border-width: 17px;">';
        echo $this->showDevelopBlocks($row["id"]);
        echo '<hr style="border-width: 17px;">';
        echo $this->showControllersArray();
        echo $this->DB->showFooters($row["id_footer"]);
    }

/**  Показать меню **/
    public function showMenuTemplate()
    {
        $str='<div style="margin-top: 20px;">';
        $str.='<a href="/" style="color: black; padding: 10px; border: 1px solid; text-decoration: none; margin: 10px; background: darkblue; color: white;">Главная</a>';
        $str.='</div>';
        return $str;
    }

/** Блоки показа без редактора **/
    public function showBlocks($id)
    {
        /* Заполнение массива данными из БД */
        $this->chernovik=$this->DB->GetJSONArrayToShow($id);
        $this->addToScriptArray("chernovik", json_encode($this->chernovik));
        $images=$this->DB->showAllImages();
        $this->addToScriptArray("images", json_encode($images));
        ob_start();
       /* Начало вывода html */
        ?>
        <!-- Вывод директив -->
        <div ng-repeat="block_name in fieldchernovik  | orderBy: 'block_sort'">
        <?php echo "\n"; foreach ($this->chernovik as $ttt) { echo "<".$ttt["block_show"]."\n"; ?>
        value="block_name.fields[0].var"
        value1="block_name.fields[1].var"
        value2="block_name.fields[2].var"
        style="block_name.block_sty"
        class0="block_name.block_style0"
        class1="block_name.block_style1"
        ng-if="block_name.block_id=='<?=$ttt["block_id"]?>'">
        sort="block_name.block_sort"
        <?php echo '</'.$ttt["block_show"].">\n"; } ?>
        </div>
        <!-- конец вывода директив -->
        <?php
        /* конец вывода html */
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

/** Блоки показа в режиме редактора **/
    public function showDevelopBlocks($id)
    {
        /* Заполнение массива данными из БД */
        $this->chernovik=$this->DB->GetJSONArray($id);
        $this->addToScriptArray("chernovik", json_encode($this->chernovik));
        $styles=$this->DB->getStylesSmart(1);
        $this->addToScriptArray("style", json_encode($styles));
        $styles=$this->DB->getStylesSmart(2);
        $this->addToScriptArray("fontfamily", json_encode($styles));
        $this->addToScriptArray("content", json_encode($this->DB->GetJSONComponents($id)));
        $images=$this->DB->showAllImages();
        $this->addToScriptArray("images", json_encode($images));
        /* Начало вывода html */
        ob_start();
        ?>
        <div ng-repeat="block_name in fieldchernovik  | orderBy: 'block_sort'">
        <?php foreach ($this->chernovik as $ttt) { echo "<" . $ttt["block_direct"]; ?>
            value="block_name.fields[0].var_chern"
            value1="block_name.fields[1].var_chern"
            value2="block_name.fields[2].var_chern"
            sort="block_name.block_sort"
            sortup=fieldchernovik;
            sortdown=fieldchernovik[$index-1].block_sort;
            class0="block_name.block_style0"
            class1="block_name.block_style1"
            style="block_name.block_sty"
            idblock="block_name.block_id"
            ng-if="block_name.block_id=='<?=$ttt["block_id"]?>'">
        <?php echo '</'.$ttt["block_direct"].">\n"; } ?>
        </div>
        <div ng-controller='myCtrl' data-ng-init='fight(fieldchernovik)'>Автосохранение произойдет через <b>{{tst}}</b> секунд</div>
        <button ng-click='sendX("send", fieldchernovik); theTime2=20; insertBlock("addBlock", [{id_template: "<?=$this->idtemplater?>", id_content: selectedVal, classx: selectedValStyle}])'>Добавить блок</button>
        <button ng-click='publish("publish", fieldchernovik)'>Опубликовать</button>
        <button ng-click='sendX("send", fieldchernovik); theTime2=20;'>Сохранить черновик</button>
        <a href='show.php?id=<?=$this->idtemplater?>' target='_blank'><button>Просмотреть результат</button></a>
        <a href='index.php'><button>На главную</button></a>
        <select ng-model="selectedVal">
            <option ng-repeat="x in fieldcontent" value="{{x.id}}">{{x.name}}</option>
        </select>
        <select ng-model="selectedValStyle">
            <option ng-repeat="xs in fieldstyle" value="{{xs.nameclass}}">{{xs.name}}</option>
        </select>
        <img ng-if="selectedVal>0" src="img/content{{selectedVal}}.jpg" style="width: 300px; height: 200px;" />
        <?php
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

/** Вывод для показа скриптов контроллера и директив **/
    public function showControllersArrayToShow()
    {
        ob_start();
        ?>
        <script>
            // Объявляем контроллер
            app.controller('mainCTR', function($scope, $http, $interval) {
            // Блок глобальных переменных
                <?=$this->getScriptArray()?>
            // Конец блока глобальных переменных
            // Отладочные функции и переменные
            $scope.fun=function(){ console.log($scope.fieldchernovik) };
            });
            // конец функционала Ангуляра
            // Объявление директив
            <?php foreach ($this->chernovik as $chern) { ?>
            app.directive("sh<?=$chern['block_id']?>", function($compile) {return {
            scope: { value: "=", value1: "=", value2: "=", sort: "=", class0: "=", class1: "=", style: "=" },
            template : '<div class="row"><div class="col"><?=$this->securityStrip($chern['content'])?></div></div>'};
            });
            <?php } ?>
        </script>
        <?php
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

/** Вывод для редактора скриптов контроллера и директив **/
    public function showControllersArray()
    {
        //ввод переменных
        $this->addScriptsVarsArray("theTime", "");
        $this->addScriptsVarsArray("theTime2", "20");
        ob_start();
        ?>
        <script>
        // Объявляем контроллер
        app.controller('mainCTR', function($scope, $http, $interval) {
        // Блок глобальных переменных
        <?=$this->getScriptArray()?>
        // Конец блока глобальных переменных
        // Вывод обычных переменных
        <?=$this->getScriptsVarsArray()?>
        // Вывод post функций
        <?=$this->addAngularFunction("sendX", "oper, fields", "oper: oper, fields: fields", "\$scope.theTime=data.data; console.log(data.data); console.log(\$scope.fieldchernovik);")?>
        <?=$this->addAngularFunction("delete", "oper, mydata", "oper: oper, mydata: mydata", "location.reload();")?>
        <?=$this->addAngularFunction("insertBlock", "oper, mydata", "oper: oper, mydata: mydata", "location.reload();")?>
        <?=$this->addAngularFunction("publish", "oper, fields", "oper: oper, fields: fields", "\$scope.theTime=data.data;")?>
        // Вывод обычных функций
        $scope.tx = function(x) { if (confirm('Вы действительно хотите удалить блок?')) { $scope.delete('delete', x); } else { } };
        $scope.refresh=function (x,sort) { for(i=0; i<x.length; i++)  if (x[i].block_sort==sort) x[i].block_sort=sort+1; }
        $scope.refreshvniz=function (x,sort) { for(i=0; i<x.length; i++)  if (x[i].block_sort==sort) x[i].block_sort=sort-1; }
            // конец функционала Ангуляра
        });
        // Дополнительные контроллеры
        app.controller('myCtrl', function ($scope, $http, $interval) {
            $scope.tst=20;
            $scope.fight = function(data) {
                $interval(function () {
                    $scope.tst--;
                    if($scope.tst==0) {
                        $scope.sendRX("send", data);
                        $scope.tst=20;
                    }
                }, 1000);
            };
            <?=$this->addAngularFunction("sendRX", "oper, fields", "oper: oper, fields: fields", "console.log(data.data);")?>
        });
        // Директивы
        <?php foreach ($this->chernovik as $chern) { ?>
            app.directive("dir<?=$chern['block_id']?>", function($compile) {
            return {
            controller: "mainCTR",
            scope: { value: "=", value1: "=", value2: "=", sort: "=", sortup: "=", sortdown: "=", idblock: "=", class0: "=", class1: "=", style: "=" },
            <?php
            echo 'template : \'<div class="row">';
            echo '<div class="col" style="text-align: center">';
            echo '<span style="position: absolute; z-index: 1000; left:0; cursor: default;" ng-click="refresh(sortup, sort-1); sort=sort-1;">ᐃ</span><span style="position: absolute; z-index: 1000; left:0; top:20px; cursor: default;" ng-click="refreshvniz(sortup, sort+1); sort=sort+1;">ᐁ</span>';
            echo '<input type="checkbox" id="chkbx{{idblock}}" ng-model="showRedact" style="display: none"><label for="chkbx{{idblock}}">🔧</label>';
            echo '<input type="number" ng-model="sort" style="width: 64px; font-size: 10px;">';
            echo '</div>';
            echo '<div class="col-10">';
            echo $this->securityStrip($chern['content_develop']);
            echo '</div>';
            echo '<div class="col" style="text-align: center">';
            echo '<button ng-click="tx(idblock)" style="width: 25px; margin: 0px; text-align: center; margin-top: 5px; margin-bottom: 5px; border: 0px; color: white; background: red;">X</button>';
            echo '</div>';
            echo '<div class="col-12" style="background: beige" ng-show="showRedact">';
            echo 'Редактор <select ng-model="class0"><option ng-repeat="x in fieldstyle" value="{{x.nameclass}}">{{x.name}}</option></select>';
            echo 'шрифт <select ng-model="class1"><option ng-repeat="x in fieldfontfamily" value="{{x.nameclass}}">{{x.name}}</option></select>';
            echo 'Цвет текста <input type="color" ng-model="style.color"> ';
            echo 'Цвет фона <input type="color" ng-model="style.background"> ';
            echo 'Внешний отступ <input type="text" ng-model="style.margin"> ';
            echo 'Внутренний отступ <input type="text" ng-model="style.padding"> ';
            echo 'высота <input type="text" ng-model="style.height"> ';
            echo 'длинна <input type="text" ng-model="style.width"> ';
            echo '</div>\'}; });'."\n";
        } ?>
        </script>
        <?php
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

/** Ангуляр парсеры и шаблоны **/

    // Добавляет переменную в массив переменных скрипта
    public function addToScriptArray($block, $strBlockVars)
    {
        $grup=array();
        $grup[0]=$block;
        $grup[1]=$strBlockVars;
        array_push($this->scriptsArray, $grup);
        return 'field'.$block;
    }

    // Выводит данные в скрипт
    public function getScriptArray()
    {
        ob_start();
        for($i=0; $i<count($this->scriptsArray); $i++) { ?>
            // Глобальные переменная
            $scope.field<?=$this->scriptsArray[$i][0]?>=<?=$this->scriptsArray[$i][1]?>;
            // Конец переменной
        <?php }
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

    // Добавляет элемент в переменные ангуляра
    public function addScriptsVarsArray($var, $value)
    {
        $grup=array();
        $grup[0]=$var;
        $grup[1]=$value;
        array_push($this->scriptsVarsArray, $grup);
    }

    // Выводит данные в скрипт в виде переменных ангуляра
    public function getScriptsVarsArray()
    {
        ob_start();
        for($i=0; $i<count($this->scriptsVarsArray); $i++) {?>
            $scope.<?=$this->scriptsVarsArray[$i][0]?>='<?=$this->scriptsVarsArray[$i][1]?>';
        <?php }
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

    // Метод выводящий функцию
    public function addAngularFunction($nameFunction, $varibles, $body, $toDo="console.log(data.data);")
    {
        ob_start(); ?>
        $scope.<?=$nameFunction?> = function(<?=$varibles?>) {
        body = {<?=$body?>};
        $http({ method: 'post',  url: './core/poster.php', data: body, headers: { 'Content-Type': 'application/x-www-form-urlencoded' }})
        .then(function(data){ <?=$toDo?> })
        .catch(function(err){ console.log('error: ', err); return; });
        };
        <?php
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }

/** Безопасность **/
    // Обработчик строк
    public function securityStrip($str)
    {
        //$str=trim($str);
        //$str=strip_tags($str);
        //$str=str_replace(chr(13),'777',$str);
        //$str=str_replace(chr(10),'999',$str);
        return $this->jsaddslashes($str);
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


    public function showContentTags($str)
    {
        $str=str_replace("#B#",'<b>',$str);
        $str=str_replace("!#B#",'</b>',$str);
        return $str;
    }
}
?>
