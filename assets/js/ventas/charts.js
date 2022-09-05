const data = {
    labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    datasets: [{
      label: 'Año',
      data: [65000, 59000, 80000, 81000, 26000, 55000, 40000, 70000, 30000, 192000, 24000, 383000],
      fill: false,
      borderColor: 'rgb(75, 192, 192)',
    }]
  };

  const config = {
    type: 'line',
    data: data,
    options: {}
  };

  const myChart = new Chart(
    document.getElementById('salesOverview'),
    config
  );
