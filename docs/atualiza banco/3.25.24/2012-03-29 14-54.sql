UPDATE pre_consulta AS p SET esp_codigo = (
  SELECT MIN(esp_codigo)
    FROM medico_especialidade
   WHERE med_codigo=p.usr_codigo
)