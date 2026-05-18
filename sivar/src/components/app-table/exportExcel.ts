import dayjs from 'dayjs'
import { useToast } from 'vue-toastification'
import type { Column } from './models'
import 'dayjs/locale/es'
import { getExcel } from '@/utils'

const toast = useToast()

async function exportFile(name: string, colums: Array<Column>, rows: Array<any>) {
  try {
    if (rows.length === 0) {
      toast.error('No hay info para descargar')
      return
    }
    const fecha = dayjs().format('YYYYMMDDhmm')
    const fileName = `${name}_${fecha}`
    getExcel(fileName, rows)
  } catch (error) {
    console.error(error)
    toast.error('Ocurrio un error al generar el excel')
  }
}

const exportExcel = {
  exportFile
}

export default exportExcel
