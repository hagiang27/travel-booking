import os

from fastapi import FastAPI, Header, HTTPException

from app.analytics import fill_rate_by_month
from app.db import load_lich_kha_dung, load_san_pham
from app.recommender import recommend_similar

app = FastAPI(title="Travel Data Service")


def _check_token(token: str | None) -> None:
    expected = os.getenv("PY_SERVICE_TOKEN", "")
    if not expected or token != expected:
        raise HTTPException(status_code=401, detail="invalid service token")


@app.get("/healthz")
def healthz():
    return {"status": "ok"}


@app.get("/recommend/{ma_san_pham}")
def recommend(
    ma_san_pham: str,
    top_n: int = 4,
    x_service_token: str | None = Header(default=None),
):
    _check_token(x_service_token)
    goi_y = recommend_similar(load_san_pham(), ma_san_pham, top_n)
    return {"ma_san_pham": ma_san_pham, "goi_y": goi_y}


@app.get("/analytics/seasonality")
def seasonality(x_service_token: str | None = Header(default=None)):
    _check_token(x_service_token)
    return {"du_lieu": fill_rate_by_month(load_lich_kha_dung())}