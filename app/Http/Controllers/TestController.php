<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sensor;
use Illuminate\Http\Request;
use App\Events\NewContentNotification;

class TestController extends Controller
{
    public function Send()
    {
        $data = [
            'message1' => 'Sensor DHT',
            'message2' => 'Sensor XYZ',
            // Tambahkan data lainnya sesuai kebutuhan Anda
        ];

        event(new NewContentNotification($data));
    }

    public function process(Request $request)
    {
        // Kelola sensor
        $data = $request->json()->all();
        $smoke = $data['smoke'];
        $flame = $data['flame'];
        $humidity = $data['humidity'];
        $temperature = $data['temperature'];

        // Menambahkan dua digit desimal ke nilai "smoke"
        $smoke_format = sprintf("%.2f", $smoke); // Format nilai "smoke" dengan dua digit desimal
        $smokes = number_format($smoke_format, 2, '.', '');
        // Mendapatkan tanggal dan waktu lokal saat ini

        $humidity_format = sprintf("%.2f", $humidity); // Format nilai "smoke" dengan dua digit desimal
        $humiditys = number_format($humidity_format, 2, '.', '');

        $temperature_format = sprintf("%.2f", $temperature); // Format nilai "smoke" dengan dua digit desimal
        $temperatures = number_format($temperature_format, 2, '.', '');

        $currentDateTime = Carbon::now()->toDateTimeString();

        // dd($smokes);

        // Simpan data sensor ke dalam database
        $sensor = new Sensor();
        $sensor->smoke = $smokes;
        $sensor->flame = $flame;
        $sensor->humidity = $humiditys;
        $sensor->temperature = $temperatures;
        $sensor->created_at = $currentDateTime; // Menggunakan waktu lokal saat ini
        $sensor->send_date = $currentDateTime; // Menggunakan waktu lokal saat ini
        $sensor->save();

        // Mengambil data sensor
        $data_sensor = Sensor::orderBy('send_date', 'desc')->first();

        // dd($data_sensor);
        // Format tanggal ke dalam format "d M Y"
        $formattedDate = $data_sensor->created_at->format('Y-m-d H:i:s');

        // Siapkan data untuk dikirim ke Pusher
        $data = [
            'smoke' => $data_sensor->smoke,
            'flame' => $data_sensor->flame,
            'temperature' => $data_sensor->temperature,
            'humidity' => $data_sensor->humidity,
            'send_date' => $formattedDate
        ];

        // Kirim data ke Pusher
        event(new NewContentNotification($data));

        return response()->json([
            'status' => 200,
            'message' => 'Success saved'
        ], 201);
    }

    public function getAll()
    {
        $data_sensor = Sensor::whereBetween('send_date', [now()->subHours(24), now('Asia/Jakarta')->toDateTimeString()])
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $data_sensor
        ], 200);
    }
}
