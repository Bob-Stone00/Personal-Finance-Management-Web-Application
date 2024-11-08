class DataService {
  constructor(apiUrl) {
    this.apiUrl = apiUrl;
  }

  async fetchTransactionData() {
    try {
      const response = await fetch(this.apiUrl);
      return await response.json();
    } catch (error) {
      throw new Error(`Failed to fetch transaction data: ${error.message}`);
    }
  }
}

class ChartDataTransformer {
  transformData(transactions) {
    return {
      labels: transactions.map((transaction) => transaction.date),
      amounts: transactions.map((transaction) => transaction.amount),
    };
  }
}

class ChartConfig {
  static getConfig(labels, amounts) {
    return {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Daily Transactions",
            data: amounts,
            backgroundColor: "rgba(75, 192, 192, 0.2)",
            borderColor: "rgba(75, 192, 192, 1)",
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    };
  }
}

class ChartManager {
  constructor(chartContext, dataService, transformer) {
    this.chartContext = chartContext;
    this.dataService = dataService;
    this.transformer = transformer;
    this.chart = null;
  }

  async initializeChart() {
    try {
      const data = await this.dataService.fetchTransactionData();
      const { labels, amounts } = this.transformer.transformData(data);
      const config = ChartConfig.getConfig(labels, amounts);

      this.chart = new Chart(this.chartContext, config);
      return this.chart;
    } catch (error) {
      console.error("Error initializing chart:", error);
      throw error;
    }
  }

  destroyChart() {
    if (this.chart) {
      this.chart.destroy();
      this.chart = null;
    }
  }
}

// Initialize chart when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("myChart").getContext("2d");
  const dataService = new DataService("transactions.php");
  const transformer = new ChartDataTransformer();

  const chartManager = new ChartManager(ctx, dataService, transformer);
  chartManager.initializeChart();
});
