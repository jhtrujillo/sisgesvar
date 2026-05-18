interface Headres {
  label: string
  field: string
  width: string
  sortable: boolean
  isKey: boolean
}

interface Column {
  keyName: string //if path, or keynames is use. consider this as a nickname. no need be related to the extracted/compose var
  text: string
  keyNames?: string[] //use this field for join vars of an object
  path?: string[] //use this field for access to a nested var, path must have the keyNames(to access to the variable)  sorted from the external[index=0] part to the internal part[index=last]
  isLastFatherList?: boolean // use when the father of a nested var is a list of objects
  formatValue?: (value: number | boolean | string) => string // format allow change/extract/modify information in the value. util for show dates, extract a substring of from the value, o show a  text if the value es null
  formatFromRow?: (row: any) => string
}
interface WildCardObject {
  [key: string]:
    | string
    | number
    | boolean
    | {
        [key: string]:
          | string
          | number
          | boolean
          | { [key: string]: string | number | boolean | Date }
      }
}
export type { Headres, Column, WildCardObject }
