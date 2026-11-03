import pandas as pd
from fastapi.testclient import TestClient

import app.main as main

client = TestClient(main.app)


def _san_pham_mau():
    return pd.DataFrame([
        {"ma_san_pham": "SP0001", "ten_san_pham": "Tour A", "ma_diem_den": "DD001",
         "loai_san_pham": "tour", "gia_co_ban": 1000000, "diem_danh_gia_tb": 4.5, "trang_thai": "dang_ban"},
        {"ma_san_pham": "SP0002", "ten_san_pham": "Tour B", "ma_diem_den": "DD001",
         "loai_san_pham": "tour", "gia_co_ban": 1100000, "diem_danh_gia_tb": 4.0, "trang_thai": "dang_ban"},
    ])


def test_request_without_token_is_rejected(monkeypatch):
    monkeypatch.setenv("PY_SERVICE_TOKEN", "abc123")
    r = client.get("/recommend/SP0001")
    assert r.status_code == 401


def test_recommend_with_valid_token_returns_suggestions(monkeypatch):
    monkeypatch.setenv("PY_SERVICE_TOKEN", "abc123")
    monkeypatch.setattr(main, "load_san_pham", _san_pham_mau)

    r = client.get("/recommend/SP0001", headers={"X-Service-Token": "abc123"})

    assert r.status_code == 200
    assert r.json()["goi_y"][0]["ma_san_pham"] == "SP0002"
