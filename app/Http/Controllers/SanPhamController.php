<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SanPhamController extends Controller
{
    private function nguoiDungDangNhap(Request $request): ?object
    {
        $maNguoiDung = $request->session()->get('ma_nguoi_dung');

        if (!$maNguoiDung) {
            return null;
        }

        return DB::table('nguoi_dung')
            ->where('ma_nguoi_dung', $maNguoiDung)
            ->where('trang_thai', 'kich_hoat')
            ->first();
    }

    private function kiemTraQuyen(Request $request): object
    {
        $nguoiDung = $this->nguoiDungDangNhap($request);

        if (!$nguoiDung) {
            abort(401, 'Vui long dang nhap.');
        }

        if (!in_array($nguoiDung->vai_tro, ['quan_tri', 'nha_cung_cap'], true)) {
            abort(403, 'Ban khong co quyen quan ly san pham.');
        }

        if ($nguoiDung->vai_tro === 'nha_cung_cap' && empty($nguoiDung->ma_ncc)) {
            abort(403, 'Tai khoan chua duoc gan nha cung cap.');
        }

        return $nguoiDung;
    }

    private function truyVanSanPham(object $nguoiDung)
    {
        $query = SanPham::query();

        if ($nguoiDung->vai_tro === 'nha_cung_cap') {
            $query->where('ma_ncc', $nguoiDung->ma_ncc);
        }

        return $query;
    }

    /**
     * GET /api/v1/products
     * Danh sach san pham cong khai, co tim kiem va loc.
     */
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:120',
            'tinh_thanh' => 'nullable|string|max:80',
            'loai_san_pham' => 'nullable|string|max:50',
            'gia_min' => 'nullable|numeric|min:0',
            'gia_max' => 'nullable|numeric|min:0|gte:gia_min',
            'sort' => 'nullable|in:gia_tang,gia_giam,moi_nhat,danh_gia',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $query = SanPham::query()
            ->where('trang_thai', 'dang_ban');

        if ($request->filled('q')) {
            $query->where('ten_san_pham', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('tinh_thanh')) {
            $tinhThanh = mb_strtolower(trim($request->tinh_thanh));

            $query->whereHas('diemDen', function ($q) use ($tinhThanh) {
                $q->whereRaw(
                    'LOWER(duong_dan_tinh) LIKE ?',
                    ['%' . str_replace(' ', '-', $tinhThanh) . '%']
                );
            });
        }

        if ($request->filled('loai_san_pham')) {
            $query->where('loai_san_pham', $request->loai_san_pham);
        }

        if ($request->filled('gia_min')) {
            $query->where('gia_co_ban', '>=', $request->gia_min);
        }

        if ($request->filled('gia_max')) {
            $query->where('gia_co_ban', '<=', $request->gia_max);
        }

        switch ($request->input('sort')) {
            case 'gia_tang':
                $query->orderBy('gia_co_ban', 'asc');
                break;
            case 'gia_giam':
                $query->orderBy('gia_co_ban', 'desc');
                break;
            case 'moi_nhat':
                $query->orderBy('ngay_tao', 'desc');
                break;
            case 'danh_gia':
                $query->orderBy('diem_danh_gia_tb', 'desc');
                break;
            default:
                $query->orderBy('ma_san_pham');
                break;
        }

        $perPage = (int) $request->input('per_page', 10);

        return response()->json(
            $query->with('diemDen')->paginate($perPage)->withQueryString()
        );
    }

    /**
     * GET /api/v1/products/{ma_san_pham}
     * Chi tiet san pham dang ban va lich khoi hanh kha dung.
     */
    public function show(string $ma_san_pham)
    {
        $product = SanPham::with([
            'diemDen',
            'lichKhaDung' => function ($query) {
                $query->whereDate('ngay_khoi_hanh', '>=', now()->toDateString())
                    ->where('trang_thai', 'mo_ban')
                    ->where('con_lai', '>', 0)
                    ->orderBy('ngay_khoi_hanh', 'asc');
            },
        ])
            ->where('ma_san_pham', $ma_san_pham)
            ->where('trang_thai', 'dang_ban')
            ->first();

        if (!$product) {
            return response()->json([
                'message' => 'Khong tim thay san pham.',
            ], 404);
        }

        return response()->json($product);
    }

    /**
     * GET /api/v1/san-pham
     * Danh sach san pham trong pham vi quan ly.
     */
    public function quanLyIndex(Request $request)
    {
        $nguoiDung = $this->kiemTraQuyen($request);

        return response()->json(
            $this->truyVanSanPham($nguoiDung)
                ->orderBy('ma_san_pham')
                ->paginate(20)
        );
    }

    public function quanLyShow(Request $request, string $ma_san_pham)
    {
        $nguoiDung = $this->kiemTraQuyen($request);

        $sanPham = $this->truyVanSanPham($nguoiDung)
            ->where('ma_san_pham', $ma_san_pham)
            ->firstOrFail();

        return response()->json($sanPham);
    }

    public function store(Request $request)
    {
        $nguoiDung = $this->kiemTraQuyen($request);

        $validator = Validator::make($request->all(), [
            'ten_san_pham' => ['required', 'string', 'max:200'],
            'duong_dan_tinh' => [
                'required', 'string', 'max:200',
                Rule::unique('san_pham', 'duong_dan_tinh'),
            ],
            'ma_ncc' => ['required', 'string', 'size:6', 'exists:nha_cung_cap,ma_ncc'],
            'ma_diem_den' => ['required', 'string', 'size:5', 'exists:diem_den,ma_diem_den'],
            'loai_san_pham' => ['required', 'string', 'max:50'],
            'gia_co_ban' => ['required', 'integer', 'min:0'],
            'don_vi_tinh' => ['required', 'string', 'max:20'],
            'so_ngay' => ['required', 'integer', 'min:1'],
            'suc_chua_toi_da' => ['required', 'integer', 'min:1'],
            'chinh_sach_huy' => ['nullable', 'string', 'max:120'],
            'diem_danh_gia_tb' => ['nullable', 'numeric', 'between:0,5'],
            'so_luot_danh_gia' => ['nullable', 'integer', 'min:0'],
            'trang_thai' => ['required', Rule::in(['dang_ban', 'ngung_ban'])],
            'ngay_tao' => ['required', 'date_format:Y-m-d'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Du lieu khong hop le.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $duLieu = $validator->validated();

        if ($nguoiDung->vai_tro === 'nha_cung_cap') {
            $duLieu['ma_ncc'] = $nguoiDung->ma_ncc;
        }

        $soThuTuLonNhat = DB::table('san_pham')
            ->whereRaw("ma_san_pham REGEXP '^SP[0-9]{4}$'")
            ->selectRaw(
                'MAX(CAST(SUBSTRING(ma_san_pham, 3) AS UNSIGNED)) AS so_lon_nhat'
            )
            ->value('so_lon_nhat');

        $soThuTu = (int) $soThuTuLonNhat + 1;
        $maSanPham = 'SP' . str_pad((string) $soThuTu, 4, '0', STR_PAD_LEFT);

        if (strlen($maSanPham) > 6 ||
            DB::table('san_pham')->where('ma_san_pham', $maSanPham)->exists()) {
            return response()->json([
                'message' => 'Khong the tao ma san pham tu dong.',
            ], 409);
        }

        $duLieu['ma_san_pham'] = $maSanPham;
        $duLieu['diem_danh_gia_tb'] = $duLieu['diem_danh_gia_tb'] ?? 0;
        $duLieu['so_luot_danh_gia'] = $duLieu['so_luot_danh_gia'] ?? 0;

        $sanPham = SanPham::create($duLieu);

        return response()->json([
            'message' => 'Tao san pham thanh cong.',
            'data' => $sanPham,
        ], 201);
    }

    public function update(Request $request, string $ma_san_pham)
    {
        $nguoiDung = $this->kiemTraQuyen($request);

        $sanPham = $this->truyVanSanPham($nguoiDung)
            ->where('ma_san_pham', $ma_san_pham)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'ten_san_pham' => ['sometimes', 'required', 'string', 'max:200'],
            'duong_dan_tinh' => [
                'sometimes', 'required', 'string', 'max:200',
                Rule::unique('san_pham', 'duong_dan_tinh')
                    ->ignore($sanPham->ma_san_pham, 'ma_san_pham'),
            ],
            'ma_ncc' => ['sometimes', 'required', 'string', 'size:6', 'exists:nha_cung_cap,ma_ncc'],
            'ma_diem_den' => ['sometimes', 'required', 'string', 'size:5', 'exists:diem_den,ma_diem_den'],
            'loai_san_pham' => ['sometimes', 'required', 'string', 'max:50'],
            'gia_co_ban' => ['sometimes', 'required', 'integer', 'min:0'],
            'don_vi_tinh' => ['sometimes', 'required', 'string', 'max:20'],
            'so_ngay' => ['sometimes', 'required', 'integer', 'min:1'],
            'suc_chua_toi_da' => ['sometimes', 'required', 'integer', 'min:1'],
            'chinh_sach_huy' => ['sometimes', 'nullable', 'string', 'max:120'],
            'trang_thai' => ['sometimes', 'required', Rule::in(['dang_ban', 'ngung_ban'])],
            'ngay_tao' => ['sometimes', 'required', 'date_format:Y-m-d'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Du lieu khong hop le.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $duLieu = $validator->validated();

        unset($duLieu['diem_danh_gia_tb'], $duLieu['so_luot_danh_gia']);

        if ($nguoiDung->vai_tro === 'nha_cung_cap') {
            unset($duLieu['ma_ncc']);
        }

        $sanPham->fill($duLieu);
        $sanPham->save();

        return response()->json([
            'message' => 'Cap nhat san pham thanh cong.',
            'data' => $sanPham->fresh(),
        ]);
    }

    public function destroy(Request $request, string $ma_san_pham)
    {
        $nguoiDung = $this->kiemTraQuyen($request);

        $sanPham = $this->truyVanSanPham($nguoiDung)
            ->where('ma_san_pham', $ma_san_pham)
            ->firstOrFail();

        $daCoDonDat = DB::table('chi_tiet_don')
            ->where('ma_san_pham', $sanPham->ma_san_pham)
            ->exists();

        if ($daCoDonDat) {
            return response()->json([
                'message' => 'San pham da co trong don dat, khong the xoa. Hay chuyen trang thai sang ngung_ban.',
            ], 409);
        }

        $sanPham->delete();

        return response()->json([
            'message' => 'Xoa san pham thanh cong.',
        ]);
    }
}
