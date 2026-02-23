@extends('layouts.master')
@section('title', 'Financial Summary')
@section('css')
@include('partials.pipeline-dashboard-styles')
@include('partials.custom-select-datepicker-styles')
<style>
    .form-page-hdr{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:.75rem}
    .form-page-hdr h4{font-size:1.1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:.45rem}
    .form-page-hdr h4 i{color:#d4af37;font-size:1.25rem}
    .crm-label{font-size:.72rem;font-weight:600;color:var(--bs-surface-500);margin-bottom:.25rem}
    .crm-input{border:1px solid rgba(0,0,0,.08);border-radius:22px;padding:.38rem .75rem;font-size:.75rem;width:100%;background:var(--bs-card-bg);color:var(--bs-body-color);transition:border-color .15s}
    .crm-input:focus{border-color:#d4af37;box-shadow:0 0 0 2px rgba(212,175,55,.12);outline:none}
    select.crm-input{appearance:none;-webkit-appearance:none;border-radius:22px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23b8860b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .7rem center;padding-right:1.8rem}
    .cat-row{display:flex;align-items:center;gap:.65rem;padding:.55rem 0;border-bottom:1px solid rgba(0,0,0,.04)}
    .cat-row:last-child{border:none}
    .cat-name{font-size:.75rem;font-weight:600;min-width:90px;color:var(--bs-body-color)}
    .cat-bar-wrap{flex:1;height:7px;background:rgba(212,175,55,.1);border-radius:4px;overflow:hidden}
    .cat-bar-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,#d4af37,#f0d878);transition:width .4s}
    .cat-amt{font-size:.75rem;font-weight:700;color:#d4af37;min-width:75px;text-align:right}
    .cat-pct{font-size:.62rem;color:var(--bs-surface-500);min-width:42px;text-align:right}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="form-page-hdr">
        <div>
            <h4><i class="bx bx-bar-chart-square"></i> Financial Summary</h4>
        </div>
    </div>

    {{-- Date Range Filter --}}
    <div class="pipe-filter-bar mb-2">
        <form class="d-flex flex-wrap align-items-end gap-2 w-100">
            <div style="min-width:130px">
                <label class="crm-label">From Date</label>
                <input type="text" class="crm-input crm-date" id="date_from" placeholder="Select" autocomplete="off">
            </div>
            <div style="min-width:130px">
                <label class="crm-label">To Date</label>
                <input type="text" class="crm-input crm-date" id="date_to" placeholder="Select" autocomplete="off">
            </div>
            <div style="min-width:120px">
                <label class="crm-label">Quick Select</label>
                <select class="crm-input crm-select" id="quick_select">
                    <option value="">Custom</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month" selected>This Month</option>
                    <option value="quarter">This Quarter</option>
                    <option value="year">This Year</option>
                </select>
            </div>
            <button type="submit" class="pipe-pill" style="margin-top:auto"><i class="bx bx-refresh"></i> Update</button>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-row" style="grid-template-columns:repeat(auto-fill,minmax(170px,1fr))">
        <div class="kpi-card k-green">
            <div class="kpi-lbl">Total Credits</div>
            <div class="kpi-val">$45,230</div>
        </div>
        <div class="kpi-card k-red">
            <div class="kpi-lbl">Total Debits</div>
            <div class="kpi-val">$28,450</div>
        </div>
        <div class="kpi-card k-gold">
            <div class="kpi-lbl">Net Balance</div>
            <div class="kpi-val">$16,780</div>
        </div>
        <div class="kpi-card k-blue">
            <div class="kpi-lbl">Transactions</div>
            <div class="kpi-val">248</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid-2 mb-2">
        <div class="ex-card sec-card">
            <div class="sec-hdr"><i class="bx bx-line-chart"></i> Monthly Trends</div>
            <div class="sec-body"><div id="chartTrends" style="min-height:260px"></div></div>
        </div>
        <div class="ex-card sec-card">
            <div class="sec-hdr"><i class="bx bx-pie-chart-alt-2"></i> Credit vs Debit</div>
            <div class="sec-body"><div id="chartPie" style="min-height:260px"></div></div>
        </div>
    </div>

    {{-- Category Breakdown --}}
    <div class="ex-card sec-card">
        <div class="sec-hdr"><i class="bx bx-category-alt"></i> Breakdown by Category</div>
        <div class="sec-body">
            <div class="cat-row">
                <div class="cat-name">Commission</div>
                <div class="cat-bar-wrap"><div class="cat-bar-fill" style="width:85%"></div></div>
                <div class="cat-amt">$24,500</div>
                <div class="cat-pct">54%</div>
            </div>
            <div class="cat-row">
                <div class="cat-name">Payment</div>
                <div class="cat-bar-wrap"><div class="cat-bar-fill" style="width:65%"></div></div>
                <div class="cat-amt">$12,350</div>
                <div class="cat-pct">27%</div>
            </div>
            <div class="cat-row">
                <div class="cat-name">Expense</div>
                <div class="cat-bar-wrap"><div class="cat-bar-fill" style="width:35%"></div></div>
                <div class="cat-amt">$5,230</div>
                <div class="cat-pct">12%</div>
            </div>
            <div class="cat-row">
                <div class="cat-name">Bonus</div>
                <div class="cat-bar-wrap"><div class="cat-bar-fill" style="width:25%"></div></div>
                <div class="cat-amt">$2,100</div>
                <div class="cat-pct">5%</div>
            </div>
            <div class="cat-row">
                <div class="cat-name">Refund</div>
                <div class="cat-bar-wrap"><div class="cat-bar-fill" style="width:15%"></div></div>
                <div class="cat-amt">$850</div>
                <div class="cat-pct">2%</div>
            </div>
            <div class="cat-row">
                <div class="cat-name">Other</div>
                <div class="cat-bar-wrap"><div class="cat-bar-fill" style="width:10%"></div></div>
                <div class="cat-amt">$200</div>
                <div class="cat-pct">0.4%</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
$(function(){
    $('.crm-select').select2({minimumResultsForSearch:10,width:'100%'});
    var dfrom=$('#date_from').datepicker({format:'yyyy-mm-dd',autoclose:true,todayHighlight:true,clearBtn:true});
    var dto=$('#date_to').datepicker({format:'yyyy-mm-dd',autoclose:true,todayHighlight:true,clearBtn:true});
    var today=new Date(), firstOfMonth=new Date(today.getFullYear(),today.getMonth(),1);
    $('#date_from').datepicker('setDate',firstOfMonth);
    $('#date_to').datepicker('setDate',today);

    document.getElementById('quick_select').addEventListener('change',function(){
        var v=this.value,t=new Date(),f;
        switch(v){
            case 'today':f=new Date();break;
            case 'week':f=new Date(t);f.setDate(t.getDate()-t.getDay());break;
            case 'month':f=new Date(t.getFullYear(),t.getMonth(),1);break;
            case 'quarter':var q=Math.floor(t.getMonth()/3);f=new Date(t.getFullYear(),q*3,1);break;
            case 'year':f=new Date(t.getFullYear(),0,1);break;
        }
        if(f){$('#date_from').datepicker('setDate',f);$('#date_to').datepicker('setDate',new Date());}
    });
    });

    // Monthly trends area chart
    new ApexCharts(document.querySelector('#chartTrends'),{
        chart:{type:'area',height:260,background:'transparent',toolbar:{show:false}},
        series:[{name:'Credits',data:[5200,6800,4300,7100,8200,6500,7060]},{name:'Debits',data:[3100,4200,3800,5100,4600,3900,3750]}],
        xaxis:{categories:['Jan','Feb','Mar','Apr','May','Jun','Jul'],labels:{style:{colors:'#94a3b8',fontSize:'10px'}}},
        yaxis:{labels:{style:{colors:'#94a3b8',fontSize:'10px'},formatter:function(v){return '$'+v.toLocaleString()}}},
        colors:['#10b981','#ef4444'],
        fill:{type:'gradient',gradient:{shadeIntensity:1,opacityFrom:.35,opacityTo:.05}},
        stroke:{curve:'smooth',width:2},
        grid:{borderColor:'rgba(212,175,55,.08)'},
        dataLabels:{enabled:false},
        legend:{position:'top',fontSize:'11px',labels:{colors:'#94a3b8'}},
        theme:{mode:'dark'}
    }).render();

    // Pie chart
    new ApexCharts(document.querySelector('#chartPie'),{
        chart:{type:'donut',height:260,background:'transparent'},
        series:[45230,28450],labels:['Credits','Debits'],
        colors:['#10b981','#ef4444'],
        stroke:{width:0},
        plotOptions:{pie:{donut:{size:'70%',labels:{show:true,total:{show:true,label:'Net',fontSize:'12px',color:'#d4af37',formatter:function(){return '$16,780'}}}}}},
        legend:{position:'bottom',fontSize:'11px',labels:{colors:'#94a3b8'}},
        dataLabels:{style:{fontSize:'10px'}},
        theme:{mode:'dark'}
    }).render();
});
</script>
@endsection
