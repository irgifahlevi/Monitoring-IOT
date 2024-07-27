@extends('layouts.template_master_admin')
@section('content')
@include('layouts.template.navbar')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div>
      <div class="row">
        <div class="col-lg-12 col-md-12 mb-4">
          <div class="card">
            <div class="d-flex align-items-end row">
              <div class="col-sm-7">
                <div class="card-body">
                  {{-- <h5 class="card-title text-primary">Hi, Welcome back {{ Auth::user()->name }} ! 🎉</h5> --}}
                  <h5 class="card-title text-primary">Hi, Welcome back ! 🎉</h5>
                  
                  <p class="mb-4">
                    You have done <span class="fw-bold">72%</span> more sales today. Check your new badge in
                    your profile.
                  </p>

                  <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a>
                </div>
              </div>
              <div class="col-sm-5 text-center text-sm-left">
                <div class="card-body pb-0 px-0 px-md-4">
                  <img
                    src="{{asset('Template/assets/img/illustrations/man-with-laptop-light.png')}}"
                    height="140"
                    alt="View Badge User"
                    data-app-dark-img="illustrations/man-with-laptop-dark.png"
                    data-app-light-img="illustrations/man-with-laptop-light.png"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

                <!-- Total Revenue -->
                <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
                  <div class="card">
                    <div class="row row-bordered g-0">
                      <div>
                        <div id="lineCharts"></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!--/ Total Revenue -->
                <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
                  <div class="row">
                    <div class="col-6 mb-4">
                      <div class="card">
                        <div class="card-header m-0 text-center text-muted d-block mb-1">Smoke</div>
                        <div id="chartLevels"></div>
                      </div>
                    </div>
                    <div class="col-12 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                            <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                              <div class="card-title">
                                <h5 class="text-nowrap mb-2">Fire status</h5>
                              </div>
                              <div id="flameStatus" class="mt-sm-auto"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-6 mb-4">
                      <div class="card">
                        <div class="card-header m-0 text-center text-sm-center">Humidity</div>
                        <div id="humidityLevels"></div>
                      </div>
                    </div>
                    <div class="col-6 mb-4">
                      <div class="card">
                        <div class="card-header m-0 text-center text-sm-center">Temperature</div>
                        <div id="temperatureLevels"></div>
                      </div>
                    </div>
                  </div>
                </div>
      </div>                   
    </div>
  </div>
  {{-- Footer --}}
  @include('layouts.template.footer')

</div>
<script>
var chartLevel;
var humLevel;
var tempLevel;

var pusher = new Pusher('ae6d2ffdbf165d54683d', {
    cluster: 'ap1'
});

var channel = pusher.subscribe('data-sensor');
channel.bind('my-event', function(data) {
    var smokeValue = parseFloat(data.data.smoke);
    var fireValue = parseFloat(data.data.flame);
    var humidityValue = parseFloat(data.data.humidity);
    var temperatureValue = parseFloat(data.data.temperature);

    if (!isNaN(smokeValue) && isFinite(smokeValue)) {
        updateChartLevel(smokeValue);
    } else {
        console.error('Invalid smoke value received:', data.data.smoke);
    }

    if (!isNaN(humidityValue) && isFinite(humidityValue)) {
        updateHumidityLevel(humidityValue);
    } else {
        console.error('Invalid humidity value received:', data.data.humidity);
    }

    if (!isNaN(temperatureValue) && isFinite(temperatureValue)) {
        updateTemperatureLevel(temperatureValue);
    } else {
        console.error('Invalid temperature value received:', data.data.temperature);
    }

    updateFireStatus(fireValue);
});

function updateChartLevel(smokeValue) {
    // Memastikan smokeValue adalah angka yang valid
    if (typeof smokeValue === 'number' && !isNaN(smokeValue) && isFinite(smokeValue)) {
        if (chartLevel) {
            // Jika grafik sudah ada, kita akan memperbarui datanya
            chartLevel.updateSeries([smokeValue]);
        } else {
            // Jika grafik belum ada, kita akan membuat grafik baru dengan data ini
            renderChartLevel(smokeValue);
        }
    } else {
        console.error('Invalid smoke value:', smokeValue);
    }
}

function renderChartLevel(smokeValue) {
    let config = {
      colors: {
        primary: '#696cff',
        secondary: '#8592a3',
        success: '#71dd37',
        info: '#03c3ec',
        warning: '#ffab00',
        danger: '#ff3e1d',
        dark: '#233446',
        black: '#000',
        white: '#fff',
        body: '#f4f5fb',
        headingColor: '#566a7f',
        axisColor: '#a1acb8',
        borderColor: '#eceef1'
      }
    };

    let cardColor, headingColor, axisColor, shadeColor, borderColor;

    cardColor = config.colors.white;
    headingColor = config.colors.headingColor;
    axisColor = config.colors.axisColor;
    borderColor = config.colors.borderColor;

    var options = {
        series: [smokeValue],
        chart: {
            height: 115,
            type: 'radialBar',
            offsetY: -15
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                dataLabels: {
                    name: {
                        fontSize: '12px',
                        color: headingColor,
                        offsetY: 20,
                        show: true,
                        fontFamily: 'Public Sans'
                      
                    },
                    value: {
                        offsetY: -14,
                        fontSize: '15px',
                        color: headingColor,
                        show:true,
                        fontFamily: 'Public Sans',
                        formatter: function(val) {
                            return val + "%";
                        }
                    }
                }
            }
        },
        colors: [config.colors.primary],
        fill: {
          type: 'gradient',
          gradient: {
            shade: 'dark',
            shadeIntensity: 0.5,
            gradientToColors: [config.colors.primary],
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 0.6,
            stops: [30, 70, 100]
          }
        },
        grid: {
          padding: {
            top: -15,
            bottom: -7,
            left: -10,
            right: -10
          }
        },
        stroke: {
            dashArray: 5
        },
        labels: ['ppm'],
    };

    chartLevel = new ApexCharts(document.querySelector("#chartLevels"), options);
    chartLevel.render();
}


function updateFireStatus(fireValue) {
    var flameStatusElement = document.getElementById('flameStatus');
    if (!flameStatusElement) {
        console.error('Element with id "flameStatus" not found.');
        return;
    }

    var badgeClass, statusText;

    // Menentukan kelas badge dan teks status berdasarkan nilai fireValue
    if (fireValue === 0) {
        badgeClass = 'bg-danger';
        statusText = 'Fire';
    } else if (fireValue === 1) {
        badgeClass = 'bg-success';
        statusText = 'No Fire';
    } else {
        console.error('Invalid fire value:', fireValue);
        return;
    }

    // Memperbarui elemen HTML dengan kelas badge dan teks status yang sesuai
    flameStatusElement.innerHTML = '<span class="badge rounded-pill ' + badgeClass + ' mb-2">' + statusText + '</span><h3 class="mb-0">Detected</h3>';
}


function humidityLevel(hValue)
{
  let config = {
      colors: {
        primary: '#696cff',
        secondary: '#8592a3',
        success: '#71dd37',
        info: '#03c3ec',
        warning: '#ffab00',
        danger: '#ff3e1d',
        dark: '#233446',
        black: '#000',
        white: '#fff',
        body: '#f4f5fb',
        headingColor: '#566a7f',
        axisColor: '#a1acb8',
        borderColor: '#eceef1'
      }
    };

    let cardColor, headingColor, axisColor, shadeColor, borderColor;

    cardColor = config.colors.white;
    headingColor = config.colors.headingColor;
    axisColor = config.colors.axisColor;
    borderColor = config.colors.borderColor;

    var options = {
        series: [hValue],
        chart: {
            height: 115,
            type: 'radialBar',
            offsetY: -15
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                dataLabels: {
                    name: {
                        fontSize: '12px',
                        color: headingColor,
                        offsetY: 20,
                        show: true,
                        fontFamily: 'Public Sans'
                      
                    },
                    value: {
                        offsetY: -14,
                        fontSize: '15px',
                        color: headingColor,
                        show:true,
                        fontFamily: 'Public Sans',
                        formatter: function(val) {
                            return val + "%";
                        }
                    }
                }
            }
        },
        colors: [config.colors.primary],
        fill: {
          type: 'gradient',
          gradient: {
            shade: 'dark',
            shadeIntensity: 0.5,
            gradientToColors: [config.colors.primary],
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 0.6,
            stops: [30, 70, 100]
          }
        },
        grid: {
          padding: {
            top: -15,
            bottom: -7,
            left: -10,
            right: -10
          }
        },
        stroke: {
            dashArray: 5
        },
        labels: ['RH'],
    };

    humLevel = new ApexCharts(document.querySelector("#humidityLevels"), options);
    humLevel.render();
}

function updateHumidityLevel(humidityValue)
{
  if (typeof humidityValue === 'number' && !isNaN(humidityValue) && isFinite(humidityValue)) {
        if (humLevel) {
            // Jika grafik sudah ada, kita akan memperbarui datanya
            humLevel.updateSeries([humidityValue]);
        } else {
            // Jika grafik belum ada, kita akan membuat grafik baru dengan data ini
            humidityLevel(humidityValue);
        }
    } else {
        console.error('Invalid humidity value:', humidityValue);
    }
}

function temperatureLevel(tValue)
{
  let config = {
      colors: {
        primary: '#696cff',
        secondary: '#8592a3',
        success: '#71dd37',
        info: '#03c3ec',
        warning: '#ffab00',
        danger: '#ff3e1d',
        dark: '#233446',
        black: '#000',
        white: '#fff',
        body: '#f4f5fb',
        headingColor: '#566a7f',
        axisColor: '#a1acb8',
        borderColor: '#eceef1'
      }
    };

    let cardColor, headingColor, axisColor, shadeColor, borderColor;

    cardColor = config.colors.white;
    headingColor = config.colors.headingColor;
    axisColor = config.colors.axisColor;
    borderColor = config.colors.borderColor;

    var options = {
        series: [tValue],
        chart: {
            height: 115,
            type: 'radialBar',
            offsetY: -15
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                dataLabels: {
                    name: {
                        fontSize: '12px',
                        color: headingColor,
                        offsetY: 20,
                        show: true,
                        fontFamily: 'Public Sans'
                      
                    },
                    value: {
                        offsetY: -14,
                        fontSize: '15px',
                        color: headingColor,
                        show:true,
                        fontFamily: 'Public Sans',
                        formatter: function(val) {
                            return val + "°C";
                        }
                    }
                }
            }
        },
        colors: [config.colors.primary],
        fill: {
          type: 'gradient',
          gradient: {
            shade: 'dark',
            shadeIntensity: 0.5,
            gradientToColors: [config.colors.primary],
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 0.6,
            stops: [30, 70, 100]
          }
        },
        grid: {
          padding: {
            top: -15,
            bottom: -7,
            left: -10,
            right: -10
          }
        },
        stroke: {
            dashArray: 5
        },
        labels: ['celcius'],
    };

    tempLevel = new ApexCharts(document.querySelector("#temperatureLevels"), options);
    tempLevel.render();
}

function updateTemperatureLevel(tempValue)
{
  if (typeof tempValue === 'number' && !isNaN(tempValue) && isFinite(tempValue)) {
        if (tempLevel) {
            // Jika grafik sudah ada, kita akan memperbarui datanya
            tempLevel.updateSeries([tempValue]);
        } else {
            // Jika grafik belum ada, kita akan membuat grafik baru dengan data ini
            temperatureLevel(tempValue);
        }
    } else {
        console.error('Invalid temperature value:', tempValue);
    }
}

</script>

{{-- <script>
  var lineChart;
  var sensorData = []; // variabel untuk menyimpan data sensor

  function fetchDataAndRenderChart() {
      $.ajax({
          url: '{{route('get.sensor')}}',
          type: 'GET',
          dataType: 'json',
          success: function(response) {
              //console.log(response.data);
              sensorData = response.data; // asumsikan respons berisi array data sensor
              renderChart();
          },
          error: function(xhr, status, error) {
              console.error('Error fetching data:', error);
          }
      });
  }

  function renderChart() {
    var timestamps = sensorData.map(data => {
            // Ubah format timestamp sesuai dengan 'YYYY-MM-DD HH:MM:SS' dari data API
            var dateObj = new Date(data.send_date);
            
            // Set zona waktu ke Asia/Jakarta
            var options = { timeZone: 'Asia/Jakarta' };
            var formattedTime = dateObj.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}); // Mendapatkan waktu dalam format HH:mm
            
            return formattedTime;
        });
      var smokeValue = sensorData.map(data => data.smoke);
      var humidityValue = sensorData.map(data => data.humidity);
      var temperatureValue = sensorData.map(data => data.temperature);

      // var options = {
      //     chart: {
      //         type: 'line',
      //         height: 350
      //     },
      //     series: [{
      //         name: 'Sensor values',
      //         data: values
      //     }],
      //     xaxis: {
      //           categories: timestamps,
      //           type: 'category', // gunakan tipe kategori
      //           labels: {
      //               datetimeFormatter: {
      //                   year: 'numeric',
      //                   month: 'short',
      //                   day: 'numeric',
      //                   hour: 'numeric',
      //                   minute: 'numeric',
      //                   hour12: false
      //               }
      //           },
      //           tickPlacement: 'on' // Menempatkan tanda centang tepat di bawah label
      //       },
      //     yaxis: {
      //         title: {
      //             text: 'Sensor Value'
      //         }
      //     }
      // };


      var optionsLine = {
        chart: {
          height: 328,
          type: 'line',
          zoom: {
            enabled: false
          },
          dropShadow: {
            enabled: true,
            top: 3,
            left: 2,
            blur: 4,
            opacity: 0.2,
          }
        },
        stroke: {
          curve: 'smooth',
          width: 4
        },
        //colors: ["#3F51B5", '#2196F3'],
        series: [{
            name: "Humidity",
            data: humidityValue
          },
          {
            name: "Temperature",
            data: temperatureValue
          },
          {
            name: "Smoke",
            data: smokeValue
          }
        ],
        title: {
          text: 'Datashet sensor',
          align: 'left',
          offsetY: 25,
          offsetX: 20
        },
        subtitle: {
          text: 'Statistics',
          offsetY: 55,
          offsetX: 20
        },
        markers: {
          size: 6,
          strokeWidth: 0,
          hover: {
            size: 9
          }
        },
        grid: {
          show: true,
          padding: {
            bottom: 0
          }
        },
        //labels: ['01/15/2002', '01/16/2002', '01/17/2002', '01/18/2002', '01/19/2002', '01/20/2002'],
        // 
        xaxis: {
                categories: timestamps,
                type: 'category', // gunakan tipe kategori
                labels: {
                    datetimeFormatter: {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: false
                    }
                },
                tickPlacement: 'on' // Menempatkan tanda centang tepat di bawah label
            },
        legend: {
          position: 'top',
          horizontalAlign: 'right',
          offsetY: -20
        }
      }

      if (!lineChart) {
          lineChart = new ApexCharts(document.querySelector("#lineCharts"), optionsLine);
          lineChart.render();
      } else {
          lineChart.updateOptions(optionsLine);
      }
  }


  
      
  

  // Fungsi untuk menghasilkan label dengan tanggal dan waktu saat ini
  function generateLabels() {
    var labels = [];
    var now = new Date();
    for (var i = 0; i <= 24; i++) { // Ubah sesuai jumlah label yang diinginkan
      labels.push(now.toLocaleString());
      now.setHours(now.getHours() - 1); // Kurangi 1 jam untuk setiap label berikutnya
    }
    console.log(labels);
    return labels.reverse(); // Balikkan array label untuk menampilkan urutan waktu yang benar
  }

  // var chartDht = new ApexCharts(document.querySelector("#lineChartsDht"), optionsLine);
  // chartDht.render();
      

  // Panggil fungsi fetchDataAndRenderChart setiap detik
  $(document).ready(function() {
      fetchDataAndRenderChart(); // panggil pertama kali saat dokumen siap
      setInterval(fetchDataAndRenderChart, 1500); // panggil setiap detik
  });
</script> --}}

<script>
  var lineChart;
  var sensorData = [];

  function fetchDataAndRenderChart() {
    $.ajax({
      url: '{{route('get.sensor')}}',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        sensorData = response.data;
        renderChart();
      },
      error: function(xhr, status, error) {
        console.error('Error fetching data:', error);
      }
    });
  }

  function renderChart() {
    var timestamps = sensorData.map(data => {
      var dateObj = new Date(data.send_date);
      var formattedTime = dateObj.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
      return formattedTime;
    });
    var smokeValue = sensorData.map(data => data.smoke);
    var humidityValue = sensorData.map(data => data.humidity);
    var temperatureValue = sensorData.map(data => data.temperature);

    var optionsLine = {
      chart: {
        height: 328,
        type: 'line',
        zoom: {
          enabled: false
        },
        animations: {
          enabled: false // Nonaktifkan animasi
        }
      },
      stroke: {
        curve: 'smooth',
        width: 4
      },
      series: [{
          name: "Humidity",
          data: humidityValue
        },
        {
          name: "Temperature",
          data: temperatureValue
        },
        {
          name: "Smoke",
          data: smokeValue
        }
      ],
      title: {
        text: 'Datashet sensor',
        align: 'left',
        offsetY: 25,
        offsetX: 20
      },
      subtitle: {
        text: 'Statistics',
        offsetY: 55,
        offsetX: 20
      },
      markers: {
        size: 6,
        strokeWidth: 0,
        hover: {
          size: 9
        }
      },
      grid: {
        show: true,
        padding: {
          bottom: 0
        }
      },
      xaxis: {
        categories: timestamps,
        type: 'category',
        labels: {
          datetimeFormatter: {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            hour12: false
          }
        },
        tickPlacement: 'on'
      },
      legend: {
        position: 'top',
        horizontalAlign: 'right',
        offsetY: -20
      }
    };

    if (!lineChart) {
      lineChart = new ApexCharts(document.querySelector("#lineCharts"), optionsLine);
      lineChart.render();
    } else {
      lineChart.updateOptions(optionsLine);
    }
  }

  $(document).ready(function() {
    fetchDataAndRenderChart();
    setInterval(fetchDataAndRenderChart, 2000); // Ubah interval pembaruan menjadi 5 detik
  });
</script>



@endsection

