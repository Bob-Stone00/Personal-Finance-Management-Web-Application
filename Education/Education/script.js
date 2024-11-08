// Include Chart.js in your project (add this in <head>)
// <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

const ctx = document.getElementById('expenseChart').getContext('2d');
const expenseChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        datasets: [{
            label: 'Expenses',
            data: [5000, 4500, 7000, 5500, 3000, 1500, 500],
            borderColor: '#ff4c61',
            borderWidth: 2,
            fill: false,
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
