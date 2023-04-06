import './bootstrap';
import {Chart} from "chart.js/auto";

$( function() {
    var dateFormat = "yy-mm-dd",
        from = $( "#from" )
            .datepicker({
                maxDate: "0",
                numberOfMonths: 2,
                dateFormat: 'yy-mm-dd'
            })
            .on( "change", function() {
                to.datepicker( "option", "minDate", getDate( this ) );
            }),
        to = $( "#to" )
            .datepicker({
                maxDate: "0",
                numberOfMonths: 2,
                dateFormat: 'yy-mm-dd'
            })
            .on( "change", function() {
                from.datepicker( "option", "maxDate", getDate( this ) );
            });


    function getDate( element ) {
        var date;
        try {
            date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
            date = null;
        }

        return date;
    }
});


function makeChart(historicQuotes) {
    const data = historicQuotes

    new Chart(
        document.getElementById('chart'),
        {
            type: 'bar',
            data: {
                labels: data.map(row => row.date),
                datasets: [
                    {
                        label: 'Open',
                        data: data.map(row => row.open)
                    },
                    {
                        label: 'Close',
                        data: data.map(row => row.close)
                    }
                ]
            }
        }
    );
}

window.makeChart = makeChart;
