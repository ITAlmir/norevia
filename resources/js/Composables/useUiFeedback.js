import { ref } from 'vue'

const confirmState = ref({
  open: false,
  title: '',
  message: '',
  confirmLabel: 'Confirm',
  cancelLabel: 'Cancel',
  danger: false,
  action: null,
})

const toastState = ref({
  open: false,
  message: '',
  type: 'success', // success | error | info
})

let toastTimer = null

export function useUiFeedback() {
  const openConfirm = ({
    title,
    message,
    confirmLabel = 'Confirm',
    cancelLabel = 'Cancel',
    danger = false,
    action = null,
  }) => {
    confirmState.value = {
      open: true,
      title,
      message,
      confirmLabel,
      cancelLabel,
      danger,
      action,
    }
  }

  const closeConfirm = () => {
    confirmState.value.open = false
    confirmState.value.action = null
  }

  const confirmYes = async () => {
    try {
      if (typeof confirmState.value.action === 'function') {
        await confirmState.value.action()
      }
    } finally {
      closeConfirm()
    }
  }

  const showToast = (message, type = 'success', duration = 2200) => {
    toastState.value = {
      open: true,
      message,
      type,
    }

    if (toastTimer) {
      clearTimeout(toastTimer)
    }

    toastTimer = setTimeout(() => {
      toastState.value.open = false
    }, duration)
  }

  const closeToast = () => {
    toastState.value.open = false

    if (toastTimer) {
      clearTimeout(toastTimer)
      toastTimer = null
    }
  }

  return {
    confirmState,
    toastState,
    openConfirm,
    closeConfirm,
    confirmYes,
    showToast,
    closeToast,
  }
}