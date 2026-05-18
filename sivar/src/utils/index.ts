import dayjs from "dayjs";
import urls from "@/services/urls";
const getExcel = (filename: string, data: Array<object> | undefined = undefined, htmlTableRef: HTMLTableElement | null = null) => {
  import("xlsx").then((xlsx) => {
    let workbook = undefined;
    if (data != undefined) {
      const worksheet = xlsx.utils.json_to_sheet(data);
      workbook = xlsx.utils.book_new();
      xlsx.utils.book_append_sheet(workbook, worksheet, "data");
    }
    if (htmlTableRef != undefined) {
      workbook = xlsx.utils.table_to_book(htmlTableRef);
    }
    if (workbook != undefined) {
      xlsx.writeFile(workbook, filename + ".xlsx");
    }
  });
};
const removeAccents = (str: string) => {
  return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
};

const filterObjectList = (list: Array<any>, keyWord: string) => {
  if (keyWord) {
    return list.filter((item) => {
      return removeAccents(JSON.stringify(item).toLocaleLowerCase()).indexOf(removeAccents(keyWord.toLocaleLowerCase())) > -1;
    });
  }
  return list;
};
const datetimeToString = (value: string) => {
  return value ? dayjs(value).format("YYYY-MM-DD hh:mm") : "no hay fecha";
};
const openUrl = (relativeUrl: string) => {
  const path = urls.API_URL + relativeUrl;
  window.open(path, "_blank");
};
export { getExcel, removeAccents, filterObjectList, datetimeToString, openUrl };
