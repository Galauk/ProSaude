<%=com.banksystem.util.TabCreator.tabHead("Dados do Acidente (2)",3,0)%>


	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2">
			Natureza da Les&atilde;o:
			&nbsp;&nbsp;  	
			<%=beanVigilanciaAcidenteTrabalho.getComboNaturezaLesao()%>
		</td>
	</tr>	
	
			
	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2">
			Parte do corpo atingidas: 		
			<br>
			<%=beanVigilanciaAcidenteTrabalho.getCheckBoxParteAtingida()%>
		</td>
	</tr>	
	
	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2">
			Havia equipamento de prote&ccedil;&atilde;o no local:
			&nbsp;&nbsp;  	
			<%=beanVigilanciaAcidenteTrabalho.getComboHaviaEquip()%>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			
			Usava equipamento de prote&ccedil;&atilde;o: 
			&nbsp;&nbsp;  	
			<%=beanVigilanciaAcidenteTrabalho.getComboUsavaEquip()%>
			&nbsp;&nbsp;&nbsp;&nbsp;  	  	
			Quais?
		</td>
	</tr>		


	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2">
			<%=beanVigilanciaAcidenteTrabalho.getCheckBoxEquipamento()%>
		</td>
	</tr>		
	
	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2">
		Foi atendido no local:
		&nbsp;&nbsp;
		<%=beanVigilanciaAcidenteTrabalho.getComboFoiAtendido()%>
	</tr>		


	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2"> 
			Conduta: 
			<br>
			<%=beanVigilanciaAcidenteTrabalho.getCheckBoxConduta()%>
		</td>
		
	</tr>		

	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2">
			Houve necessidade de remo&ccedil;&atilde;o do local:
			&nbsp;&nbsp;
			<%=beanVigilanciaAcidenteTrabalho.getComboFoiRemovido()%>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Ve&iacute;culo:
			&nbsp;&nbsp;
			<%=beanVigilanciaAcidenteTrabalho.getComboVeiculo()%>
		</td>
	</tr>		

	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2">
			Houve abono de dias de trabalho: 
			&nbsp;&nbsp;
			<%=beanVigilanciaAcidenteTrabalho.getComboAbono()%>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Atendimento feito por:
			&nbsp;&nbsp;
			<%=beanVigilanciaAcidenteTrabalho.getComboAtendidoPor()%>
		</td>
	</tr>		

	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2">
			Nome:
			&nbsp;&nbsp;
			<input name="nmAtendente1" type="text" value="" size="50" maxlength="70" class="txNormal" onKeyPress="return validaCampo(event, this, 70, jsValidCharsObsText);">	
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Nome:
			&nbsp;&nbsp;
			<input name="nmAtendente2" type="text" value="" size="50" maxlength="70" class="txNormal" onKeyPress="return validaCampo(event, this, 70, jsValidCharsObsText);">	
		</td>
  	</tr>	
  	

	<tr> 
		<td width="2%">&nbsp;</td>
		<td colspan="2"> 
			Notificante:
			&nbsp;&nbsp;
			<%=beanVigilanciaAcidenteTrabalho.getComboNotificante()%>
		</td>
	</tr>		


<%=com.banksystem.util.TabCreator.tabTail("trash2",1)%>