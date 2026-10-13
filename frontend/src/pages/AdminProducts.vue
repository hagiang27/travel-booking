<template>
  <section class="quan-tri">
    <div class="quan-tri__tieu-de">
      <h1>Quản lý sản phẩm</h1>
      <button type="button" class="nut nut--chinh" @click="moForm()">
        Thêm sản phẩm
      </button>
    </div>

    <p v-if="thongBao" class="thong-bao" role="status">{{ thongBao }}</p>

    <p v-if="dangTai" role="status">Đang tải danh sách…</p>

    <div v-else-if="loi" class="thong-bao thong-bao--loi" role="alert">
      {{ loi }}
      <button type="button" class="nut" @click="taiDanhSach">Thử lại</button>
    </div>

    <p v-else-if="danhSach.length === 0">Chưa có sản phẩm nào.</p>

    <div v-else class="bang-bao">
      <table class="bang">
        <caption>Danh sách sản phẩm ({{ danhSach.length }})</caption>
        <thead>
          <tr>
            <th scope="col">Mã</th>
            <th scope="col">Tên sản phẩm</th>
            <th scope="col">Loại</th>
            <th scope="col">Giá cơ bản</th>
            <th scope="col"><span class="sr-only">Thao tác</span></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sp in danhSach" :key="sp.ma_san_pham">
            <td>{{ sp.ma_san_pham }}</td>
            <td>{{ sp.ten_san_pham }}</td>
            <td>{{ tenLoai(sp.loai_san_pham) }}</td>
            <td>{{ dinhDangGia(sp.gia_co_ban) }}</td>
            <td class="hanh-dong">
              <button type="button" class="nut" @click="moForm(sp)">
                Sửa<span class="sr-only"> {{ sp.ten_san_pham }}</span>
              </button>
              <button type="button" class="nut nut--nguy" @click="xoa(sp)">
                Xóa<span class="sr-only"> {{ sp.ten_san_pham }}</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <form v-if="dangMoForm" class="form" novalidate @submit.prevent="luu">
      <h2>{{ maDangSua ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}</h2>

      <div class="o-nhap">
        <label for="ten">Tên sản phẩm</label>
        <input
          id="ten"
          v-model="form.ten_san_pham"
          type="text"
          maxlength="200"
          :aria-invalid="!!loiForm.ten_san_pham"
          aria-describedby="loi-ten"
        />
        <p v-if="loiForm.ten_san_pham" id="loi-ten" class="loi" role="alert">
          {{ loiForm.ten_san_pham }}
        </p>
      </div>

      <div class="o-nhap">
        <label for="loai">Loại sản phẩm</label>
        <select id="loai" v-model="form.loai_san_pham">
          <option value="tour">Tour</option>
          <option value="stay">Lưu trú</option>
          <option value="transfer">Đưa đón</option>
          <option value="ticket">Vé tham quan</option>
          <option value="experience">Trải nghiệm</option>
        </select>
      </div>

      <div class="o-nhap">
        <label for="gia">Giá cơ bản (VNĐ)</label>
        <input
          id="gia"
          v-model="form.gia_co_ban"
          type="number"
          min="0"
          step="1000"
          :aria-invalid="!!loiForm.gia_co_ban"
          aria-describedby="loi-gia"
        />
        <p v-if="loiForm.gia_co_ban" id="loi-gia" class="loi" role="alert">
          {{ loiForm.gia_co_ban }}
        </p>
      </div>

      <div class="o-nhap">
        <label for="huy">Chính sách hủy</label>
        <textarea id="huy" v-model="form.chinh_sach_huy" rows="3" maxlength="255"></textarea>
      </div>

      <div class="form__hanh-dong">
        <button type="submit" class="nut nut--chinh" :disabled="dangLuu">
          {{ dangLuu ? 'Đang lưu…' : 'Lưu' }}
        </button>
        <button type="button" class="nut" @click="dangMoForm = false">Hủy</button>
      </div>
    </form>
  </section>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { productService } from '../lib/productService'

const danhSach = ref([])
const dangTai = ref(false)
const dangLuu = ref(false)
const dangMoForm = ref(false)
const maDangSua = ref(null)
const loi = ref('')
const thongBao = ref('')

const form = reactive({
  ten_san_pham: '',
  gia_co_ban: '',
  loai_san_pham: 'tour',
  chinh_sach_huy: '',
})
const loiForm = reactive({})

const NHAN_LOAI = {
  tour: 'Tour',
  stay: 'Lưu trú',
  transfer: 'Đưa đón',
  ticket: 'Vé tham quan',
  experience: 'Trải nghiệm',
}

function tenLoai(ma) {
  return NHAN_LOAI[ma] ?? ma
}

function dinhDangGia(so) {
  return `${Number(so).toLocaleString('vi-VN')} ₫`
}

function xoaLoi() {
  Object.keys(loiForm).forEach((k) => delete loiForm[k])
}

function kiemTra() {
  const ket = {}
  const ten = form.ten_san_pham.trim()
  if (!ten) ket.ten_san_pham = 'Vui lòng nhập tên sản phẩm.'
  else if (ten.length > 200) ket.ten_san_pham = 'Tên tối đa 200 ký tự.'

  if (form.gia_co_ban === '' || Number.isNaN(Number(form.gia_co_ban))) {
    ket.gia_co_ban = 'Giá phải là một số.'
  } else if (Number(form.gia_co_ban) < 0) {
    ket.gia_co_ban = 'Giá không được âm.'
  }
  return ket
}

async function taiDanhSach() {
  dangTai.value = true
  loi.value = ''
  try {
    const ket = await productService.danhSach()
    danhSach.value = ket.data
  } catch (e) {
    loi.value = e.message || 'Có lỗi xảy ra.'
  } finally {
    dangTai.value = false
  }
}

function moForm(sp = null) {
  thongBao.value = ''
  xoaLoi()
  if (sp) {
    maDangSua.value = sp.ma_san_pham
    Object.assign(form, {
      ten_san_pham: sp.ten_san_pham,
      gia_co_ban: sp.gia_co_ban,
      loai_san_pham: sp.loai_san_pham ?? 'tour',
      chinh_sach_huy: sp.chinh_sach_huy ?? '',
    })
  } else {
    maDangSua.value = null
    Object.assign(form, {
      ten_san_pham: '',
      gia_co_ban: '',
      loai_san_pham: 'tour',
      chinh_sach_huy: '',
    })
  }
  dangMoForm.value = true
}

async function luu() {
  xoaLoi()
  Object.assign(loiForm, kiemTra())
  if (Object.keys(loiForm).length > 0) return

  const duLieu = {
    ten_san_pham: form.ten_san_pham.trim(),
    gia_co_ban: Number(form.gia_co_ban),
    loai_san_pham: form.loai_san_pham,
    chinh_sach_huy: form.chinh_sach_huy.trim(),
  }

  dangLuu.value = true
  try {
    if (maDangSua.value) {
      await productService.capNhat(maDangSua.value, duLieu)
      thongBao.value = 'Đã cập nhật sản phẩm.'
    } else {
      await productService.tao(duLieu)
      thongBao.value = 'Đã thêm sản phẩm.'
    }
    dangMoForm.value = false
    await taiDanhSach()
  } catch (e) {
    thongBao.value = e.message
  } finally {
    dangLuu.value = false
  }
}

async function xoa(sp) {
  if (!window.confirm(`Xóa sản phẩm "${sp.ten_san_pham}"?`)) return
  try {
    await productService.xoa(sp.ma_san_pham)
    thongBao.value = 'Đã xóa sản phẩm.'
    await taiDanhSach()
  } catch (e) {
    thongBao.value = e.message
  }
}

onMounted(taiDanhSach)
</script>

<style scoped>
.quan-tri {
  padding: var(--space-6) var(--space-4);
  max-width: 1100px;
  margin: 0 auto;
}

.quan-tri__tieu-de {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: var(--space-3);
  margin-bottom: var(--space-4);
}

.quan-tri__tieu-de h1 {
  margin: 0;
  font-size: var(--font-size-h2);
}

.bang-bao {
  overflow-x: auto;
}

.bang {
  width: 100%;
  border-collapse: collapse;
  font-size: var(--font-size-small);
}

.bang th,
.bang td {
  padding: var(--space-3);
  border-bottom: 1px solid var(--color-border);
  text-align: left;
  vertical-align: middle;
}

.bang caption {
  text-align: left;
  padding-bottom: var(--space-2);
  color: var(--color-text-secondary);
}

.hanh-dong {
  white-space: nowrap;
}

.hanh-dong .nut + .nut {
  margin-left: var(--space-2);
}

.form {
  margin-top: var(--space-6);
  padding: var(--space-6);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  background: var(--color-surface);
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
  max-width: 560px;
}

.form h2 {
  margin: 0;
  font-size: var(--font-size-h3);
}

.o-nhap {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.o-nhap label {
  font-size: var(--font-size-small);
  font-weight: var(--font-weight-semibold);
}

.o-nhap input,
.o-nhap select,
.o-nhap textarea {
  min-height: 44px;
  padding: var(--space-2) var(--space-3);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-bg);
  color: var(--color-text);
  font: inherit;
}

.o-nhap input[aria-invalid='true'] {
  border-color: var(--color-danger, var(--color-primary));
}

.loi {
  margin: 0;
  font-size: var(--font-size-small);
  color: var(--color-danger, var(--color-primary));
}

.form__hanh-dong {
  display: flex;
  gap: var(--space-2);
}

.thong-bao {
  padding: var(--space-3);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-surface-2);
  font-size: var(--font-size-small);
}

.thong-bao--loi {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-3);
}

.nut {
  min-height: 40px;
  padding: 0 var(--space-4);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-surface);
  color: var(--color-text);
  font-size: var(--font-size-small);
  cursor: pointer;
}

.nut--chinh {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: var(--color-bg);
}

.nut--nguy {
  color: var(--color-danger, var(--color-primary));
}

.nut:focus-visible,
.o-nhap input:focus-visible,
.o-nhap select:focus-visible,
.o-nhap textarea:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.nut:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  overflow: hidden;
  clip-path: inset(50%);
  white-space: nowrap;
}

@media (max-width: 480px) {
  .quan-tri {
    padding: var(--space-4) var(--space-3);
  }

  .form {
    padding: var(--space-4);
  }
}
</style>