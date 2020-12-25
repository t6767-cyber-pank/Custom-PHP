<?php
	if($GLOBAL->UserInfo->RightsUsers!=="Y"){
		header("Location: /admin/");
		exit;
	}

	$ParentId=0;
	$Crumbs=array("Управление пользователям");
	$ShowLanguages=true;
	$UserExist=false;

	$Direction="DESC";
	$PageDirection="DESC";

	$SortField="Name";
	$SortMethod="Name {$Direction}";

	$InPage=50;
	$PageNum=(int) @$_GET["p"];
	$PageNum=$PageNum<1?1:$PageNum;
	$LIMIT=$InPage*($PageNum-1);

	if(isset($_GET["Sort"])){
		$Direction=(isset($_GET["Direction"]) && $_GET["Direction"]=="DESC")?"DESC":"ASC";
		$PageDirection=$Direction;
		$Sort=$_GET["Sort"];

		$SortField=$Sort;

		$SortMethod="{$Sort} {$Direction}";

		$Direction=$Direction=="DESC"?"ASC":"DESC";
	}

	$DirectionArrow=$Direction=="ASC"?" <img src=\"/admin/i/desc.png\" alt=\"по убыванию\" title=\"по убыванию\" />":" <img src=\"/admin/i/asc.png\" alt=\"по возрастанию\" title=\"по возрастанию\" />";

	$Filters=array();
	$FiltersURL=array();

	if($GLOBAL->UserInfo->RightsAdmin=="N" && $GLOBAL->UserInfo->RightsUsers=="Y"){
		$Filters[]="(`UserId`={$GLOBAL->UserInfo->Id} OR Id={$GLOBAL->UserInfo->Id})";
	}

	if(isset($_GET["Filter"])){
		$Id=(int) @$_GET["Id"];
		if(!empty($Id)){
			$Filters[]="`Id`={$Id}";
			$FiltersURL[]="Id={$Id}";
		}
		$Login=addslashes(stripslashes(strip_tags(@$_GET["Login"])));
		if(!empty($Login)){
			$Filters[]="`Login` LIKE '%{$Login}%'";
			$FiltersURL[]="Login={$Login}";
		}
		$Name=addslashes(stripslashes(strip_tags(@$_GET["Name"])));
		if(!empty($Name)){
			$Filters[]="`Name` LIKE '%{$Name}%'";
			$FiltersURL[]="Name={$Name}";
		}
		$Phone=addslashes(stripslashes(strip_tags(@$_GET["Phone"])));
		if(!empty($Phone)){
			$Filters[]="`Phone` LIKE '%{$Phone}%'";
			$FiltersURL[]="Phone={$Phone}";
		}
		$EMail=addslashes(stripslashes(strip_tags(@$_GET["EMail"])));
		if(!empty($EMail)){
			$EMail=urldecode($EMail);
			$Filters[]="`EMail` LIKE '%{$EMail}%'";
			$FiltersURL[]="EMail={$EMail}";
		}
		$Status=@$_GET["Status"];
		if(!empty($Status)){
			$Filters[]="`Status`='{$Status}'";
			$FiltersURL[]="Status={$Status}";
		}
		$DateTime=@$_GET["DateTime"];
		if(!empty($DateTime)){
			$DateTime=explode("/",$DateTime);
			$StartTime=mktime(0,0,0,(int) $DateTime[1],(int) $DateTime[0],(int) $DateTime[2]);
			$FinishTime=mktime(23,59,59,(int) $DateTime[1],(int) $DateTime[0],(int) $DateTime[2]);

			$Filters[]="(`DateTime`>={$StartTime} AND `DateTime`<={$FinishTime})";
			$FiltersURL[]="DateTime={$_GET["DateTime"]}";
		}
		$LastLogin=@$_GET["LastLogin"];
		if(!empty($LastLogin)){
			$LastLogin=explode("/",$LastLogin);
			$StartTime=mktime(0,0,0,(int) $LastLogin[1],(int) $LastLogin[0],(int) $LastLogin[2]);
			$FinishTime=mktime(23,59,59,(int) $LastLogin[1],(int) $LastLogin[0],(int) $LastLogin[2]);

			$Filters[]="(`LastLogin`>={$StartTime} AND `LastLogin`<={$FinishTime})";
			$FiltersURL[]="LastLogin={$_GET["LastLogin"]}";
		}
	}

	$FiltersURL=count($FiltersURL)?("&Filter&".join("&",$FiltersURL)):"";
	$Filters=count($Filters)?(" WHERE ".join(" AND ",$Filters)):"";

	$AllUsers=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers}{$Filters} ORDER BY {$SortMethod} LIMIT {$LIMIT},{$InPage};");

	if(isset($_GET["Id"])){
		$Id=(int) $_GET["Id"];
		if($Id==1 && (int) $GLOBAL->UserInfo->Id!==1){
			$Id=-1;
		}

		if($UserInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers} WHERE Id={$Id};",false)){
			$UserExist=true;
		}
	}

	if(isset($_GET["AddForm"])){
		$ShowForm=true;
		$ShowList=false;
	}elseif(isset($_GET["EditForm"]) && $UserExist){
		$ShowForm=true;
		$ShowList=false;

		$IsEdit=true;
	}elseif(isset($_GET["Add"])){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"M";
		$SQL[]="`Status`='{$Status}'";

		$DateTime=time();
		$SQL[]="`DateTime`={$DateTime}";

		$Login=addslashes(stripslashes(strip_tags(@$_POST["Login"])));
		$SQL[]="`Login`='{$Login}'";

		$Name=addslashes(stripslashes(strip_tags(@$_POST["Name"])));
		$SQL[]="`Name`='{$Name}'";

		$Phone=$Kernel->MakePhoneLink(@$_POST["Phone"],false);
		$SQL[]="`Phone`='{$Phone}'";

		$EMail=urlencode(@$_POST["EMail"]);
		$SQL[]="`EMail`='{$EMail}'";

		$EditorTitle=htmlspecialchars(addslashes(stripslashes(strip_tags(@$_POST["EditorTitle"]))));
		$SQL[]="`EditorTitle`='{$EditorTitle}'";

		$WWW=urlencode(@$_POST["WWW"]);
		$SQL[]="`WWW`='{$WWW}'";

		if($GLOBAL->UserInfo->RightsAdmin=="Y"){
			$RightsAdmin=(isset($_POST["RightsAdmin"]) && $_POST["RightsAdmin"]=="Y")?"Y":"N";
			$RightsPages=(isset($_POST["RightsPages"]) && $_POST["RightsPages"]=="Y")?"Y":"N";
			$RightsModules=(isset($_POST["RightsModules"]) && $_POST["RightsModules"]=="Y")?"Y":"N";
			$RightsUsers=(isset($_POST["RightsUsers"]) && $_POST["RightsUsers"]=="Y")?"Y":"N";
			$RightsCategories=(isset($_POST["RightsCategories"]) && $_POST["RightsCategories"]=="Y")?"Y":"N";
			$RightsBrands=(isset($_POST["RightsBrands"]) && $_POST["RightsBrands"]=="Y")?"Y":"N";
			$RightsProducts=(isset($_POST["RightsProducts"]) && $_POST["RightsProducts"]=="Y")?"Y":"N";
			$RightsShares=(isset($_POST["RightsShares"]) && $_POST["RightsShares"]=="Y")?"Y":"N";
			$RightsSimTypes=(isset($_POST["RightsSimTypes"]) && $_POST["RightsSimTypes"]=="Y")?"Y":"N";
			$RightsBaskets=(isset($_POST["RightsBaskets"]) && $_POST["RightsBaskets"]=="Y")?"Y":"N";
		}else{
			$RightsAdmin=$GLOBAL->UserInfo->RightsAdmin;
			$RightsPages=$GLOBAL->UserInfo->RightsPages;
			$RightsModules=$GLOBAL->UserInfo->RightsModules;
			$RightsUsers=$GLOBAL->UserInfo->RightsUsers;
			$RightsCategories=$GLOBAL->UserInfo->RightsCategories;
			$RightsBrands=$GLOBAL->UserInfo->RightsBrands;
			$RightsProducts=$GLOBAL->UserInfo->RightsProducts;
			$RightsShares=$GLOBAL->UserInfo->RightsShares;
			$RightsSimTypes=$GLOBAL->UserInfo->RightsSimTypes;
			$RightsBaskets=$GLOBAL->UserInfo->RightsBaskets;
		}
		$SQL[]="`RightsAdmin`='{$RightsAdmin}'";
		$SQL[]="`RightsPages`='{$RightsPages}'";
		$SQL[]="`RightsModules`='{$RightsModules}'";
		$SQL[]="`RightsUsers`='{$RightsUsers}'";
		$SQL[]="`RightsCategories`='{$RightsCategories}'";
		$SQL[]="`RightsBrands`='{$RightsBrands}'";
		$SQL[]="`RightsProducts`='{$RightsProducts}'";
		$SQL[]="`RightsShares`='{$RightsShares}'";
		$SQL[]="`RightsSimTypes`='{$RightsSimTypes}'";
		$SQL[]="`RightsBaskets`='{$RightsBaskets}'";

		$Password=@$_POST["Pass"];
		$Password=$Kernel->CreatePasswordCash($Password,$DateTime);
		$SQL[]="`Password`='{$Password}'";

		$SQL[]="`UserId`={$GLOBAL->UserInfo->Id}";

		$SQL=join(",",$SQL);

		$LastId=$DataBase->InsertQuery("INSERT INTO {$GLOBAL->SystemUsers} SET {$SQL};");

		header("Location: ?EditForm&Id={$LastId}&updated");
		exit();
	}elseif(isset($_GET["Edit"]) && $UserExist){
		$SQL=array();

		$Status=(isset($_POST["Status"]) && $_POST["Status"]=="Y")?"Y":"N";
		$SQL[]="`Status`='{$Status}'";

		$Login=addslashes(stripslashes(strip_tags(@$_POST["Login"])));
		$SQL[]="`Login`='{$Login}'";

		$Name=addslashes(stripslashes(strip_tags(@$_POST["Name"])));
		$SQL[]="`Name`='{$Name}'";

		$Phone=$Kernel->MakePhoneLink(@$_POST["Phone"],false);
		$SQL[]="`Phone`='{$Phone}'";

		$EMail=urlencode(@$_POST["EMail"]);
		$SQL[]="`EMail`='{$EMail}'";

		$EditorTitle=htmlspecialchars(addslashes(stripslashes(strip_tags(@$_POST["EditorTitle"]))));
		$SQL[]="`EditorTitle`='{$EditorTitle}'";

		$WWW=urlencode(@$_POST["WWW"]);
		$SQL[]="`WWW`='{$WWW}'";

		if($GLOBAL->UserInfo->RightsAdmin=="Y"){
			$RightsAdmin=(isset($_POST["RightsAdmin"]) && $_POST["RightsAdmin"]=="Y")?"Y":"N";
			$RightsPages=(isset($_POST["RightsPages"]) && $_POST["RightsPages"]=="Y")?"Y":"N";
			$RightsModules=(isset($_POST["RightsModules"]) && $_POST["RightsModules"]=="Y")?"Y":"N";
			$RightsUsers=(isset($_POST["RightsUsers"]) && $_POST["RightsUsers"]=="Y")?"Y":"N";
			$RightsCategories=(isset($_POST["RightsCategories"]) && $_POST["RightsCategories"]=="Y")?"Y":"N";
			$RightsBrands=(isset($_POST["RightsBrands"]) && $_POST["RightsBrands"]=="Y")?"Y":"N";
			$RightsProducts=(isset($_POST["RightsProducts"]) && $_POST["RightsProducts"]=="Y")?"Y":"N";
			$RightsShares=(isset($_POST["RightsShares"]) && $_POST["RightsShares"]=="Y")?"Y":"N";
			$RightsSimTypes=(isset($_POST["RightsSimTypes"]) && $_POST["RightsSimTypes"]=="Y")?"Y":"N";
			$RightsBaskets=(isset($_POST["RightsBaskets"]) && $_POST["RightsBaskets"]=="Y")?"Y":"N";
		}else{
			$RightsAdmin=$GLOBAL->UserInfo->RightsAdmin;
			$RightsPages=$GLOBAL->UserInfo->RightsPages;
			$RightsModules=$GLOBAL->UserInfo->RightsModules;
			$RightsUsers=$GLOBAL->UserInfo->RightsUsers;
			$RightsCategories=$GLOBAL->UserInfo->RightsCategories;
			$RightsBrands=$GLOBAL->UserInfo->RightsBrands;
			$RightsProducts=$GLOBAL->UserInfo->RightsProducts;
			$RightsShares=$GLOBAL->UserInfo->RightsShares;
			$RightsSimTypes=$GLOBAL->UserInfo->RightsSimTypes;
			$RightsBaskets=$GLOBAL->UserInfo->RightsBaskets;
		}
		$SQL[]="`RightsAdmin`='{$RightsAdmin}'";
		$SQL[]="`RightsPages`='{$RightsPages}'";
		$SQL[]="`RightsModules`='{$RightsModules}'";
		$SQL[]="`RightsUsers`='{$RightsUsers}'";
		$SQL[]="`RightsCategories`='{$RightsCategories}'";
		$SQL[]="`RightsBrands`='{$RightsBrands}'";
		$SQL[]="`RightsProducts`='{$RightsProducts}'";
		$SQL[]="`RightsShares`='{$RightsShares}'";
		$SQL[]="`RightsSimTypes`='{$RightsSimTypes}'";
		$SQL[]="`RightsBaskets`='{$RightsBaskets}'";

		$Password=@$_POST["Pass"];
		if(!empty($Password)){
			$Password=$Kernel->CreatePasswordCash($Password, $UserInfo->DateTime);
			$SQL[]="`Password`='{$Password}'";
		}

		$SQL=join(",",$SQL);

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET {$SQL} WHERE Id={$UserInfo->Id};");

		header("Location: ?EditForm&Id={$UserInfo->Id}&updated");
		exit();
	}elseif(isset($_GET["ChangeStatus"]) && $UserInfo){
		$Status=(isset($_POST["Status{$UserInfo->Id}"]) && $_POST["Status{$UserInfo->Id}"]=="Y")?"Y":"N";

		$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemUsers} SET `Status`='{$Status}' WHERE Id={$UserInfo->Id};");

		exit();
	}elseif(isset($_GET["Delete"]) && $CanDelete){
		$DelIds=array();
		$AllUsers=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemUsers};");
		foreach($AllUsers as $User){
			if((int) $User->Id!==1 && isset($_POST["Chk{$User->Id}"]) && $_POST["Chk{$User->Id}"]==$User->Id){
				$DelIds[$User->Id]=$User->Id;
				$Kernel->ClearThumbnails($DIRS->Editors,$User->Id,"jpg");
			}

			unset($User);
		}
		if(count($DelIds)){
			$DelIds=join(",",$DelIds);

			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->SystemUsers} WHERE Id IN ({$DelIds});");
		}

		header("Location: ?deleted");
		exit();
	}

	$UserCount=$DataBase->Query("SELECT COUNT(*) AS Users FROM {$GLOBAL->SystemUsers};",false);
	$UserCount=(int) @$UserCount->Users;
	$Pages=ceil($UserCount/$InPage);

	$Crumbs=join(" / ",$Crumbs);
?>
<?$Design->LoadHeader($Crumbs);?>
<?php
	if($ShowList){
?>
<form action="?Delete" method="post" delquestion="выбранных пользователей" id="GroupChecked">
	<section id="listBlock">
		<div class="title" id="formTitle">
			<h1><?=$Crumbs?></h1>
			<div class="buttons">
				<a href="?AddForm" class="button">Добавить пользователя</a>
				<button class="button filter" id="filter">Применить фильтр</button>
				<button class="button delete">Удалить</button>
				<div class="pages">
					<span>Всего пользователей: <strong><?=$UserCount?></strong></span>
					<a href="?Filter&Sort=<?=$SortField?>&Direction=<?=$PageDirection.$FiltersURL?>&p=1" class="button page">&laquo;</a>
					<a href="?Filter&Sort=<?=$SortField?>&Direction=<?=$PageDirection.$FiltersURL?>&p=<?=$PageNum>1?($PageNum-1):1?>" class="button page">&lsaquo;</a>
					<?=$HtmlTags->InputText("p",$PageNum,"","text","p","text",11,false,5)?> из <?=$Pages?>
					<a href="?Filter&Sort=<?=$SortField?>&Direction=<?=$PageDirection.$FiltersURL?>&p=<?=$PageNum<($Pages)?($PageNum+1):($Pages)?>" class="button page">&rsaquo;</a>
					<a href="?Filter&Sort=<?=$SortField?>&Direction=<?=$PageDirection.$FiltersURL?>&p=<?=$Pages?>" class="button page">&raquo;</a>
				</div>
			</div>
		</div>
		<div class="container">
			<table class="table" id="Positions">
				<colgroup>
					<col />
					<col />
					<col width="20%" />
					<col width="20%" />
					<col width="20%" />
				</colgroup>
				<thead>
					<tr>
						<th class="checkbox"><?=$HtmlTags->InputCheckbox("ChkAll","Y","",false,"","ChkAll","",true,"checkbox",true)?></th>
						<th><a href="?Sort=Id&Direction=<?=$Direction?><?=$FiltersURL?>">Id<?=$SortField=="Id"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Login&Direction=<?=$Direction?><?=$FiltersURL?>">Логин<?=$SortField=="Login"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Name&Direction=<?=$Direction?><?=$FiltersURL?>">Имя<?=$SortField=="Name"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=EMail&Direction=<?=$Direction?><?=$FiltersURL?>">E-mail<?=$SortField=="EMail"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=DateTime&Direction=<?=$Direction?><?=$FiltersURL?>">Дата создания<?=$SortField=="DateTime"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=LastLogin&Direction=<?=$Direction?><?=$FiltersURL?>">Последняя авторизация<?=$SortField=="LastLogin"?$DirectionArrow:""?></a></th>
						<th><a href="?Sort=Status&Direction=<?=$Direction?><?=$FiltersURL?>">Статус<?=$SortField=="Status"?$DirectionArrow:""?></a></th>
					</tr>
					<tr class="filters">
						<th></th>
						<th><?=$HtmlTags->InputText("Id",@$_GET["Id"],"","text","Id","text")?></th>
						<th><?=$HtmlTags->InputText("Login",@$_GET["Login"],"","text","Login","text")?></th>
						<th><?=$HtmlTags->InputText("Name",@$_GET["Name"],"","text","Name","text")?></th>
						<th><?=$HtmlTags->InputText("EMail",@$_GET["EMail"],"","text","EMail","text")?></th>
						<th><?=$HtmlTags->InputText("DateTime",@$_GET["DateTime"],"","text","DateTime","text")?></th>
						<th><?=$HtmlTags->InputText("LastLogin",@$_GET["LastLogin"],"","text","LastLogin","text")?></th>
						<th>
							<select name="Status" id="Status">
								<?=$HtmlTags->option(0,"Все",@$_GET["Status"]==0)?>
								<?=$HtmlTags->option("Y","Активные",@$_GET["Status"]=="P")?>
								<?=$HtmlTags->option("N","Заблокирован",@$_GET["Status"]=="M")?>
							</select>
						</th>
					</tr>
				</thead>
				<tbody>
<?php
		foreach($AllUsers as $User){
?>
					<tr fid="<?=$User->Id?>">
						<td><?=$HtmlTags->InputCheckbox("Chk{$User->Id}",$User->Id,"",false,"","Chk{$User->Id}","",true,"checkbox",true)?></td>
						<td><a href="?EditForm&Id=<?=$User->Id?>"><?=$User->Id?></a></td>
						<td><a href="?EditForm&Id=<?=$User->Id?>"><?=$User->Login?></a></td>
						<td><a href="?EditForm&Id=<?=$User->Id?>"><?=$User->Name?></a></td>
						<td><a href="?EditForm&Id=<?=$User->Id?>"><?=urldecode($User->EMail)?></a></td>
						<td><?=date("d.m.Y",$User->DateTime)?></td>
						<td><?=date("d.m.Y",$User->LastLogin)?></td>
						<td><?=$HtmlTags->InputCheckbox("Status{$User->Id}","Y","",$User->Status=="Y","","Status{$User->Id}","",true,"checkbox-slider",true)?></td>
					</tr>
<?php
			unset($User);
		}
?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="8"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</section>
</form>
<?php
	}elseif($ShowForm){
?>
<form action="?<?=$IsEdit?"Edit&Id={$UserInfo->Id}":"Add"?>" method="post" enctype="multipart/form-data">
	<section>
		<div class="title" id="formTitle">
			<h1><?=$IsEdit?"Редактирование существующего":"Добавление нового"?> пользователя:</h1>
			<div class="buttons">
				<a href="?Id=<?=$ParentId?>" class="back">Вернуться</a>
				<button class="button add"><?=$IsEdit?"Применить":"Добавить"?></button>
			</div>
		</div>
		<div class="container">
			<table class="table">
				<thead>
					<tr>
						<th colspan="2" class="tl tr">Информация</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Логин:</th>
						<td><?=$HtmlTags->InputText("Login",$IsEdit?$UserInfo->Login:"","","text","Login","text")?>
					</tr>
					<tr>
						<th>ФИО:</th>
						<td><?=$HtmlTags->InputText("Name",$IsEdit?$UserInfo->Name:"","","text","Name","text")?>
					</tr>
					<tr>
						<th>EMail:</th>
						<td><?=$HtmlTags->InputText("EMail",$IsEdit?urldecode($UserInfo->EMail):"","","email","EMail","text")?>
					</tr>
					<tr>
						<th>Телефон:</th>
						<td><?=$HtmlTags->InputText("Phone",$IsEdit?$UserInfo->Phone:"","","text","Phone","text")?></td>
					</tr>
					<tr>
						<th>Пароль:</th>
						<td><?=$HtmlTags->InputText("Pass","","","password","Pass","text")?>
					</tr>
					<tr>
						<th>Статусы/доступы:</th>
						<td>
							<?=$HtmlTags->InputCheckbox("Status","Y","активен",$IsEdit?$UserInfo->Status=="Y":true,"","Status","",true,"checkbox-slider",true)?>
							<?=$HtmlTags->InputCheckbox("RightsAdmin","Y","администратор",$IsEdit?$UserInfo->RightsAdmin=="Y":true,"","RightsAdmin","",true,"checkbox-slider",true,$GLOBAL->UserInfo->RightsAdmin!=="Y")?>
							<?=$HtmlTags->InputCheckbox("RightsPages","Y","редактор страниц",$IsEdit?$UserInfo->RightsPages=="Y":true,"","RightsPages","",true,"checkbox-slider",true,$GLOBAL->UserInfo->RightsAdmin!=="Y")?>
							<?=$HtmlTags->InputCheckbox("RightsUsers","Y","админ.пользователей",$IsEdit?$UserInfo->RightsUsers=="Y":true,"","RightsUsers","",true,"checkbox-slider",true,$GLOBAL->UserInfo->RightsAdmin!=="Y")?>
							<?=$HtmlTags->InputCheckbox("RightsCategories","Y","админ.категорий",$IsEdit?$UserInfo->RightsCategories=="Y":true,"","RightsCategories","",true,"checkbox-slider",true,$GLOBAL->UserInfo->RightsAdmin!=="Y")?>
							<?=$HtmlTags->InputCheckbox("RightsBrands","Y","админ.брендов",$IsEdit?$UserInfo->RightsBrands=="Y":true,"","RightsBrands","",true,"checkbox-slider",true,$GLOBAL->UserInfo->RightsAdmin!=="Y")?>
							<?=$HtmlTags->InputCheckbox("RightsProducts","Y","админ.продуктов",$IsEdit?$UserInfo->RightsProducts=="Y":true,"","RightsProducts","",true,"checkbox-slider",true,$GLOBAL->UserInfo->RightsAdmin!=="Y")?>
							<?=$HtmlTags->InputCheckbox("RightsShares","Y","админ.акций",$IsEdit?$UserInfo->RightsShares=="Y":true,"","RightsShares","",true,"checkbox-slider",true,$GLOBAL->UserInfo->RightsAdmin!=="Y")?>
							<?=$HtmlTags->InputCheckbox("RightsSimTypes","Y","админ.sim",$IsEdit?$UserInfo->RightsSimTypes=="Y":true,"","RightsSimTypes","",true,"checkbox-slider",true,$GLOBAL->UserInfo->RightsAdmin!=="Y")?>
							<?=$HtmlTags->InputCheckbox("RightsBaskets","Y","админ. заказов",$IsEdit?$UserInfo->RightsBaskets=="Y":true,"","RightsBaskets","",true,"checkbox-slider",true,$GLOBAL->UserInfo->RightsAdmin!=="Y")?>
						</td>
					</tr>
					<tr>
						<th></th>
					</tr>
				</tbody>
			</table>
		</div>
	</section>
</form>
<?php
	}
?>
<?$Design->LoadFooter();?>