import { reactive, computed } from 'vue'
import {
  login as apiLogin,
  getCurrentUser,
  logout as apiLogout,
} from './api'

// Trạng thái đăng nhập dùng chung cho toàn ứng dụng.
// Nguồn sự thật là phiên trên máy chủ (cookie); ở đây chỉ giữ bản sao để hiển thị.
const state = reactive({ user: null, ready: false })

export const currentUser = computed(() => state.user)
export const isLoggedIn = computed(() => !!state.user)
export const userRole = computed(() => state.user?.vai_tro ?? null)
export const authReady = computed(() => state.ready)

/** Gọi một lần khi mở ứng dụng, để F5 vẫn giữ đăng nhập */
export async function initAuth() {
  try {
    state.user = await getCurrentUser()
  } catch {
    state.user = null
  } finally {
    state.ready = true
  }
}

export async function signIn(username, password) {
  await apiLogin(username, password)
  const user = await getCurrentUser()
  if (!user) {
    throw new Error('Đăng nhập không thành công. Vui lòng thử lại.')
  }
  state.user = user
  return user
}

export async function signOut() {
  try {
    await apiLogout()
  } finally {
    state.user = null
  }
}
