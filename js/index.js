$(document).ready(function () {
    sendFirstSessionRequest();
});

function sendFirstSessionRequest() {
    $.ajax({
        url: 'first_session.php',
        method: 'POST',
        success: function (jsonResponse) {
            let response = JSON.parse(jsonResponse);

            if (!response.x_axis) {
                response.x_axis = [];
            }

            if (!response.y_axis) {
                response.y_axis = [];
            }
            drawLineGraph(response);
        },
        error: function (xhr, status, error) {
            alert('Error: ' + error);
        }
    });
}

function drawLineGraph(response) {
    const graphData = {
        labels: response.x_axis,
        datasets: [{
            label: 'First Session',
            data: response.y_axis,
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    };

    new Chart($('#myChart'), {
        type: 'line',
        data: graphData,
    });
}
