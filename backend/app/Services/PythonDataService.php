<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonDataService
{
    /**
     * Gợi ý sản phẩm tương tự. Trả về [] nếu python-service không khả dụng.
     */
    public function recommend(string $maSanPham, int $topN = 4): array
    {
        $key = "py:recommend:{$maSanPham}:{$topN}";

        $cached = Cache::get($key);
        if ($cached !== null) {
            return $cached;
        }

        $data = $this->get("/recommend/{$maSanPham}", ['top_n' => $topN]);
        if ($data === null) {
            return [];
        }

        $goiY = $data['goi_y'] ?? [];
        Cache::put($key, $goiY, now()->addMinutes(30));

        return $goiY;
    }

    /**
     * Thống kê tỷ lệ lấp đầy theo tháng. Trả về [] nếu python-service không khả dụng.
     */
    public function seasonality(): array
    {
        $key = 'py:seasonality';

        $cached = Cache::get($key);
        if ($cached !== null) {
            return $cached;
        }

        $data = $this->get('/analytics/seasonality');
        if ($data === null) {
            return [];
        }

        $duLieu = $data['du_lieu'] ?? [];
        Cache::put($key, $duLieu, now()->addMinutes(60));

        return $duLieu;
    }

    private function get(string $path, array $query = []): ?array
    {
        $token = config('services.python.token');
        if (empty($token)) {
            Log::warning('PY_SERVICE_TOKEN chưa được cấu hình');
            return null;
        }

        try {
            $response = Http::timeout((int) config('services.python.timeout', 3))
                ->withHeaders(['X-Service-Token' => $token])
                ->acceptJson()
                ->get(config('services.python.url') . $path, $query);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('python-service trả lỗi HTTP', [
                'path' => $path,
                'status' => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('python-service không kết nối được', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}