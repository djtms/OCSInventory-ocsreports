<?php
/*
 * Add value in config table
 * 
 */
require_once('require/function_config_generale.php');
$form_name='admin_values_config'.$protectedGet['tag'];
$table_name=$form_name;
$data_on[1]=$l->g(1059);
$data_on[2]=$l->g(1060);


//if no tab selected
if (!isset($protectedPost['onglet']) or $protectedPost['onglet']=='')
	 $protectedPost['onglet'] = 1;



//faire la vérif sur le tag en get
//for update name
if (isset($protectedPost['MODIF']) 
	and is_numeric($protectedPost['MODIF']) 
	and !isset($protectedPost['Valid_modif_x'])){
	 $protectedPost['onglet'] = 2;
	 $val_info=look_config_default_values(array($protectedGet['tag']."_".$protectedPost['MODIF']));
	 $protectedPost['newfield']=$val_info['tvalue'][$protectedGet['tag']."_".$protectedPost['MODIF']];
	 $hidden=$protectedPost['MODIF'];
}


echo "<br><form name='".$form_name."' id='".$form_name."' method='POST'>";
onglet($data_on,$form_name,"onglet",2);
echo '<div class="mlt_bordure" >';


if ($protectedPost['onglet'] == 1){
	$tab_options['CACHE']='RESET';

	//delete few fields
	if (isset($protectedPost['del_check']) and $protectedPost['del_check'] != ''){		
		$list = $protectedPost['del_check'];
		$sql_delete="DELETE FROM config WHERE name like '%s' and ivalue in (%s)";
		$arg_delete=array($protectedGet['tag']."_%",$list);
		mysql2_query_secure($sql_delete,$_SESSION['OCS']["readServer"],$arg_delete);	
	}
	
	//delete on field
	if(isset($protectedPost['SUP_PROF'])) {
		delete($protectedGet['tag']."_".$protectedPost['SUP_PROF']);
	}	
	
	$queryDetails ="select IVALUE,TVALUE from config where name like '".$protectedGet['tag']."_%'";

	if (!isset($protectedPost['SHOW']))
		$protectedPost['SHOW'] = 'NOSHOW';
	if (!(isset($protectedPost["pcparpage"])))
		 $protectedPost["pcparpage"]=5;

	$list_fields[$l->g(224)]='TVALUE';
	$list_fields['SUP']='IVALUE';
	$list_fields['MODIF']='IVALUE';
	$list_fields['CHECK']='IVALUE'; 
	$list_col_cant_del=$list_fields;
	$default_fields=$list_col_cant_del; 
	$are_result=tab_req($table_name,$list_fields,$default_fields,$list_col_cant_del,$queryDetails,$form_name,100,$tab_options);
	//traitement par lot
	if ($are_result){
		del_selection($form_name);
	}
	
	}elseif ($protectedPost['onglet'] == 2){
		
	
		if (isset($protectedPost['MODIF_OLD']) 
			and is_numeric($protectedPost['MODIF_OLD']) 
			and $protectedPost['Valid_modif_x'] != ""){
			//UPDATE VALUE
			echo update_config($protectedGet['tag']."_".$protectedPost['MODIF_OLD'],'TVALUE',$protectedPost['newfield']);
			
			$hidden=$protectedPost['MODIF_OLD'];		
			
		}elseif( $protectedPost['Valid_modif_x'] != "" ) {
		//ADD NEW VALUE	
			//vérification que le nom du champ n'existe pas pour les nouveaux champs
				if (trim($protectedPost['newfield']) != ''){
					$sql_verif="SELECT count(*) c FROM config WHERE TVALUE = '%s' and NAME like '%s'";
					//echo $sql_verif;
					$arg_verif=array($protectedPost['newfield'],$protectedGet['tag']."_%");
					$res_verif = mysql2_query_secure( $sql_verif, $_SESSION['OCS']["readServer"],$arg_verif);
					//echo $val_verif = mysql_fetch_array( $res_verif );
					$val_verif = mysql_fetch_array( $res_verif );
					if ($val_verif['c'] > 0)
					$ERROR=$l->g(656);
				}else
					$ERROR=$l->g(1068);
			
			
			if (!isset($ERROR)){
				$sql_new_value="SELECT max(ivalue) max FROM config WHERE  NAME like '%s'";
				$arg_new_value=array($protectedGet['tag']."_%");
				$res_new_value = mysql2_query_secure( $sql_new_value, $_SESSION['OCS']["readServer"],$arg_new_value);
				$val_new_value = mysql_fetch_array( $res_new_value );	
				if ($val_new_value['max'] == "")
				$val_new_value['max']=0;
				$val_new_value['max']++;
				$sql_insert="INSERT INTO config (NAME,TVALUE,IVALUE) 
										VALUES('%s','%s','%s')";
				$arg_insert=array($protectedGet['tag']."_".$val_new_value['max'],$protectedPost['newfield'],$val_new_value['max']);
				mysql2_query_secure($sql_insert,$_SESSION['OCS']["readServer"],$arg_insert);	
				//si on ajoute un champ, il faut créer la colonne dans la table downloadwk_pack
				echo "<font color=green><b>".$l->g(1069)."</b></font>";
			}else
				echo "<font color=red><b>".$ERROR."</b></font>";		
		}
		
	
	
		if ( isset($hidden) and is_numeric($hidden)){
			$tab_hidden['MODIF_OLD']=$hidden;		
		}
		//NAME FIELD
		$name_field=array("newfield");
		$tab_name[0]=$l->g(80);
		$type_field= array(0);
		$value_field=array($protectedPost['newfield']);
		$tab_typ_champ=show_field($name_field,$type_field,$value_field);
		$tab_typ_champ[0]['CONFIG']['SIZE']=20;
		tab_modif_values($tab_name,$tab_typ_champ,$tab_hidden,$title="",$comment="",$name_button="modif",$showbutton=true,$form_name='NO_FORM');
}


echo "</div>"; 
echo "</form>";

?>

