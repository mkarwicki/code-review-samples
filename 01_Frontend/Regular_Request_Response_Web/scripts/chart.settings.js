$(function(){
    var rangeBuffer;
    var numberOfNightsRange = document.getElementById('numberOfNightsRange');
    var firstSetup=1;
    noUiSlider.create(numberOfNightsRange, {
        start: [defaultNumberOfNights],
        step:1,
        range: {
            'min': 1,
            'max': 365
        },
        format: wNumb({
            decimals: 0
        })
    });
    numberOfNightsRange.noUiSlider.on('update', function(){
        if(firstSetup>=3) {
            value = numberOfNightsRange.noUiSlider.get();
            $('#days-val').html(value);
            clearTimeout(rangeBuffer);
            rangeBuffer = setTimeout(function () {
                var calcData = numberOfNightsRange.noUiSlider.get() * pricePerNightRange.noUiSlider.get();
                updateChartData(calcData);
            }, 500);
        }else{
            firstSetup++;
        }
    });
    var pricePerNightRange = document.getElementById('pricePerNightRange');
    noUiSlider.create(pricePerNightRange, {
        start: [defaultCalcPricePerDay],
        step:1,
        range: {
            'min': 1,
            'max': 999
        },
        format: wNumb({
            decimals: 0
        })
    });
    pricePerNightRange.noUiSlider.on('update', function(){
        if(firstSetup>=3){
            value=pricePerNightRange.noUiSlider.get();
            $('#price-val').html(value+' PLN');
            clearTimeout(rangeBuffer);
            rangeBuffer = setTimeout(function(){
                var calcData=numberOfNightsRange.noUiSlider.get()*pricePerNightRange.noUiSlider.get();
                updateChartData(calcData);
            }, 500);
        }else{
            firstSetup++;
        }
    });
    $(document).on('change','#apartmentTypeChange',function(){
        var currentValNumber=$(this).find('option:selected').attr('data-value');
        var currentValTitle=$(this).find('option:selected').html();
        pricePerNightRange.noUiSlider.set(currentValNumber);
        $('#summaryApartmentTypeTitle').html(currentValTitle);
    });
    var ctx = document.getElementById("canvas").getContext("2d");
    window.myBar = new Chart(ctx, {
        type: 'bar',
        data: '',
        options: {
            title:{
                display:false
            },
            legend: {
                display:false
            },
            tooltips: {
                enabled:false
            },
            responsive: true,
            scales: {
                xAxes: [{
                    stacked: true,
                    color:'rgba(255,255,255,1)',
                    ticks: {
                        fontColor: '#333333'
                    },
                    gridLines: {
                        display:false
                    },
                    barPercentage:0.43
                }],
                yAxes: [{
                    stacked: true,
                    ticks: {
                        fontColor: '#333333'
                    }
                }]
            }
        }
    });
    var barVal=numberOfNightsRange.noUiSlider.get()*pricePerNightRange.noUiSlider.get();
    updateChartData(barVal)
})
function updateChartData(barVal){
    $('#calc-income-calc-section-container .calc .calc-summary .calc-summary-value .value-container .value').html(numberWithSpace(barVal));
    var barChartData = {
        labels: ["1", "2", "3", "4", "5"],
        datasets: [{
            label: '1',
            backgroundColor: 'rgba(255,255,255,0.5)',
            data: [
                barVal,barVal,barVal,barVal,barVal
            ]
        }, {
            label: '2',
            backgroundColor: 'rgba(255,255,255,0.6)',
            data: [
                0,0,barVal,barVal,barVal
            ]
        }, {
            label: '3',
            backgroundColor: 'rgba(255,255,255,0.7)',
            data: [
                0,0,0,barVal,barVal
            ]
        }, {
            label: '4',
            backgroundColor: 'rgba(255,255,255,0.8)',
            data: [
                0,0,0,0,barVal
            ]
        }, {
            label: '5',
            backgroundColor: 'rgba(255,255,255,1)',
            data: [
                0,barVal,barVal,barVal,barVal
            ]
        }]
    };
    window.myBar.data=barChartData;
    window.myBar.update(0);
}
function numberWithSpace(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}









