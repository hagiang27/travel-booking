import pandas as pd


def recommend_similar(products: pd.DataFrame, ma_san_pham: str, top_n: int = 4) -> list[dict]:
    """Gợi ý sản phẩm tương tự: cùng loại, cùng điểm đến, giá gần, đánh giá cao."""
    if products.empty:
        return []

    target_rows = products[products["ma_san_pham"] == ma_san_pham]
    if target_rows.empty:
        return []
    target = target_rows.iloc[0]

    candidates = products[
        (products["ma_san_pham"] != ma_san_pham) & (products["trang_thai"] == "dang_ban")
    ].copy()
    if candidates.empty:
        return []

    gia_goc = max(float(target["gia_co_ban"]), 1.0)
    gia_gan = 1 - ((candidates["gia_co_ban"].astype(float) - float(target["gia_co_ban"])).abs() / gia_goc).clip(upper=1)

    candidates["diem"] = (
        (candidates["loai_san_pham"] == target["loai_san_pham"]).astype(float) * 3.0
        + (candidates["ma_diem_den"] == target["ma_diem_den"]).astype(float) * 2.0
        + gia_gan
        + candidates["diem_danh_gia_tb"].astype(float) / 5.0 * 0.5
    )

    top = candidates.sort_values(["diem", "ma_san_pham"], ascending=[False, True]).head(top_n)

    return [
        {
            "ma_san_pham": row["ma_san_pham"],
            "ten_san_pham": row["ten_san_pham"],
            "gia_co_ban": int(row["gia_co_ban"]),
            "diem": round(float(row["diem"]), 3),
        }
        for _, row in top.iterrows()
    ]
