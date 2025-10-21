var app = $.spapp({
  defaultView: "#home",
  templateDir: "./"
});

app.run();

function initVotesChart(){
  const canvas = document.getElementById('myChart');
  if (!canvas) {
    setTimeout(initVotesChart, 200);
    return;
  }

  if (canvas.dataset.chartInitialized) return;

  const ctx = canvas.getContext('2d');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Kamala Harris', 'Donald Trump', 'Robert F. Kennedy'],
      datasets: [{
        label: 'Votes',
        data: [10000, 15000, 5000],
        backgroundColor: ['#4e73df', '#e74a3b', '#c33dcd'],
        borderWidth: 2,
        borderColor: '#000000ff'
      }]
    },
    options: {
      responsive: true,
      indexAxis: 'y',
      plugins: {
        legend: { 
          display: false 
        }
      }
    }
  });

  canvas.dataset.chartInitialized = '1';
}



window.addEventListener('load', initVotesChart); 