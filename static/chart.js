fetch("/sk/customer_get_statistics.php")
  .then((response) => {
    if (response.ok) {
      return response.json();
    } else {
      throw new Error("Ошибка при передачи данных.");
    }
  })
  .then((answer) => {
    const acceptedOrdersAmount = answer["accepted_orders_amount"];
    const allOrdersAmount = answer["all_orders_amount"];

    const Utils = ChartUtils.init();
    
    const data = {
      labels: ['Статистика'],
      datasets: [
        {
          label: 'Кол-во одобренных отзывов',
          data: [acceptedOrdersAmount],
          borderWidth: 1,
          backgroundColor: Utils.CHART_COLORS.green,
        },
        {
          label: 'Общее кол-во отзывов',
          data: [allOrdersAmount],
          borderWidth: 1,
          backgroundColor: Utils.CHART_COLORS.yellow,
        },
      ],
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    };
    
    const config = {
      type: 'bar',
      data: data,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
        }
      }
    };
    
    const ctx = document.getElementById('statistics');
    new Chart(ctx, config);
  });