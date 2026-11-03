import pandas as pd


def fill_rate_by_month(slots: pd.DataFrame) -> list[dict]:
    """Tỷ lệ lấp đầy chỗ theo tháng khởi hành, từ bảng lich_kha_dung."""
    if slots.empty:
        return []

    df = slots.copy()
    df["ngay_khoi_hanh"] = pd.to_datetime(df["ngay_khoi_hanh"])
    df["thang"] = df["ngay_khoi_hanh"].dt.strftime("%Y-%m")

    g = df.groupby("thang")[["tong_cho", "da_dat"]].sum().reset_index()
    g["ti_le_lap_day"] = (g["da_dat"] / g["tong_cho"].where(g["tong_cho"] > 0)).fillna(0).round(4)

    return g[["thang", "tong_cho", "da_dat", "ti_le_lap_day"]].to_dict(orient="records")
