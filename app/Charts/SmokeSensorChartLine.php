<?php

namespace App\Charts;

use App\Models\Sensor;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class SmokeSensorChartLine
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\LineChart
    {
        // Menginisialisasi array kosong untuk menyimpan data yang akan digunakan untuk grafik
        $smokeData = [];
        $hourlyData = [];

        // Mengambil data sensor dalam rentang waktu tertentu
        $data_sensor = Sensor::whereBetween('created_at', [now()->subHours(24), now()]) // Ambil data sensor dalam 24 jam terakhir
            ->orderBy('created_at', 'desc') // Urutkan berdasarkan created_at secara ascending
            ->get();

        // Mengelompokkan data sensor per jam dan menghitung rata-rata nilai smoke
        foreach ($data_sensor as $sensor) {
            $hour = $sensor->created_at->format('H'); // Ambil jam dari created_at
            if (!isset($hourlyData[$hour])) {
                $hourlyData[$hour] = ['total' => 0, 'count' => 0];
            }
            $hourlyData[$hour]['total'] += $sensor->smoke; // Tambahkan nilai smoke ke total per jam
            $hourlyData[$hour]['count']++; // Hitung jumlah data per jam
        }

        // Hitung rata-rata nilai smoke per jam
        foreach ($hourlyData as $hour => $data) {
            $averageSmoke = $data['total'] / $data['count'];
            $smokeData[] = round($averageSmoke, 2); // Bulatkan rata-rata smoke per jam ke 2 desimal
        }

        // Menggunakan data yang sudah diubah ke dalam format yang sesuai untuk membuat grafik
        return $this->chart->lineChart()
            ->setTitle('Smoke Sensor Data per Hour')
            ->setSubtitle('MQ2 Sensor.')
            ->addData('Smoke', $smokeData)
            ->setXAxis(array_keys($hourlyData)); // Gunakan jam sebagai sumbu X
    }
}
