<?php
//------------------------------------------------------------------>
//-> Verificacao de autenticacao
//------------------------------------------------------------------>
session_start();
$id_login = base64_decode($_SESSION["b80bb7740288fda1f201890375a60c8f"]);
function verauth($id_login) {
	if(empty($id_login)) {
	 	header("location: ".$_SESSION['linkroot'].$_SESSION['modulo']."auth.php");
	}
	include_once $_SESSION['root'].$_SESSION['comum']."library/php/db.inc.php";

	$sql = "SELECT *
			  FROM logon
			 WHERE id_login = '$id_login'
			   AND dt_atualizacao > NOW()";
	$vf = pg_query($sql);
	if((pg_num_rows($vf) == "0")){
		header("location: ".$_SESSION['linkroot'].$_SESSION['modulo']."auth.php?erno=4");
	} else {
		$updt = "UPDATE logon
					SET dt_atualizacao = NOW()+interval '360 minute'
				  WHERE id_login = '$id_login'";
		$up = pg_query($updt);
	}

}

function ChmodBtn($id_login,$acao,$href)
{

   /**  APENAS PARA PASTA LEF -> tirar o primeiro str_replace depois de colocado na raiz */
   //$GetNameFile=str_replace("lef","",str_replace("/","",$_SERVER["SCRIPT_NAME"]));
   $GetNameFile=str_replace("WebSocialSaude","",$_SERVER["SCRIPT_NAME"]);
   $GetNameFile = str_replace("//","",$GetNameFile);

   $SepNameFile = explode(".",$GetNameFile);
   $SepHref = explode(".",$href);
   $arq_name = explode(".",$href);
   $arquivo = $arq_name[0].".php";

   //---- OLHA ISTO AQUI -  FOI EU QUEM FEZ --------------
   if($arquivo=="apresenta_produto.php")
   {
	  $GetNameFile="apresenta_produto.php";
	  $SepNameFile[0]="apresenta_produto";
   }

   if($arquivo=="aih_edit.php")
   {
	  $GetNameFile="aih_edit.php";
      $SepNameFile[0]="aih_edit";
   }

   if($arquivo=="aih_del.php")
   {
	  $GetNameFile="aih_del.php";
	  $SepNameFile[0]="aih_del";
   }

   if($arquivo=="apac_edit.php")
   {
	  $GetNameFile="apac_edit.php";
	  $SepNameFile[0]="apac_edit";
   }

   if($arquivo=="apac_del.php")
   {
	  $GetNameFile="apac_del.php";
	  $SepNameFile[0]="apac_del";
   }

//-----------------------------------------------------
   $sql = "select p.perm_descricao, p.perm_programa, up.nivel_i, up.nivel_a,
		  up.nivel_d, up.nivel_l, up.nivel_b, up.perm_set
		  from usuarios_permissoes as up
		  left join permissoes as p on up.perm_codigo = p.perm_codigo
		  where up.usr_codigo = '$id_login' and p.perm_programa = '$arquivo'";
   $sql =  pg_query($sql);
   $perm = pg_fetch_array($sql);
   if(($acao=="adicionar" && $SepHref[0]==$SepNameFile[0]))
   {
	  if($perm[nivel_i]=="S")
	  {
		 $Btn = "<a href=$href&id_login=$id_login><img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_on.jpg alt='adicionar' border=0></a>";
	  } else {
		 $Btn = "<img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_off.jpg border=0>";
	  }
   }
   if(($acao=="editar" && $SepHref[0]==$SepNameFile[0]))
   {
	  if($perm[nivel_a]=="S")
	  {
		 $Btn = "<a href=$href&id_login=$id_login><img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_on.jpg border=0></a>";
	  } else {
		 $Btn = "<img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_off.jpg border=0>";
	  }
   }
   if(($acao=="apagar" && $SepHref[0]==$SepNameFile[0]))
   {
	  if($perm[nivel_d]=="S")
	  {
		 $Btn = "<a href=\"$href&id_login=$id_login\" onClick=\"if (!confirm('Realmente deseja apagar este registro?')) return false\"><img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_on.jpg border=0></a>";
	  } else {
		 $Btn = "<img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_off.jpg border=0>";
	  }
   }
   if(($acao=="delpront" && $SepHref[0]==$SepNameFile[0]))
   {
	  if($perm[nivel_d]=="S")
	  {
		 $Btn = "<a href=\"$href&id_login=$id_login\" onClick=\"if (!confirm('Realmente deseja apagar esta consulta?')) return false\"><img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_on.jpg border=0></a>";
	  } else {
		 $Btn = "<img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_off.jpg border=0>";
	  }
   }
   if(($acao=="procurar" && $SepHref[0]==$SepNameFile[0]))
   {
	  if($perm[nivel_b]=="S")
	  {
		 $Btn = "<input type=image src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/procurar_on.jpg>";
	  } else {
		 $Btn = "<img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_off.jpg border=0>";
	  }
   }
   if(($acao!="procurar" && $acao!="adicionar" && $acao!="editar" && $acao!="apagar" && $acao!="procurar_if" && $acao!="adicionar_if" && $acao!="editar_if" && $acao!="apagar_if" && $acao!="lista_if" && $SepHref[0]!=$SepNameFile[0]))
   {
	  if($perm[perm_set]=="S")
	  {
		 $Btn = "<a href=$href&id_login=$id_login><img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_on.jpg border=0></a>";
	  } else {
		 $Btn = "<img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/".$acao."_off.jpg border=0>";
	  }
   }

   /*
    Adicionado por renato para trazer true ou false só
   */
   if($acao=="adicionar_if")
   {
	  if($perm[nivel_i]=="S")
	  {
		 $Btn = true;
	  } else {
		 $Btn = false;
	  }
   }
   if($acao=="editar_if")
   {
	  if($perm[nivel_a]=="S")
	  {
		 $Btn = true;
	  } else {
		 $Btn = false;
	  }
   }
   if($acao=="apagar_if")
   {
	  if($perm[nivel_d]=="S")
	  {
		 $Btn = true;
	  } else {
		 $Btn = false;
	  }
   }
   if($acao=="procurar_if")
   {
	  if($perm[nivel_b]=="S")
	  {
		 $Btn = true;
	  } else {
		 $Btn = false;
	  }
   }
   if($acao=="listar_if")
   {
	  if($perm[nivel_l]=="S")
	  {
		 $Btn = true;
	  } else {
		 $Btn = false;
	  }
   }
   /*
   -------
   */

   return $Btn;
}

function TreeMenu($id_login) {

echo "<link href='style/style_menu.css' rel='stylesheet' type='text/css'>";
echo "<script type='text/javascript' src='js/funcao_menu.js'></script>";
echo "<table bgcolor='#dcedcd' width='100%' style='margin:0; border:0;'>";
echo "<tr>";
echo "<td>";
echo "<ul id='nav'>";

echo "	<li><a href=#>Cadastros Gerais</a>";
echo "		<ul>";

		// MENU PACIENTE
	   if(SelPerm($id_login,'paciente.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=paciente.php&id_login=$id_login ";
			echo "style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Pacientes</a>";
			//echo "</div>";
		}else{
			echo "<li><a href=# style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Pacientes</a></div>";
		}

echo "				<ul>";
echo "					<li>";

				// SUBMENU PACIENTE
			   	//if(SelPerm($id_login,'paciente.php') != "0") {
			   //echo (ChmodBtn($id_login, "adicionar_if", "paciente.php") ? "teste" : "errado");
			   if(ChmodBtn($id_login, 'adicionar_if', 'paciente.php'))
			   {
				  echo "<a href=paciente.php?acao=form_add&id_login=$id_login target=frameprincipal>";
				  echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a>";
			   } else {
				  echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add_off.png' border='0' /> Adicionar</a>";
			   }


			   	//if(SelPerm($id_login,'paciente.php') != "0") {
			   if(ChmodBtn($id_login, 'listar_if', 'paciente.php'))
			   {
				  echo "<a href=paciente.php?id_login=$id_login target=frameprincipal>";
				  echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a>";
			   } else {
				  echo "<a href='#'><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16_off.png' border='0' /> Listar</a>";
			   }

echo "					</li>";
echo "				</ul>";
echo "			</li>";

		// MENU MEDICO
	#	if(SelPerm($id_login,'medico.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=medico.php&id_login=$id_login ";
			echo " style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> M&eacute;dicos</a>";
			//echo "</div>";
	#	}
#else{
#			echo "<li><a href=# style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> M&eacute;dicos</a></div>";
#		}

echo "				<ul>";
echo "					<li>";

				// SUBMENU MEDICO
			   	//if(SelPerm($id_login,'medico.php') != "0") {
				if(chmodbtn($id_login, "adicionar_if", "medico.php"))
			    {
						echo "<a href=medico.php?acao=form_add&id_login=$id_login target=frameprincipal>";
						echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a>";
				} else {
						echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add_off.png' border='0' /> Adicionar</a>";
				}

			   	//if(SelPerm($id_login,'medico.php') != "0") {
				if(chmodbtn($id_login, "listar_if", "medico.php"))
			    {
						echo "<a href=medico.php?id_login=$id_login target=frameprincipal>";
						echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a>";
				} else {
						echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16_off.png' border='0' /> Listar</a>";
				}

			   	if(SelPerm($id_login,'especialidade.php') != "0")
				//if(chmodbtn($id_login, "adicionar_if", "especialidade.php"))
			    {
						echo "<a href=especialidade.php?acao=form_espec&id_login=$id_login target=frameprincipal>";
						echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add_especialidade.png' border='0' /> Especialidade</a>";
				} else {
						echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add_especialidade_off.png' border='0' /> Especialidade</a>";
				}

			   	if(SelPerm($id_login,'medico_especialidade.php') != "0")
				//if(chmodbtn($id_login, "adicionar_if", "medico_especialidade.php"))
			    {
						echo "<a href=medico_especialidade.php?acao=form_med_esp&id_login=$id_login target=frameprincipal>";
						echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/medico_especialidade.png' border='0' /> M&eacute;dico/Especialidade</a>";
				} else {
						echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/medico_especialidade_off.png' border='0' /> M&eacute;dico/Especialidade</a>";
				}

			   	if(SelPerm($id_login,'recomendacao.php') != "0")
				//if(chmodbtn($id_login, "adicionar_if", "recomendacao.php"))
			    {
						echo "<a href=recomendacao.php?acao=form_med_esp&id_login=$id_login target=frameprincipal>";
						echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Recomenda&ccedil;&atilde;o</a>";
				}else{
						echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Recomenda&ccedil;&atilde;o</a>";
				}

echo "					</li>";
echo "				</ul>";
echo "			</li>";

		// MENU COORDENADOS DE UNIDADE
		if(SelPerm($id_login,'agente.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=agente.php&id_login=$id_login ";
			echo " style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Responsável Técnico</a>";
		}else{
			echo "<li><a href=# style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Coord. de Unidade</a>";
		}

echo "				<ul>";
echo "					<li>";
				// SUBMENU AGENTES
			   	//if(SelPerm($id_login,'agente.php') != "0") {
				if(chmodbtn($id_login, "adicionar_if", "agente.php"))
				{
					echo "<a href=agente.php?acao=form_add&id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add_off.png' border='0' /> Adicionar</a>";
				}

			   	//if(SelPerm($id_login,'agente.php') != "0") {
				if(chmodbtn($id_login, "listar_if", "agente.php"))
				{
					echo "<a href=agente.php?id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16_off.png' border='0' /> Listar</a>";
				}


echo "					</li>";
echo "				</ul>";
echo "			</li>";

		// MENU UNIDADES
		if(SelPerm($id_login,'unidade.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=unidade.php&id_login=$id_login ";
			echo "style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Unidades</a>";
			//echo "</div>";
		}else{
			echo "<li><a href=# style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Unidades</a></div>";
		}

echo "				<ul>";
echo "					<li>";

					// SUBMENU UNIDADES
			   	//if(SelPerm($id_login,'unidade.php') != "0") {
				if(chmodbtn($id_login, "adicionar_if", "unidade.php"))
				{
					echo "<a href=unidade.php?acao=form_add&id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add_off.png' border='0' /> Adicionar</a>";
				}

			   	//if(SelPerm($id_login,'unidade.php') != "0") {
				if(chmodbtn($id_login, "listar_if", "unidade.php"))
				{
					echo "<a href=unidade.php?id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16_off.png' border='0' /> Listar</a>";
				}

echo "					</li>";
echo "				</ul>";
echo "			</li>";

		// MENU FERIADO
		if(SelPerm($id_login,'feriado.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=feriado.php&id_login=$id_login ";
			echo "style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Feriados</a>";
			//echo "</div>";
		}else{
			echo "<li><a href=# style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Feriados</a></div>";
		}
echo "				<ul>";
echo "					<li>";
				// SUBMENU FERIADO
			   	//if(SelPerm($id_login,'feriado.php') != "0") {
				if(chmodbtn($id_login, "adicionar_if", "feriado.php"))
				{
					echo "<a href=feriado.php?acao=form_add&id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add_off.png' border='0' /> Adicionar</a>";
				}

			   	//if(SelPerm($id_login,'feriado.php') != "0") {
				if(chmodbtn($id_login, "listar_if", "feriado.php"))
				{
					echo "<a href=feriado.php?&id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16_off.png' border='0' /> Listar</a>";
				}

echo "					</li>";
echo "				</ul>";
echo "			</li>";

		// MENU FAMILIA
		if(SelPerm($id_login,'familia.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=familia.php&id_login=$id_login ";
			echo "style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Fam&iacute;lia</a>";
			//echo "</div>";
		}else{
			echo "<li><a href=# style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Fam&iacute;lia</a></div>";
		}

echo "				<ul>";
echo "					<li>";

				// SUBMENU FAMILIA
			   	//if(SelPerm($id_login,'familia.php') != "0") {
				if(chmodbtn($id_login, "adicionar_if", "familia.php"))
				{
					echo "<a href=familia.php?acao=form_add&id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add_off.png' border='0' /> Adicionar</a>";
				}

			   	//if(SelPerm($id_login,'familia.php') != "0") {
				if(chmodbtn($id_login, "listar_if", "familia.php"))
				{
					echo "<a href=familia.php?id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16_off.png' border='0' /> Listar</a>";
				}

			   	if(SelPerm($id_login,'area.php') != "0") {
					echo "<a href=area.php?acao=&id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/area16.png' border='0' /> &Aacute;rea</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/area16_off.png' border='0' /> &Aacute;rea</a>";
				}

			   	if(SelPerm($id_login,'microarea.php') != "0") {
					echo "<a href=microarea.php?acao=&id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/microarea.png' border='0' /> Micro &Aacute;rea</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/microarea_off.png' border='0' /> Micro &Aacute;rea</a>";
				}

echo "					</li>";
echo "				</ul>";
echo "			</li>";

                // MENU EXAME
        echo "<li><a href=# style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Exame</a></div>";

echo "                          <ul>";
echo "                                  <li>";

                                // SUBMENU EXAME
                                        echo "<a href=exa_tipodemetodo.php?id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Tipo de Metodo</a>";

                                        echo "<a href=exa_categoriadeexames.php?id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Categoria de Exames</a>";

                                        echo "<a href=exa_tipodematerial.php?acao=&id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Tipo de Material</a>";

                                        echo "<a href=exa_tipodeexame.php?acao=&id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Tipo de Exame</a>";

                                        echo "<a href=exa_material.php?acao=&id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Material</a>";

                                        echo "<a href=exa_metododeanalise.php?acao=&id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Metodo de Analise</a>";

                                        echo "<a href=exa_subexames.php?acao=&id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Sub-Exame</a>";

                                        echo "<a href=exa_itensdeanalise.php?acao=&id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Itens de Analise</a>";

                                        echo "<a href=exa_valoresreferencia.php?acao=&id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Valores Referenciais</a>";

                                        echo "<a href=exa_categorialaudo.php?acao=&id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Categoria de Laudos</a>";

                                        echo "<a href=exa_tipodelaudos.php?acao=&id_login=$id_login target=frameprincipal>";
                                        echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Tipo de Laudos</a>";

echo "                                  </li>";
echo "                          </ul>";
echo "                  </li>";
		// MENU PROCEDIMENTO
		#if(SelPerm($id_login,'procedimento.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=procedimento.php&id_login=$id_login > Procedimento</a>";
		#}else{
		#	echo "<li><a href=#> Procedimento</a></div>";
		#}
		// MENU LABORATÓRIO
		if(SelPerm($id_login,'laboratorio.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=laboratorio.php&id_login=$id_login> Prestador de Servi&ccedil;o</a>";
		}else{
			echo "<li><a href=#> Prestador de Servi&ccedil;o</a>";
		}
		// MENU HOSPITAL
	/*	if(SelPerm($id_login,'hospital.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=hospital.php&id_login=$id_login> Hospital</a>";
		}else{
			echo "<li><a href=#> Hospital</a>";
		}*/
		// MENU C.I.
		if(SelPerm($id_login,'ci.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=ci.php&id_login=$id_login> Caráter de Interna&ccedil;&atilde;o ( C.I. )</a>";
		}else{
			echo "<li><a href=#> Caráter de Interna&ccedil;&atilde;o ( C.I. )</a>";
		}
		// MENU CL&iacute;NICA
		if(SelPerm($id_login,'clinica.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=clinica.php&id_login=$id_login> Cl&iacute;nica</a>";
		}else{
			echo "<li><a href=#> Cl&iacute;nica</a>";
		}
/*

echo "				<ul>";
echo "					<li>";

				// SUBMENU EXAMES (PROCEDIMENTO)
			   	if(SelPerm($id_login,'procedimento.php') != "0") {
					echo "<a href=procedimento.php?acao=form_add&id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Procedimento</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Procedimento</a>";
				}

				// SUBMENU EXAMES (LABORATÓRIO)
			   	if(SelPerm($id_login,'laboratorio.php') != "0") {
					echo "<a href=laboratorio.php?acao=form_add&id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Laboratorio</a>";
				}else{
					echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Laboratorio</a>";
				}

echo "					</li>";
echo "				</ul>";

*/
         // MENU CL&iacute;NICA
		if(SelPerm($id_login,'cancelar_agendamento.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=cancelar_agendamento.php&id_login=$id_login> Cancelamento de Consultas</a>";
		}else{
			echo "<li><a href=#> Cancelamento de Consultas</a>";
		}

echo "			</li>";


echo "		</ul>";
echo "	</li>";










echo "	<li><a href=#>Atendimentos</a>";
echo "		<ul>";

		// MENU ATENDIMENTOS
		// ITEM ATEND. MÉDICO
#		if(SelPerm($id_login,'atendimento_medico.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=atendimento_medico.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> M&eacute;dico</a></li>";
	#	}else{
	#		echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> M&eacute;dico</a></li>";
	#	}

		// ITEM ATEND. PAM ( PRONTO ATENDIMENTO )
		if(SelPerm($id_login,'ambulatorio.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=ambulatorio.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/pam_on.gif' border='0' /> Pronto Atendimento</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/pam_off.gif' border='0' /> Pronto Atendimento</a></li>";
		}

		// ITEM ATEND. ( ATENDIMENTO PSF )
		if(SelPerm($id_login,'atendimento_psf.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=atendimento_psf.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/house.png'/> Atendimento PSF</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/house_off.png'/> Atendimento PSF</a></li>";
		}

		// ITEM ATEND. ( ODONTOLOGIA )
		//if(SelPerm($id_login,'odonto_atendimento.php') != "0") {
	#	if(SelPerm($id_login,'odonto_recepcionado.php') != "0") {
			//echo "<li><a href=$PHP_SELF?link=odonto_atendimento.php&id_login=$id_login>";
			echo "<li><a href=$PHP_SELF?link=odonto_recepcionado.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' /> Odontologia</a></li>";
	#	}else{
	#		echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' /> Odontologia</a></li>";
	#	}

		// ITEM ATEND. ( ATENDIMENTOS ESPECIAIS ) criar a pag. atendimentos_espec.php $PHP_SELF?link=atendimentos_espec.php&id_login=$id_login
		/*if(SelPerm($id_login,'atendimentos_espec.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=atendimentos_espec.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' /> Atendimentos Especiais</a></li>";
		}else{*/
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' /> Atendimentos Especiais</a></li>";
		//}

		// ITEM ATEND. ( ATENDIMENTOS ESPECIAIS ) criar a pag. atendimentos_espec.php $PHP_SELF?link=atendimentos_espec.php&id_login=$id_login
		if(SelPerm($id_login,'atend_balcao.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=atend_balcao.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' /> Atendimento de Balc&atilde;o</a></li>";

			echo "<li><a href=$PHP_SELF?link=agendamento_atd.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' /> Listar Recepcionados</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' /> Atendimento de Balc&atilde;o</a></li>";
		}


echo "		</ul>";
echo "	</li>";


echo "	<li><a href=#>Agendamento</a>";
echo "		<ul>";

				// SUBMENU AGENDAMENTO (RECEP&ccedil;&atilde;O)
			   	if(SelPerm($id_login,'agendamento.php') != "0") {
					echo "<li><a href=agendamento.php?id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Recep&ccedil;&atilde;o</a></li>";
				}else{
					echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Recep&ccedil;&atilde;o</a></li>";
				}

				// SUBMENU AGENDAMENTO (FAZER AGENDAMENTO)
			   	if(SelPerm($id_login,'fazer_agendamento.php') != "0") {
					echo "<li><a href=fazer_agendamento.php?id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Fazer Agendamento</a></li>";
				}else{
					echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Fazer Agendamento</a></li>";
				}

				// SUBMENU AGENDAMENTO (MANUTEN&ccedil;&atilde;O DE AGENDAS POR DATA)
			   	if(SelPerm($id_login,'manutencaomedicos_data.php') != "0") {
					echo "<li><a href=manutencaomedicos_data.php?id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Manut. Agendas Por Data</a></li>";
				}else{
					echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Manut. Agendas</a></li>";
				}

				// SUBMENU AGENDAMENTO (MANUTEN&ccedil;&atilde;O DE AGENDAS POR PERIODO)
				if(SelPerm($id_login,'manutencaomedicos.php') != "0") {
					echo "<li><a href=manutencaomedicos.php?id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Manut. Agendas Por Periodo</a></li>";
				}else{
					echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Manut. Agendas</a></li>";
				}

				// SUBMENU AGENDAMENTO (MANUTEN&ccedil;&atilde;O POR GRUPO DE AGENTES)
			   	if(SelPerm($id_login,'manutencaoagentes.php') != "0") {
					echo "<li><a href=manutencaoagentes.php?id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Manut. de Grupos de Agenda</a></li>";
				}else{
					echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Manut. de Grupos de Agenda</a></li>";
				}

			   	if(SelPerm($id_login,'recepcao_pacientes.php') != "0") {
					echo "<li><a href='recepcao_pacientes.php' target=_blank>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Recep. C&oacute;d. Barra</a></li>";
				}else{
					echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' />  Recep. C&oacute;d. Barra</a></li>";
				}

               //SUBMENU AGENDAMENTO (CADASTRO LISTA DE ESPERA)
                if(SelPerm($id_login,'cad_lista_espera.php') != "0") {
					echo "<li><a href=cad_lista_espera.php?id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Cad. Lista de Espera </a></li>";
				}else{
					echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Cad. Lista de Espera </a></li>";
				}

                //SUBMENU AGENDAMENTO (CADASTRO AGENDAMENTO DA LISTA DE ESPERA)
                if(SelPerm($id_login,'lista_espera_agendamento.php') != "0") {
					echo "<li><a href=lista_espera_agendamento.php?id_login=$id_login target=frameprincipal>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Age. Lista de Espera </a></li>";
				}else{
					echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Age. Lista de Espera </a></li>";
				}

echo "		</ul>";
echo "	</li>";

		// MENU AIH
	   	if(SelPerm($id_login,'aih.php') != "0") {
			echo "	<li><a href=$PHP_SELF?link=aih.php&id_login=$id_login>AIH</a></li>";
		}else{
			echo "	<li><a href=#>AIH</a></li>";
		}

	   	if(SelPerm($id_login,'apac.php') != "0") {
			echo "	<li><a href=$PHP_SELF?link=apac.php&id_login=$id_login>APAC</a></li>";
		}else{
			echo " <li><a href=#>APAC</a></li>";
		}


echo "	<li><a href=#>Exames</a>";
echo "		<ul>";
				echo "<li><a href='$PHP_SELF?link=liberacao/liberacao.php&id_login=$id_login'>Libera&atilde;o</a></li>";
		// ITEM ADICIONAR EXAME
		if(SelPerm($id_login,'agendar_exame.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=fazer_agendamento.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Agendamento</a></li>";

		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add_off.png' border='0' /> Adicionar</a></li>";
		}


echo "		</ul>";
echo "	</li>";


echo "	<li><a href=#>Materiais</a>";
echo "		<ul>";

		/*
		if(SelPerm($id_login,'.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a></li>";
		}

		if(SelPerm($id_login,'.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a></li>";
		}*/

		// ITEM GRUPO
		if(SelPerm($id_login,'grupo.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=grupo.php&acao=form_grupo&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/grupo.png' border='0' /> Grupo</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/grupo_off.png' border='0' /> Grupo</a></li>";
		}

		// ITEM SETOR
		if(SelPerm($id_login,'setor.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=setor.php&acao=form_setor&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/setor.png' border='0' /> Setor</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/setor_off.png' border='0' /> Setor</a></li>";
		}

		// ITEM PSICOTRÓPICOS
		if(SelPerm($id_login,'psico.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=psico.php&acao=form_psico&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/psicotropicos.png' border='0' /> Psicotrópicos</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/psicotropicos_off.png' border='0' /> Psicotrópicos</a></li>";
		}

		// ITEM FORNECEDOR
		if(SelPerm($id_login,'fornecedor.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=fornecedor.php&acao=form_forn&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/fornecedor.png' border='0' /> Fornecedor</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/fornecedor_off.png' border='0' /> Fornecedor</a></li>";
		}

		// ITEM MOVIMENTACAO
		if(SelPerm($id_login,'movimentacao.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=movimentacao.php&acao=form_entrada&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/movimentacao.png' border='0' /> Movimenta&ccedil;&atilde;o</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/movimentacao_off.png' border='0' /> Movimenta&ccedil;&atilde;o</a></li>";
		}

		/*if(SelPerm($id_login,'fechamento.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=fechamento.php&acao=form_fechamento&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/feriados.png' border='0' /> Fechamento Mensal</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/feriados_off.png' border='0' /> Fechamento Mensal</a></li>";
		}*/


echo "		</ul>";
echo "	</li>";
/*ITEM INCLUIDO 18/04, A PEDIDO DILIEE, AUTOR: CLAUDIA*/
echo "	<li><a href=#>Farm&aacute;cia</a>";
echo "		<ul>";

		/*
		if(SelPerm($id_login,'.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a></li>";
		}

		if(SelPerm($id_login,'.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a></li>";
		}*/

		// ITEM PSICOTROPICOS
		if(SelPerm($id_login,'psico.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=psico.php&acao=form_psico&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/psicotropicos.png' border='0' /> Psicotr&oacute;picos</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/psicotropicos_off.png' border='0' /> Psicotr&atilde;³picos</a></li>";
		}

		// ITEM DISPENSA&atilde;‡&atilde;ƒO
		if(SelPerm($id_login,'dispensacao.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=dispensacao.php&acao=form_entrada&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/setor.png' border='0' /> Dispensa&ccedil;&atilde;o</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/setor_off.png' border='0' /> Dispensa&ccedil;&atilde;o</a></li>";
		}

		// ITEM DISPENSA MATERIAIS
		//INLCUIDO A PEDIDO DO LUCIO/RENATO, POIS N&atilde;ƒO SE SABE AINDA QUAL IR&atilde;� UTILIZAR, SE &atilde;‰ O DISPENSA&atilde;‡&atilde;ƒO OU O ARQUIVO NOVO
		if(SelPerm($id_login,'dispensa_medicamentos.php') != "0") {
			//echo "<li><a href=$PHP_SELF?link=dispensa_medicamentos.php&acao=form_entrada&id_login=$id_login>";
			echo "<li><a href=$PHP_SELF?link=dispensa_medicamentos.php&acao=listar&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/setor.png' border='0' /> Dispensa Medicamentos</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/setor_off.png' border='0' /> Dispensa&ccedil;&atilde;o</a></li>";
		}

		// ITEM COTAS PROD. POR PACIENTE
		if(SelPerm($id_login,'cota_paciente.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=cota_paciente.php&acao=&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/psicotropicos.png' border='0' /> Cotas Prod. por Paciente</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/psicotropicos_off.png' border='0' /> Cotas Prod. por Paciente</a></li>";
		}

		// ITEM PROGRAMA ATENDIMENTO
		if(SelPerm($id_login,'programa_atendimento.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=programa_atendimento.php&acao=&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/fornecedor.png' border='0' />Programa Atendimento</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/fornecedor_off.png' border='0' /> Programa Atendimento</a></li>";
		}

		// ITEM PROGRAMA PRODUTO
		if(SelPerm($id_login,'programa_produto.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=programa_produto.php&acao=&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/movimentacao.png' border='0' /> Programa Produto</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/movimentacao_off.png' border='0' /> Programa Produto</a></li>";
		}

		/*	if(SelPerm($id_login,'fechamento.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=fechamento.php&acao=form_fechamento&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/feriados.png' border='0' /> Fechamento Mensal</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/feriados_off.png' border='0' /> Fechamento Mensal</a></li>";
		}*/


echo "		</ul>";
echo "	</li>";
/*FIM DO ITEM INCLUIDO DIA 18/04*/

//fun&ccedil;&atilde;o para a restri&ccedil;&atilde;o de acesso aos relatórios
function verif_perm_rel($id_login,$pagina)
{
   $sql = "SELECT up.perm_set FROM usuarios_permissoes AS up
            LEFT JOIN permissoes AS p ON up.perm_codigo=p.perm_codigo
            WHERE up.usr_codigo = '$id_login' AND p.perm_programa = '$pagina'";
   $query = db_query($sql);
   $row = pg_fetch_array($query);
   return($row[perm_set]);
}

echo "	<li><a href=#>Relatórios</a>";
echo "		<ul>";

	       // MENU RELATÓRIOS
	       // ITEM REL. AGENDAMENTO
               if ( verif_perm_rel($id_login,'rel_index.php?opcao=1') == 'S' &&
                   SelPerm($id_login,'rel_index.php') != "0" )
               {
                  echo "<li><a href=rel_index.php?opcao=1 target=frameprincipal>";
		  echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/manutencao_agenda.png' border='0' /> Agendamento</a></li>";
               }
               else
               {
                  echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/manutencao_agenda_off.png' border='0' /> Agendamento</a></li>";
               }
                /*era assim:
		if(SelPerm($id_login,'rel_index.php') != "0") {
			echo "<li><a href=rel_index.php?id_login=$id_login&opcao=1 target=frameprincipal>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/manutencao_agenda.png' border='0' /> Agendamento</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/manutencao_agenda_off.png' border='0' /> Agendamento</a></li>";
		}
               */

	       // ITEM REL. ATENDIMENTO
               if ( verif_perm_rel($id_login,'rel_index.php?opcao=2') == 'S' &&
                   SelPerm($id_login,'rel_index.php') != "0" )
               {
                  echo "<li><a href=rel_index.php?opcao=2 target=frameprincipal>";
		  echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/house.png' border='0' /> Atendimento</a></li>";
               }
               else
               {
                  echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/house_off.png' border='0' /> Atendimento</a></li>";
               }

	       // ITEM REL. METERIAIS
               if ( verif_perm_rel($id_login,'rel_index.php?opcao=7') == 'S' &&
                   SelPerm($id_login,'rel_index.php') != "0" )
               {
                  echo "<li><a href=rel_index.php?opcao=7 target=frameprincipal>";
		  echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/materiais.png' border='0' /> Materiais</a></li>";
               }
               else
               {
                  echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/materiais_off.png' border='0' /> Materiais</a></li>";
               }

               // ITEM REL. FARMACIA
               if ( verif_perm_rel($id_login,'rel_index.php?opcao=8') == 'S' &&
                   SelPerm($id_login,'rel_index.php') != "0" )
               {
                  echo "<li><a href=rel_index.php?opcao=8 target=frameprincipal>";
		  echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Farm&aacute;cia</a></li>";
               }
               else
               {
                  echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Farm&aacute;cia</a></li>";
               }

	       // ITEM REL. EMERGÊNCIA 24H
               if ( verif_perm_rel($id_login,'rel_index.php') == 'S' &&
                   SelPerm($id_login,'rel_index.php') != "0" )
               {
                  echo "<li><a href=rel_index.php?opcao=99 target=frameprincipal>";
		  echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Emerg&ecirc;ncia 24h</a></li>";
               }
               else
               {
                  echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/6.gif' border='0' /> Emerg&ecirc;ncia 24h</a></li>";
               }


echo "		</ul>";
echo "	</li>";

echo "	<li><a href=#>Usuário</a>";
echo "		<ul>";

		// MENU USUÁRIO
		// ITEM ADICIONAR USUÁRIO
		if(SelPerm($id_login,'usuarios.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=usuarios.php&acao=form_add&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add.png' border='0' /> Adicionar</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/add_off.png' border='0' /> Adicionar</a></li>";
		}

		// ITEM LISTAR USUÁRIOS
		if(SelPerm($id_login,'usuarios.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=usuarios.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16.png' border='0' /> Listar</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/listar16_off.png' border='0' /> Listar</a></li>";
		}

		// ITEM ACESSO POR USUÁRIO
		if(SelPerm($id_login,'usuario_acesso.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=usuario_acesso.php&acao=form_acesso&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/grupo_agentes.png' border='0' /> Acesso por Usuário</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/grupo_agentes_off.png' border='0' /> Acesso por Usuário</a></li>";
		}

		// ITEM PERMISS&otilde;ES
		if(SelPerm($id_login,'permissoes.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=permissoes.php&acao=form_perm&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/permissoes.png' border='0' /> Permiss&otilde;es</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/permissoes_off.png' border='0' /> Permiss&otilde;es</a></li>";
		}

		// ITEM PERMISS&atilde;O POR USUÁRIO
		if(SelPerm($id_login,'permissoes_usuarios.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=permissoes_usuarios.php&acao=form_perm&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/permissao_por_usuario.png' border='0' /> Permiss&otilde;es por Usuário</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/permissao_por_usuario_off.png' border='0' /> Permiss&otilde;es por Usuário</a></li>";
		}

		// ITEM LOG
		if(SelPerm($id_login,'log.php') != "0") {
			echo "<li><a href=$PHP_SELF?link=log.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/log.png' border='0' /> Log</a></li>";
		}else{
			echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/log_off.png' border='0' /> Log</a></li>";
		}

		// ALTERAR SENHA
			echo "<li><a href=$PHP_SELF?link=alteraSenha.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/permissoes.png' border='0' /> Alterar Senha</a></li>";

echo "		</ul>";
echo "	</li>";


		// BOTAO MANUTENCAO
		echo "<li><a href='#'>Manuten&ccedil;&atilde;o</a>";
		echo "<ul>";

		// MENU IMPORTACAO
			echo "<li><a href=# style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Importa&ccedil;&atilde;o</a>";

echo "				<ul>";

				// SUBMENU IMPORTACAO
			   	if(SelPerm($id_login,'importacao_procedimento_sihsus.php') != "0") {
					echo "<li><a href='$PHP_SELF?link=importacao_procedimento_sihsus.php&id_login=$id_login'>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/importar.png' border='0' /> Procedimentos SIHSUS</a></li>";
				}else{
					echo "<li><a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/importar_off.png' border='0' /> Procedimentos SIHSUS</a></li>";
				}

echo "				</ul>";
echo "			</li>";

		// MENU EXPORTACAO
	   if(SelPerm($id_login,'paciente.php') != "0") {
			echo "<li><a href=# ";
			echo "style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Exporta&ccedil;&atilde;o</a>";
		}else{
			echo "<li><a href=# style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Importa&ccedil;&atilde;</a></div>";
		}

echo "				<ul>";
echo "					<li>";

				// SUBMENU EXPORTACAO
			   	if(SelPerm($id_login,'layout_critica.php') != "0") {
					echo"<a href='$PHP_SELF?link=layout_critica.php&id_login=$id_login'> ";
					echo"<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/exportar.png' border='0' /> Exporta&ccedil;&atilde;o Layout Cr&iacute;tica</a>";
				}else{
		echo "<a href=#><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/exportar_off.png' border='0' /> Exporta&ccedil;&atilde;o Layout Cr&iacute;tica</a>";
				}

echo "					</li>";
echo "				</ul>";
echo "			</li>";

echo "		</ul>";


#
#-------------------------------------------- GUICHE ------------------------------
#

		// BOTAO MANUTENCAO
		echo "<li><a href='#'>Senhas do Guiche</a>";
		echo "<ul>";

		// MENU IMPORTACAO
			echo "<li><a href=# style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Cadastros</a>";

echo "				<ul>";
					echo "<li><a href='$PHP_SELF?link=guiche/cadastro.php&id_login=$id_login'>";
					echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/importar.png' border='0' /> Cadastrar Guiches</a></li>";
echo "				</ul>";

echo "			</li>";

		// MENU EXPORTACAO
			echo "<li><a href=# ";
			echo "style='background-image:url(".$_SESSION['linkroot'].$_SESSION['comum']."imgs/seta.gif);background-position:right;background-repeat:no-repeat;'> Configuracoes</a>";

echo "				<ul>";
echo "					<li>";

				// SUBMENU EXPORTACAO
					echo"<a href='$PHP_SELF?link=layout_critica.php&id_login=$id_login'> ";
					echo"<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/exportar.png' border='0' /> Exporta&ccedil;&atilde;o Layout Cr&iacute;tica</a>";

echo "					</li>";
echo "				</ul>";
echo "			</li>";

echo "		</ul>";

#
#-------------------------------------------- FINAL MENU GUICHE ------------------------------
#






/*
			// BOTAO AJUDA
			echo "<li><a href=$PHP_SELF?link=ajuda.php&id_login=$id_login>";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/help16.png' />&nbsp;&nbsp;Ajuda</a></li>";

			// BOTAO AJUDA
			echo "<li><a href='#' onclick=\"javascript:window.open('http://200.101.176.52:8080/dedao/','ponto','resizable=yes,scrollbars=yes')\">";
			echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/fingerprint.png' width='14' height='14' />&nbsp;&nbsp;Ponto</a></li>";
*/

			// BOTAO SAIR
			echo "<li><a href='logoff.php?id_login=$id_login' title='SAIR' target='_parent' onClick=\"if (!confirm('Realmente deseja Sair do Sistema?')) return false\">Sai</a></li>";

echo "</ul><br style='bclear:both; margin:0;' />";
echo "</td>";
echo "</tr>";
echo "</table>";


}
// VALIDANDO MENU SUPERIOR

function verificaMenuSuperior($id_login){
	$id_login = $id_login;
	$buscaMenuSuperior = pg_query("SELECT * FROM permissao_menu_superior WHERE usr_codigo = '$id_login'");
	$recebeMenuSuperior = pg_fetch_object($buscaMenuSuperior);
	// echo "<pre>";print_r($recebeMenuSuperior);die();
	return $recebeMenuSuperior;
}

function verificaMenuInferior($id_login){
	$id_login = $id_login;
	$buscaMenuInferior = pg_query("SELECT * FROM permissao_menu_inferior WHERE usr_codigo = '$id_login'");
	$recebeMenuInferior = pg_fetch_object($buscaMenuInferior);
	// echo "<pre>";print_r($recebeMenuInferior);die();
	return $recebeMenuInferior;
}

// VALIDANDO MENU SUPERIOR
//
// Funcao Selecionando as permissoes para o menu de icones
//
function SelPerm($id_login,$arq) {
 $sql =  pg_query ("select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up left join permissoes as p on up.perm_codigo=p.perm_codigo where up.usr_codigo = '$id_login' and p.perm_programa = '$arq' and up.perm_set = 'S'");
 $perm_set = pg_num_rows($sql);
 return $perm_set;
}
// Bot&otilde;es de &iacute;cones

function BtnPrincial($id_login) {
echo "<table id=nav2 width='100%' height='25' border='0' bgcolor='#dcedcd'>";
echo "	<tr>";

	// BOTAO PACIENTES----------------------------------
   if(SelPerm($id_login,'paciente.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=paciente.php&id_login=$id_login title='PACIENTES'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/pacientes_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/pacientes_smallicon_off.gif' border='0' title='PACIENTES' /></td>";
   }

	//BOTAO FAMILIA---------------------------------->
   if(SelPerm($id_login,'familia.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=familia.php&id_login=$id_login title='FAM&iacute;LIA'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/familia_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/familia_smallicon_off.gif' border='0' title='FAM&iacute;LIA' /></td>";
   }

	//BOTAO MEDICO---------------------------------->
   if(SelPerm($id_login,'medico.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=medico.php&id_login=$id_login title='MÉDICOS'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/medico_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/medico_smallicon_off.gif' border='0' /></td>";
   }

	//AGENTE---------------------------------->
   if(SelPerm($id_login,'agente.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=agente.php&id_login=$id_login title='RESPONSÁVEL TÉCNICO'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/agentes_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/agentes_smallicon_off.gif' border='0' /></td>";
   }

	//UNIDADE---------------------------------->
   if(SelPerm($id_login,'unidade.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=unidade.php&id_login=$id_login title='UNIDADES'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/unidades_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/unidades_smallicon_off.gif' border='0' /></td>";
   }

	//AGENDAMENTO---------------------------------->
   if(SelPerm($id_login,'agendamento.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=agendamento.php&id_login=$id_login title='AGENDAMENTO'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/agendamento_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/agendamento_smallicon_off.gif' border='0' /></td>";
   }

	//EXAME---------------------------------->
#   if(SelPerm($id_login,'exame.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=exame/exa_listapedidoexame.php&id_login=$id_login title='EXAMES'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/exames_smallicon_on.gif' border='0' /></a></td>";
#   } else {
#		echo "		<td align='center' valign='middle'>";
#		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/exames_smallicon_off.gif' border='0' /></td>";
 #  }

	//MATERIAIS---------------------------------->
   if(SelPerm($id_login,'materiais.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=materiais.php&id_login=$id_login title='MATERIAS'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/materiais_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/materiais_smallicon_off.gif' border='0' /></td>";
   }

	//FARMACIA---------------------------------->
   if(SelPerm($id_login,'farmacia.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=farmacia.php&id_login=$id_login title='FARMACIA-----'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/farmacia_on.png' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/farmacia_off.png' border='0' /></td>";
   }

	//RELATÓRIOS---------------------------------->
   if(SelPerm($id_login,'rel_index.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=rel_index.php&id_login=$id_login title='RELATÓRIOS'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/relatorios_2_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/relatorios_2_smallicon_off.gif' border='0' /></td>";
   }

	//PAM---------------------------------->
   if(SelPerm($id_login,'ambulatorio.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=ambulatorio.php&id_login=$id_login title='PAM'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/pam_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/pam_smallicon_off.gif' border='0' /></td>";
   }

	//ATENDIMENTO MEDICO---------------------------------->
   if(SelPerm($id_login,'atendimento_medico.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=atendimento_medico.php&id_login=$id_login title='ATENDIMENTO MÉDICO'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/atendimentomedico_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/atendimentomedico_smallicon_off.gif' border='0' /></td>";
   }

	//USUÁRIOS---------------------------------->
   if(SelPerm($id_login,'usuarios.php') != "0") {
		echo "		<td align='center' valign='middle'>";
		echo "			<a href=$PHP_SELF?link=usuarios.php&id_login=$id_login title='USUÁRIOS'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/usuarios_smallicon_on.gif' border='0' /></a></td>";
   } else {
		echo "		<td align='center' valign='middle'>";
		echo "			<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/usuarios_smallicon_off.gif' border='0' /></td>";
   }

/*
	//AJUDA---------------------------------->
	echo "		<td align='center' valign='middle'>";
	echo "			<a href=$PHP_SELF?link=ajuda.php&id_login=$id_login title='AJUDA'><img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/help_smallicon_on.gif' border='0' /></a></td>";
*/
	//MENSAGEM---------------------------------->
	echo "		<td align='center' valign='middle'>";
	echo "			<a href='#' title='Mensagens' ".
            "onclick=\"window.open( 'mensagem.php?id_login={$id_login}', 'msg',".
            "'width=600,height=350,scrollbars=yes,resizable=yes,top=100,left=10' )\">".
            "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/comunicacao_on.png' border=0 width=32 height=32></a></td>";

echo "	</tr>";
echo "</table>";
}

?>
