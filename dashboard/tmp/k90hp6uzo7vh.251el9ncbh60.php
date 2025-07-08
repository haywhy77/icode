<div class="row">
    <div class="col-12 col-lg-9">
        <div class="card">
            <div class="page-heading">
                <div class="page-heading__container">
                    <h1 class="title">Applicants turn ups</h1>
                    <p class="caption">Total volume 15,33 tons.</p>
                </div>
            </div>
            <div class="card-container">
                <div class="dropdown">
                    <div class="rw-btn rw-btn--card" data-bs-toggle="dropdown"><div></div></div>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item">Print data</a>
                        <a href="#" class="dropdown-item">Save data</a>
                        <a href="#" class="dropdown-item">Mark as important</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
        
                <canvas id="barChart"></canvas>
        
            </div>
        </div>
        
        <div class="card">
            <div class="page-heading">
                <div class="page-heading__container">
                    <h1 class="title">Deliveries by exporting companies</h1>
                    <p class="caption">Total volume 12,01 tons.</p>
                </div>
            </div>
            <div class="card-container">
                <div class="dropdown">
                    <div class="rw-btn rw-btn--card" data-bs-toggle="dropdown"><div></div></div>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item">Print data</a>
                        <a href="#" class="dropdown-item">Save data</a>
                        <a href="#" class="dropdown-item">Mark as important</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
        
                <div class="row margin-top-20">
                    <div class="col-12">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
        
            </div>
        </div>
    </div>
</div>
<div class="hide barcharYear" style="display: none;"><?= ($barchartYear) ?></div>
<div class="hide barcharValue" style="display: none;"><?= ($barchartValue) ?></div>


<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {

        var ctxbar = document.getElementById("barChart").getContext("2d");
        var color = Chart.helpers.color;
        // alert($(".barcharYear").html())
        var keys= $(".barcharYear").html().split(",");
        var values= $(".barcharValue").html().split(",");
        console.log(values)
        window.myBar = new Chart(ctxbar, {
            type: 'bar',
            data: {
                labels: keys,
                datasets: [{
                    label: 'Total volume',
                    backgroundColor: color(window.chartColors.primary).rgbString(),
                    borderColor: window.chartColors.primary,
                    borderWidth: 1,
                    data: values
                }]
            },
            options: {
                responsive: true,
                legend: {
                    display: false,
                    position: 'top',
                },
                title: {
                    display: false
                }
            }
        });


        //for histogram
        var configLine = {
            type: 'line',
            data: {
                labels: keys,
                datasets: [{
                    label: 'FF Lowride',
                    fill: false,
                    backgroundColor: window.chartColors.success,
                    borderColor: window.chartColors.success,
                    borderWidth: 1,
                    data: values
                }, {
                    label: 'DreamEnt.',
                    fill: false,
                    backgroundColor: window.chartColors.warning,
                    borderColor: window.chartColors.warning,
                    data: values
                }, {
                    label: 'PrentEnt dr.',
                    fill: false,
                    backgroundColor: window.chartColors.danger,
                    borderColor: window.chartColors.danger,
                    data: values
                }]
            },
            options: {
                responsive: true,
                title:{
                    display: false
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: false
                        }
                    }]
                }
            }
        };

        var ctx = document.getElementById("lineChart").getContext("2d");
        var myLine = new Chart(ctx, configLine);
    });
</script>