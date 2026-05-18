import axios from "axios";
import urls from "./urls";

// AUTH y parametrización de peticiones get, post, put, delete, head; con su respectiva lógica de errores
import { useUserStore } from "@/stores/user";

axios.defaults.headers.post["Content-Type"] = "application/json";
axios.defaults.xsrfHeaderName = "X-CSRFToken";
axios.defaults.xsrfCookieName = "csrftoken";

axios.interceptors.response.use(
  function (response) {
    return response;
  },
  async function (error) {
    const userStore = useUserStore();
    const originalRequest = error.config;

    if (error.response.status === 401 && (originalRequest.url.includes("refresh") || originalRequest.url.includes("login"))) {
      return Promise.reject(error);
    } else if (error.response.status === 401 && !originalRequest._retry) {
      try {
        originalRequest._retry = true;

        const result = await axios({
          method: "post",
          url: urls.API_AUTH_REFRESH_TOKEN,
          headers: {
            "Content-Type": "application/json",
            Authorization: "Bearer " + userStore.refresh
          }
        });
        if (result.status === 200) {
          userStore.token = result.data.access;
          originalRequest.headers.Authorization = "Bearer " + result.data.access;

          return axios(originalRequest);
        }
      } catch (_error: any) {
        if (_error.response && _error.response.data) {
          userStore.logout();
          return Promise.reject(_error.response.data);
        }

        return Promise.reject(_error);
      }
    }

    if (error.response.status === 403 && error.response.data) {
      return Promise.reject(error.response.data);
    }

    return Promise.reject(error);
  }
);

const api = {
  get,
  post,
  patch,
  put,
  head,
  delete: _delete,
  postWithImages,
  putFile,
  postForDownload
};

async function performAxios(url: string, request: unknown, method: string, secured: boolean | undefined) {
  const userStore = useUserStore();

  const headers = {
    "Content-Type": "application/json",
    Authorization: ""
  };

  if (secured) {
    const token = userStore.token;

    if (token) {
      headers.Authorization = "Bearer " + token;
    }
  }

  try {
    const response = await axios({
      method: method,
      url: url,
      data: request,
      headers: headers
    });
    return await Promise.resolve(response);
  } catch (error) {
    return await Promise.reject(error);
  }
}

function get(url: string, request: unknown, secured = true) {
  return performAxios(url, request, "get", secured);
}

function post(url: string, request: unknown, secured = true) {
  return performAxios(url, request, "post", secured);
}

function patch(url: string, request: unknown, secured = true) {
  return performAxios(url, request, "patch", secured);
}

function put(url: string, request: unknown, secured = true) {
  return performAxios(url, request, "put", secured);
}

function head(url: string, request: unknown, secured = true) {
  return performAxios(url, request, "head", secured);
}

function _delete(url: string, request: unknown, secured = true) {
  return performAxios(url, request, "delete", secured);
}

async function postWithImages(url: string, request: { _boundary: any }, secured = true) {
  const userStore = useUserStore();

  let headers = {
    "Content-Type": "application/json",
    Authorization: ""
  };

  if (secured) {
    const token = userStore.token;

    if (token) {
      headers = {
        Authorization: "Bearer " + token,
        "Content-Type": `multipart/form-data; boundary=${request._boundary}`
      };
    }
  }

  try {
    const response = await axios({
      method: "post",
      url: url,
      data: request,
      headers: headers
    });
    return [response, null];
  } catch (error) {
    return [null, error];
  }
}

async function postForDownload(url: string, request: { filename: string }, secured = true) {
  const userStore = useUserStore();

  let headers = {
    "Content-Type": "application/json",
    Authorization: ""
  };

  if (secured) {
    const token = userStore.token;

    if (token) {
      headers = {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json"
      };
    }
  }

  try {
    const response = await axios({
      method: "post",
      url: url,
      data: request,
      headers: headers,
      responseType: "blob"
    });
    const urlDownload = URL.createObjectURL(
      new Blob([response.data], {
        type: response.headers["content-type"]
      })
    );
    const link = document.createElement("a");
    link.href = urlDownload;

    const filename = request.filename + ".xlsx";

    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
  } catch (error) {
    return [null, error];
  }
}

async function putFile(url: string, request: any, secured = true) {
  let headers = {
    "Content-Type": "application/json",
    Authorization: ""
  };

  if (secured) {
    const userStore = useUserStore();
    const token = userStore.token;

    if (token) {
      headers = {
        Authorization: "Bearer " + token,
        "Content-Type": "multipart/form-data"
      };
    }
  }
  try {
    const response = await axios({
      method: "put",
      url: url,
      data: request,
      headers: headers
    });
    return [response, null];
  } catch (error) {
    return [null, error];
  }
}

export default api;
