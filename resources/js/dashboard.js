// Sales Overview Line Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
        type: 'line',
            data: {
                labels: ['Apr 1', 'Apr 6', 'Apr 11', 'Apr 16', 'Apr 21', 'Apr 26', 'Apr 30'],
                datasets: [{
                    label: 'Sales',
                    data: [4000, 5000, 4200, 5500, 6000, 5800, 7000],
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#14b8a6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
         });

        // Sales by Category Pie Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Exhausts', 'Helmets', 'Tires', 'Brakes', 'Others'],
                datasets: [{
                    data: [35, 25, 20, 10, 10],
                    backgroundColor: ['#14b8a6', '#22c55e', '#eab308', '#a855f7', '#9ca3af'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } }
            }
        });

        // Monthly Sales Comparison Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Nov 23', 'Dec 23', 'Jan 24', 'Feb 24', 'Mar 24', 'Apr 24'],
                datasets: [{
                    label: 'Sales (₱)',
                    data: [40000, 45000, 50000, 60000, 70000, 78930],
                    backgroundColor: '#14b8a6',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                    }
                }
            }
         }
    }
});

