<?php
require_once("./classes/DB.php");
$db=new DB();
$templ=json_encode($db->getJsonTemplateById());
$ct=json_encode($db->getCategoryJSON());
$allcategory=json_encode($db->getAllCategoryJSON());
$allParentCategory=json_encode($db->getAllParentCategoryJSON());
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="angular.min.js"></script>
    <script src="angular-sanitize.js"></script>
    <script>var app = angular.module('myApp', ['ngSanitize']);</script>
    <title>Что посоветуешь Думатель?</title>
    <link href="css/main.css" rel="stylesheet" type="text/css" >
    <style>
        *:focus {
            outline: none;
        }
    </style>
</head>
<body>
<div class="container" ng-app="myApp" ng-controller="mainCTR">
    <nav class="navbar navbar-light bg-light">
        <form>
        <button class="btn btn-primary" ng-click="hC(1)" type="button">Добавить категорию</button>
        <button class="btn btn-primary" ng-click="hH(1)" type="button">Добавить шаблон</button>
        </form>
    </nav>
    <div style="position: fixed; z-index: 100; background: cornflowerblue; left: 34%; top: 30%; padding: 20px; border-radius: 46px; width: 465px;" ng-show="hideCat">
        <div class="row">
            <div class="col">
                <div class="col-12">
                <h2 style="color: white; text-align: center;">Добавить категорию</h2>
                </div>
                <div class="col-12">
                    <span style="color: white">Введите название: </span><input type="text" ng-model="editArrayCategory[0]" style="width: 100%; border: 0px; border-radius: 15px; padding-left: 15px; padding-right: 15px;">
                </div>
                <div class="col-12">
                    <span style="color: white">Введите принадлежность категории: </span>
                <select ng-model="editArrayCategory[1]" style="width: 100%; border-radius: 15px; border: 0px; padding-left: 15px; padding-right: 15px;">
                <option value="0" default>Родительская</option>
                <option ng-repeat="x in categoryAllParent" value="{{x.id}}">{{x.name}}</option>
                </select>
                </div>
            </div>
            <div class="col-12">
                <button ng-click="sendRX('editCategory', editArrayCategory)" style="text-align: center; font-size: 24px; margin-top: 30px; border-radius: 20px; border: 0px; background: transparent; color: white; width: 100%;">Добавить категорию</button>
            </div>
        </div>
    </div>
    <div style="position: fixed; width: 100%; left:0; top:0; height: 100%; background: black; opacity: 0.4; z-index: 20;" ng-show="hideCat">
            <button ng-click="hC(0)" style="float: right; margin-top: 1%; margin-right: 3%; font-size: 56px; width: 40px; background: black; color: white; border: 0px;">x</button>
    </div>


    <div style="position: fixed; z-index: 100; background: cornflowerblue; left: 34%; top: 30%; padding: 20px; border-radius: 46px; width: 465px;" ng-show="hideHT">
        <div class="row">
            <div class="col">
                <div class="col-12">
                    <h2 style="color: white; text-align: center;">Добавить шаблон</h2>
                </div>
                <div class="col-12">
                    <span style="color: white">Введите название: </span><input type="text" ng-model="editArray[0]" style="width: 100%; border: 0px; border-radius: 15px; padding-left: 15px; padding-right: 15px;">
                </div>
                <div class="col-12">
                    <span style="color: white">Выберите категорию: </span>
                    <select ng-model="editArray[1]" style="width: 100%; border-radius: 15px; border: 0px; padding-left: 15px; padding-right: 15px;">
                        <option ng-repeat="x in categoryAll" value="{{x.id}}">{{x.name}}</option>
                    </select>
                </div>
            </div>
            <div class="col-12">
                <button ng-click="sendRX('editContent', editArray)" style="text-align: center; font-size: 24px; margin-top: 30px; border-radius: 20px; border: 0px; background: transparent; color: white; width: 100%;">Добавить шаблон</button>
            </div>
        </div>
    </div>
    <div style="position: fixed; width: 100%; left:0; top:0; height: 100%; background: black; opacity: 0.4; z-index: 20;" ng-show="hideHT">
        <button ng-click="hH(0)" style="float: right; margin-top: 1%; margin-right: 3%; font-size: 56px; width: 40px; background: black; color: white; border: 0px;">x</button>
    </div>

    <div ng-repeat="cat in categoryVar">
        <div class="row">
            <div class="col-10">
                <h1 ng-bind="cat[0].name" class="header"></h1>
            </div>
            <div class="col-2">
            <input type="checkbox" id="catShow{{cat[0].id}}" ng-model="cat[0].catShow" style="display: none">
            <label for="catShow{{cat[0].id}}"><span ng-if="cat[0].catShow==true">❌ скрыть</span><span ng-if="(cat[0].catShow==false) || (cat[0].catShow=='x')">🔎 показать</span></label>
            </div>
            <div class="col" ng-if="cat[0].catShow==true">
                <input type="text" ng-model="cat[0].name" class="edtext">
                <button ng-click="sendRX('ChangeNameCategory', cat[0])" class="edbutton">Изменить</button>
            </div>
            <div class="col-1" ng-if="cat[0].catShow==true">
                <button ng-click="sendRX('deleteCategory', cat[0])" class="edbuttonX">X</button>
            </div>
        </div>

        <div ng-repeat="templ in cat[1]">
            <div class="row">
                    <div class="col-9">
                        <a href="show.php?id={{templ.id}}" style="margin-left: 50px">{{templ.tempName}}</a>
                    </div>
                    <div class="col-1">
                        <form method="post" action="constructor.php" style="margin: 0px;">
                            <input type="hidden" name="id" value="{{templ.id}}">
                            <input type="submit" value="Редактор" class="buttonPostChr">
                        </form>
                    </div>
                <div class="col-2">
                    <input type="checkbox" id="tempmainshow{{templ.id}}" ng-model="templ.tempmainshow" style="display: none">
                    <label for="tempmainshow{{templ.id}}"><span ng-if="templ.tempmainshow==true">❌ скрыть</span><span ng-if="(templ.tempmainshow==false) || (templ.tempmainshow=='x')">🔎 показать</span></label>
                </div>
                    <div class="col" ng-if="templ.tempmainshow==true" >
                        <input type="text" ng-model="templ.tempName" class="edtextPar">
                        <select ng-model="templ.id_category" class="edtextPar">
                            <option ng-repeat="x in categoryAll" value="{{x.id}}">{{x.name}}</option>
                        </select>
                        <button ng-click="sendRX('changeTemplate', templ)" class="edtextPar">Изменить данные</button>
                    </div>
                    <div class="col-1" ng-if="templ.tempmainshow==true" >
                        <button ng-click="sendRX('deleteTemplate', templ)" class="edbuttonX">X</button>
                    </div>
                </div>
        </div>

        <div ng-repeat="subcat in cat[2]">
            <div class="row">
                <div class="col-10">
                    <h2 ng-bind="subcat[0].name" class="subheader"></h2>
                </div>
                <div class="col-2">
                    <input type="checkbox" id="subcatShow{{subcat[0].id}}" ng-model="subcat[0].subcatShow" style="display: none">
                    <label for="subcatShow{{subcat[0].id}}"><span ng-if="subcat[0].subcatShow==true">❌ скрыть</span><span ng-if="(subcat[0].subcatShow==false) || (subcat[0].subcatShow=='x')">🔎 показать</span></label>
                </div>
                <div class="col" ng-if="subcat[0].subcatShow==true">
                <input type="text" ng-model="subcat[0].name" class="edtextsubheder">
                    <button ng-click="sendRX('ChangeNameCategory', subcat[0])" class="edtextsubheder">Изменить</button>
                </div>
                <div class="col-1" ng-if="subcat[0].subcatShow==true" >
                    <button ng-click="sendRX('deleteCategory', subcat[0])" class="edbuttonX">X</button>
                </div>
            </div>
            <div ng-repeat="templ in subcat[1]">
                <div class="row">
                    <div class="col-9">
                        <a href="show.php?id={{templ.id}}" style="margin-left: 50px">{{templ.tempName}}</a>
                    </div>
                    <div class="col-1">
                        <form method="post" action="constructor.php" style="margin: 0px;">
                            <input type="hidden" name="id" value="{{templ.id}}">
                            <input type="submit" value="Редактор" class="buttonPostChr">
                        </form>
                    </div>
                    <div class="col-2">
                        <input type="checkbox" id="tempsubshow{{templ.id}}" ng-model="templ.tempsubshow" style="display: none">
                        <label for="tempsubshow{{templ.id}}"><span ng-if="templ.tempsubshow==true">❌ скрыть</span><span ng-if="(templ.tempsubshow==false) || (templ.tempsubshow=='x')">🔎 показать</span></label>
                    </div>
                    <div class="col" ng-if="templ.tempsubshow==true" >
                        <input type="text" ng-model="templ.tempName" class="edtextPar">
                        <select ng-model="templ.id_category" class="edtextPar">
                            <option ng-repeat="x in categoryAll" value="{{x.id}}">{{x.name}}</option>
                        </select>
                        <button ng-click="sendRX('changeTemplate', templ)" class="edtextPar">Изменить данные</button>
                    </div>
                    <div class="col-1" ng-if="templ.tempsubshow==true" >
                        <button ng-click="sendRX('deleteTemplate', templ)" class="edbuttonX">X</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    app.controller('mainCTR', function($scope, $http, $interval) {
        $scope.templVar=<?=$templ?>;
        $scope.categoryVar=<?=$ct?>;
        $scope.categoryAll=<?=$allcategory?>;
        $scope.categoryAllParent=<?=$allParentCategory?>;
        $scope.var="0";
        $scope.hideCat=0;
        $scope.hideHT=0;
        $scope.xxx=function (x) {
            $scope.var=$scope.templVar[x].id;
        };
        $scope.editArray=["", "1"];
        $scope.editArrayCategory=["", "0"];
        $scope.hC=function(x) { $scope.hideCat=x; }
        $scope.hH=function(x) { $scope.hideHT=x; }
        $scope.sendRX = function(oper, fields) {
            body = {oper: oper, fields: fields};
            var chose = confirm("Вы уверены что хотите сделать это?");
            if (fields[0]!="" && chose==true) {
                $http({
                    method: 'post',
                    url: './core/poster.php',
                    data: body,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                    .then(function (data) {
                        console.log(data.data);
                        location.reload();
                    })
                    .catch(function (err) {
                        console.log('error: ', err);
                        return;
                    });
            } else { if (fields[0]=="") alert("Значение не должно быть пустым"); }
        };
    });
</script>
</body>
</html>
