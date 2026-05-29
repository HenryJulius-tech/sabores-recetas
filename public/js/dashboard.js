document.addEventListener('DOMContentLoaded',function(){
    var ctx=document.getElementById('financeChart');
    if(!ctx)return;
    var chart;
    function loadChart(filter){
        fetch((typeof API_FINANCE_URL!=='undefined'?API_FINANCE_URL:'api/finance-data')+'?filter='+filter)
        .then(function(r){return r.json()})
        .then(function(d){
            if(chart)chart.destroy();
            chart=new Chart(ctx,{
                type:'bar',
                data:{
                    labels:d.labels,
                    datasets:[{
                        label:'Ingresos',
                        data:d.ingresos,
                        backgroundColor:'rgba(25,135,84,0.7)',
                        borderColor:'#198754',
                        borderWidth:1
                    },{
                        label:'Gastos',
                        data:d.gastos,
                        backgroundColor:'rgba(220,53,69,0.7)',
                        borderColor:'#dc3545',
                        borderWidth:1
                    }]
                },
                options:{
                    responsive:true,
                    plugins:{
                        legend:{position:'top'},
                        tooltip:{
                            callbacks:{
                                label:function(ctx){return ctx.dataset.label+': $'+parseInt(ctx.raw).toLocaleString('es-CO')}
                            }
                        }
                    },
                    scales:{
                        y:{
                            beginAtZero:true,
                            ticks:{callback:function(v){return'$'+v.toLocaleString('es-CO')}}
                        }
                    }
                }
            });
        });
    }
    loadChart('diario');
    document.querySelectorAll('.chart-filter').forEach(function(btn){
        btn.addEventListener('click',function(){
            document.querySelectorAll('.chart-filter').forEach(function(b){b.classList.remove('active')});
            this.classList.add('active');
            loadChart(this.dataset.filter);
        });
    });
});
