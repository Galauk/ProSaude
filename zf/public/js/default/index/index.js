$(function(){
    google.charts.load('current', {packages: ['corechart', 'line']});
    google.charts.setOnLoadCallback(procedimentosChart);
    google.charts.setOnLoadCallback(especialidadesChart);
});

function procedimentosChart() {
    var data = new google.visualization.DataTable();

    data.addColumn('date', 'Month');
    
    for(var i in dadosProcedimentos.linhas){
        data.addColumn('number', dadosProcedimentos.linhas[i]);
    }

    var meses = [];
    var dados = [];
    
    for(var i in dadosProcedimentos.dados){
        var mes = new Date(i.substr(0, 4), new Number(i.substr(4, 2))-1);
        meses.push(mes);
        var linha = [mes];
        // for(var letra in dadosProcedimentos.dados[i]){
        //     linha.push(dadosProcedimentos.dados[i][letra]);
        // }
        for(var letra in dadosProcedimentos.linhas){
            var valor = typeof dadosProcedimentos.dados[i][letra] != 'undefined' ? dadosProcedimentos.dados[i][letra] : 0;
            linha.push(valor);
        }

        dados.push(linha);
    }

    data.addRows(dados);

    var options = {
        title: 'Procedimentos',
        hAxis: {
            ticks: meses
        },
        chartArea:{top:40, width:'80%',height:'70%'},
        legend:'bottom',
    };

    var chart = new google.visualization.LineChart(document.getElementById('graficos-procedimentos'));
    chart.draw(data, options);
}

function especialidadesChart() {
    var dados = [['Especialidade', 'Total']];
    for(var i in dadosEspecialidades){
        dados.push([dadosEspecialidades[i].esp_nome, dadosEspecialidades[i].total]);
    }

    var data = google.visualization.arrayToDataTable(dados);

    var options = {
        title: 'Especialidades',
        chartArea:{width:'90%',height:'70%'},
        legend:{position:'top', maxLines:15},
    };

    var chart = new google.visualization.PieChart(document.getElementById('graficos-especialidades'));
    chart.draw(data, options);
}