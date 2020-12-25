<?php
	include("include/kernel.inc");
	include("../include/configs.inc");

	$Kernel=new Kernel();
	$Kernel->IsAdmin();

	$errorMsg="";
	$IsCategoryEdit=false;
	$IsModuleEdit=false;

	$CanEdit=($GLOBAL->UserInfo->RightsAdmin=="Y" && $GLOBAL->UserInfo->Id==1);
	$CanAdmin=($CanEdit || $GLOBAL->UserInfo->RightsModules=="Y");

	if(!isset($work) && isset($_GET["work"])){
		extract($_GET);
		extract($_POST);
	}
	if(isset($work)){
		if($work=="AddCategory" && $CanEdit){
			$Title=htmlspecialchars($Title);
			$DataBase->InsertQuery("INSERT INTO {$GLOBAL->SystemModulesCategories} SET Title='{$Title}',`Type`='{$Type}';");
			header("Location: {$_SERVER['PHP_SELF']}");
			exit();
		}elseif($work=="EditCategory" && $CanEdit){
			$CategoryId=(int) $CategoryId;
			$Title=htmlspecialchars($Title);
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemModulesCategories} SET Title='{$Title}',`Type`='{$Type}' WHERE Id={$CategoryId};");
			header("Location: {$_SERVER['PHP_SELF']}");
			exit();
		}elseif($work=="EditCategoryForm" && $CanEdit){
			$CategoryId=(int) $CategoryId;
			if($CategoryInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemModulesCategories} WHERE Id={$CategoryId};",false)){
				$IsCategoryEdit=true;
			}
		}elseif($work=="DelCategory" && $CanEdit){
			$CategoryId=(int) $CategoryId;
			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->SystemModulesCategories} WHERE Id={$CategoryId};");
			header("Location: {$_SERVER['PHP_SELF']}");
			exit();
		}elseif($work=="AddModule" && $CanEdit){
			$Title=htmlspecialchars($Title);
			$Categor=(int) $Categor;
			$DataBase->InsertQuery("INSERT INTO {$GLOBAL->SystemModules} SET Title='{$Title}',Categor={$Categor},`File`='{$_FILES['File']['name']}';");
			move_uploaded_file($_FILES['File']['tmp_name'],"..".$DIRS->modules.$_FILES['File']['name']);
			header("Location: {$_SERVER['PHP_SELF']}");
			exit();
		}elseif($work=="EditModule" && $CanEdit){
			$Title=htmlspecialchars($Title);
			$Categor=(int) $Categor;
			$ModuleInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemModules} WHERE Id={$ModuleId};",false);
			$File="";
			if(!empty($_FILES['File']['tmp_name'])){
				@unlink("..".$DIRS->modules.$ModuleInfo->File);
				move_uploaded_file($_FILES['File']['tmp_name'],"..".$DIRS->modules.$_FILES['File']['name']);
				$File=",`File`='{$_FILES['File']['name']}'";
			}
			$DataBase->UpdateQuery("UPDATE {$GLOBAL->SystemModules} SET Title='{$Title}',Categor={$Categor}{$File} WHERE Id={$ModuleId};");
			header("Location: {$_SERVER['PHP_SELF']}");
			exit();
		}elseif($work=="EditModuleForm" && $CanEdit){
			$ModuleId=(int) $ModuleId;
			if($ModuleInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemModules} WHERE Id={$ModuleId};",false)){
				$IsModuleEdit=true;
			}
		}elseif($work=="DelModule" && $CanEdit){
			$ModuleId=(int) $ModuleId;
			$ModuleInfo=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemModules} WHERE Id={$ModuleId};",false);
			@unlink("..".$DIRS->modules.$ModuleInfo->File);
			$DataBase->UpdateQuery("DELETE FROM {$GLOBAL->SystemModules} WHERE Id={$ModuleId};");
			header("Location: {$_SERVER['PHP_SELF']}");
			exit();
		}
	}

	$AdminTitle="Административные модули";

	include("include/header.inc");

	if($CanEdit){
?>
<fieldset><legend>Администрирование:</legend>
	<table width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr>
			<td width="50%" valign="top">
<?php
	$AllModules=$DataBase->Query("SELECT *,(SELECT Title FROM {$GLOBAL->SystemModulesCategories} WHERE Id={$GLOBAL->SystemModules}.Categor) AS CategoryName FROM {$GLOBAL->SystemModules} ORDER BY Categor;");
	if(count($AllModules)>0){
?>
				<fieldset><legend>Все модули:<?=($IsModuleEdit)?"&nbsp;&nbsp;&nbsp;<img src=\"i/add.gif\" alt=\"Добавить новый\"> <a href=\"{$_SERVER['PHP_SELF']}\">Добавить новый</a>":""?></legend>
					<table width="100%" border="0" cellpadding="1" cellspacing="0">
						<colgroup>
							<col width="40%" />
							<col width="30%" />
							<col width="30%" />
						</colgroup>
						<tr>
							<th>Название</th>
							<th>Категория</th>
							<th>Имя файла</th>
							<th align="right"><img src="i/del.gif" alt="Удалить"></th>
						</tr>
<?php
	$ThisItem=0;
	foreach($AllModules as $Module){
?>
						<tr <?php echo ($ThisItem++ % 2)?"class=\"line\"":""?>>
							<td><a href="?work=EditModuleForm&ModuleId=<?php echo $Module->Id?>"><?php echo stripslashes($Module->Title)?></a></td>
							<td><?php echo $Module->CategoryName?></td>
							<td><?php echo $Module->File?></td>
							<td align="right"><a href="?work=DelModule&ModuleId=<?php echo $Module->Id?>"><img src="i/del.gif" alt="Удалить"></a></td>
						</tr>
<?php
	}
?>
					</table>
				</fieldset>
				<hr>
<?php
	}
?>
				<fieldset><legend><?php echo ($IsModuleEdit)?"Редактирование":"Добавление нового"?> модуля:</legend>
				<form action="?work=<?php echo ($IsModuleEdit)?"EditModule&ModuleId=$ModuleInfo->Id":"AddModule"?>" method="POST" enctype="multipart/form-data">
					<table width="100%" border="0" cellpadding="1" cellspacing="0">
						<tr>
							<th width="30%">Название:</th>
							<td width="70%"><?php echo $HtmlTags->InputText("Title",($IsModuleEdit)?stripslashes($ModuleInfo->Title):"","width:100%")?></td>
						</tr>
						<tr>
							<th>Категория:</th>
							<td><select name="Categor">
<?php
	$AllCategories=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemModulesCategories} ORDER BY `Type`,Title;");
	foreach($AllCategories as $Category){
		echo $HtmlTags->option($Category->Id,"[$Category->Type] ".stripslashes($Category->Title),($IsModuleEdit)?($ModuleInfo->Categor==$Category->Id):false);
	}
?>
							</select></td>
						</tr>
						<tr>
							<th>Файл:</th>
							<td><?php echo $HtmlTags->InputText("File","","width:100%","file")?></td>
						</tr>
						<tr>
							<td colspan="2" class="tp">
								<?php echo $HtmlTags->InputSubmit(($IsModuleEdit)?"Обновить":"Добавить")?>
								<?php echo $HtmlTags->InputSubmit("Сбросить","","","",true,"reset")?>
							</td>
						</tr>
					</table>
				</form>
				</fieldset>
			</td>
			<td width="50%" valign="top">
<?php
		if($AllCategories=$DataBase->Query("SELECT * FROM {$GLOBAL->SystemModulesCategories} ORDER BY Title;")){
?>
				<fieldset><legend>Все категории:<?php echo ($IsCategoryEdit)?"&nbsp;&nbsp;&nbsp;<img src=\"i/add.gif\" alt=\"Добавить\"> <a href=\"{$_SERVER['PHP_SELF']}\">Добавить</a>":""?></legend>
					<table width="100%" border="0" cellpadding="1" cellspacing="0">
						<tr>
							<th width="100%">Название категории</th>
							<th width="10%" align="right"><img src="i/del.gif" alt="Удалить"></th>
						</tr>
<?php
	$ThisItem=0;
	foreach($AllCategories as $Category){
?>
						<tr <?php echo ($ThisItem++ % 2)?"class=\"line\"":""?>>
							<td><a href="?work=EditCategoryForm&CategoryId=<?php echo $Category->Id?>"><?php echo stripslashes($Category->Title)?></a></td>
							<td align="right"><a href="?work=DelCategory&CategoryId=<?php echo $Category->Id?>"><img src="i/del.gif" alt="Удалить"></a></td>
						</tr>
<?php
	}
?>
					</table>
				</fieldset>
				<hr>
<?php
		}
?>
				<fieldset><legend><?php echo ($IsCategoryEdit)?"Редактирование":"Добавление"?> категории:</legend>
					<form action="?work=<?php echo ($IsCategoryEdit)?"EditCategory&CategoryId=$CategoryInfo->Id":"AddCategory"?>" method="POST">
					<table width="100%" border="0" cellpadding="1" cellspacing="0">
						<tr>
							<th width="30%">Название категории:</th>
							<td width="70%"><?php echo $HtmlTags->InputText("Title",($IsCategoryEdit)?stripslashes($CategoryInfo->Title):"","width:100%")?></td>
						</tr>
						<tr>
							<th>Тип категории:</th>
							<td>
								<select name="Type">
<?php
	$AllTypes=array("ADMIN","CLIENT");
	foreach($AllTypes as $Type){
		echo $HtmlTags->option($Type,$Type,($IsCategoryEdit)?($CategoryInfo->Type==$Type):false);
	}
?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="tp" colspan="2">
								<?php echo $HtmlTags->InputSubmit(($IsCategoryEdit)?"Обновить":"Добавить")?>
								<?php echo $HtmlTags->InputSubmit("Сбросить","","","",true,"reset")?>
							</td>
						</tr>
					</table>
					</form>
				</fieldset>
			</td>
		</tr>
	</table>
</fieldset>
<?php
	}
	include("include/footer.inc");
?>