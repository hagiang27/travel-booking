import os

import pandas as pd
from dotenv import load_dotenv
from sqlalchemy import create_engine
from sqlalchemy.engine import URL

load_dotenv()

_engine = None


def _connect_args():
    ca = os.getenv("DB_SSL_CA")
    return {"ssl": {"ca": ca}} if ca else {}


def _get_engine():
    global _engine
    if _engine is None:
        url = URL.create(
            "mysql+pymysql",
            username=os.getenv("DB_USERNAME", "root"),
            password=os.getenv("DB_PASSWORD", ""),
            host=os.getenv("DB_HOST", "127.0.0.1"),
            port=int(os.getenv("DB_PORT", "3306")),
            database=os.getenv("DB_DATABASE", "travel_booking_dt02"),
            query={"charset": "utf8mb4"},
        )
        _engine = create_engine(
            url, pool_pre_ping=True, connect_args=_connect_args()
        )
    return _engine


def load_san_pham() -> pd.DataFrame:
    sql = """
        SELECT ma_san_pham, ten_san_pham, ma_diem_den, loai_san_pham,
               gia_co_ban, diem_danh_gia_tb, trang_thai
        FROM san_pham
    """
    return pd.read_sql(sql, _get_engine())


def load_lich_kha_dung() -> pd.DataFrame:
    sql = "SELECT ngay_khoi_hanh, tong_cho, da_dat FROM lich_kha_dung"
    return pd.read_sql(sql, _get_engine())