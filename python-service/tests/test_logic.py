import pandas as pd

from app.analytics import fill_rate_by_month
from app.recommender import recommend_similar


def _san_pham():
    return pd.DataFrame([
        {"ma_san_pham": "SP0001", "ten_san_pham": "Tour A", "ma_diem_den": "DD001",
         "loai_san_pham": "tour", "gia_co_ban": 1000000, "diem_danh_gia_tb": 4.5, "trang_thai": "dang_ban"},
        {"ma_san_pham": "SP0002", "ten_san_pham": "Tour B cùng loại cùng điểm", "ma_diem_den": "DD001",
         "loai_san_pham": "tour", "gia_co_ban": 1100000, "diem_danh_gia_tb": 4.0, "trang_thai": "dang_ban"},
        {"ma_san_pham": "SP0003", "ten_san_pham": "Vé máy bay khác loại", "ma_diem_den": "DD002",
         "loai_san_pham": "ve_may_bay", "gia_co_ban": 1050000, "diem_danh_gia_tb": 5.0, "trang_thai": "dang_ban"},
        {"ma_san_pham": "SP0004", "ten_san_pham": "Tour đã ngừng bán", "ma_diem_den": "DD001",
         "loai_san_pham": "tour", "gia_co_ban": 1000000, "diem_danh_gia_tb": 5.0, "trang_thai": "ngung_ban"},
    ])


def test_recommend_excludes_self_and_stopped_and_ranks_same_category_first():
    result = recommend_similar(_san_pham(), "SP0001", top_n=5)
    ma = [r["ma_san_pham"] for r in result]

    assert "SP0001" not in ma
    assert "SP0004" not in ma
    assert ma[0] == "SP0002"


def test_recommend_unknown_product_returns_empty():
    assert recommend_similar(_san_pham(), "SP9999") == []


def test_fill_rate_by_month_computes_ratio():
    slots = pd.DataFrame([
        {"ngay_khoi_hanh": "2026-03-05", "tong_cho": 10, "da_dat": 5},
        {"ngay_khoi_hanh": "2026-03-20", "tong_cho": 10, "da_dat": 10},
        {"ngay_khoi_hanh": "2026-04-01", "tong_cho": 0, "da_dat": 0},
    ])
    result = {r["thang"]: r for r in fill_rate_by_month(slots)}

    assert result["2026-03"]["tong_cho"] == 20
    assert result["2026-03"]["da_dat"] == 15
    assert result["2026-03"]["ti_le_lap_day"] == 0.75
    assert result["2026-04"]["ti_le_lap_day"] == 0.0


def test_fill_rate_empty_input_returns_empty_list():
    assert fill_rate_by_month(pd.DataFrame()) == []
