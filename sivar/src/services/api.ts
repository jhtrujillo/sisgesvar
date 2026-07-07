import axios from "axios";
import urls from "./urls";

// AUTH y parametrización de peticiones get, post, put, delete, head; con su respectiva lógica de errores
import { useUserStore } from "@/stores/user";

// Note: Do NOT set a global Content-Type default for POST — it breaks multipart/form-data (FormData) uploads.
// Each function sets its own Content-Type as needed.
// (B-1) Se elimina la config CSRF estilo Django (xsrfCookieName "csrftoken"): este backend
// es Laravel con JWT por cabecera Authorization; no aplica y confundía el modelo de auth.

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
  postForDownload,
  getForDownload
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
    const config: any = {
      method: method,
      url: url,
      headers: headers
    };

    // GET requests need params (query string), other methods need data (body)
    if (method.toLowerCase() === 'get') {
      config.params = (request as any)?.params || request;
    } else {
      config.data = request;
    }

    const response = await axios(config);
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

async function postWithImages(url: string, request: any, secured = true) {
  const userStore = useUserStore();

  // Start with no Content-Type — Axios will auto-set multipart/form-data + boundary for FormData
  const headers: Record<string, any> = {
    "Content-Type": undefined  // Explicitly unset so Axios/browser generates correct multipart boundary
  };

  if (secured) {
    const token = userStore.token;
    if (token) {
      headers["Authorization"] = "Bearer " + token;
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

// Descarga por GET autenticada por cabecera (M-3): evita poner el JWT en la URL.
async function getForDownload(url: string, filename: string, secured = true) {
  const userStore = useUserStore();
  const headers: Record<string, string> = {};
  if (secured && userStore.token) {
    headers.Authorization = "Bearer " + userStore.token;
  }
  const response = await axios({ method: "get", url, headers, responseType: "blob" });
  const objUrl = URL.createObjectURL(
    new Blob([response.data], { type: response.headers["content-type"] })
  );
  const link = document.createElement("a");
  link.href = objUrl;
  link.setAttribute("download", filename);
  document.body.appendChild(link);
  link.click();
  link.remove();
  URL.revokeObjectURL(objUrl);
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
