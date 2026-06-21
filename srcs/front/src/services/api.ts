import axios, { AxiosError, InternalAxiosRequestConfig } from 'axios';
import { getAccessToken, getRefreshToken, saveTokens, clearTokens } from '../utils/token';
import { DeviceEventEmitter } from 'react-native';

const API_URL = `http://${process.env.EXPO_PUBLIC_API_URL}:8000/api`;

// 1. Création d'une interface personnalisée pour étendre la config d'Axios
interface CustomAxiosRequestConfig extends InternalAxiosRequestConfig {
  _retry?: boolean;
}

// 2. Typage de la réponse attendue lors du refresh
interface RefreshTokenResponse {
  token: string;
  refresh_token: string;
}

// 3. Variables pour gérer la file d'attente du refresh token
let isRefreshing = false;
let failedQueue: Array<{
  resolve: (token: string) => void;
  reject: (error: any) => void;
}> = [];

// Fonction pour traiter les requêtes en attente une fois le token rafraîchi (ou en cas d'échec)
const processQueue = (error: any, token: string | null = null) => {
  failedQueue.forEach((prom) => {
    if (error) {
      prom.reject(error);
    } else {
      prom.resolve(token as string);
    }
  });
  // On vide la file d'attente
  failedQueue = [];
};

export const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Intercepteur de requête
api.interceptors.request.use(
  async (config: InternalAxiosRequestConfig) => {
    const token = await getAccessToken();
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error: AxiosError) => Promise.reject(error)
);

// Intercepteur de réponse
api.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    const originalRequest = error.config as CustomAxiosRequestConfig;

    if (error.response?.status === 401 && originalRequest && !originalRequest._retry) {
      
      // Si un rafraîchissement est DÉJÀ en cours, on met la requête en pause dans la file d'attente
      if (isRefreshing) {
        return new Promise((resolve, reject) => {
          failedQueue.push({
            resolve: (token: string) => {
              if (originalRequest.headers) {
                originalRequest.headers.Authorization = `Bearer ${token}`;
              }
              resolve(api(originalRequest));
            },
            reject: (err: any) => {
              reject(err);
            },
          });
        });
      }

      // S'il n'y a pas de rafraîchissement en cours, on verrouille et on lance la procédure
      originalRequest._retry = true;
      isRefreshing = true;

      try {
        const refreshToken = await getRefreshToken();

        if (!refreshToken) {
          throw new Error("Aucun refresh token disponible");
        }

        const response = await axios.post<RefreshTokenResponse>(`${API_URL}/token/refresh`, {
          refresh_token: refreshToken,
        });

        const newAccessToken = response.data.token;
        const newRefreshToken = response.data.refresh_token;

        await saveTokens(newAccessToken, newRefreshToken);

        // On met à jour le header de la requête initiale ayant échoué
        if (originalRequest.headers) {
          originalRequest.headers.Authorization = `Bearer ${newAccessToken}`;
        }

        // On libère la file d'attente en passant le nouveau token
        processQueue(null, newAccessToken);

        // On relance la requête initiale
        return api(originalRequest);
        
      } catch (refreshError) {
        console.error("Échec du rafraîchissement du token :", refreshError);
        
        // On rejette toutes les requêtes en attente avec l'erreur
        processQueue(refreshError, null);
        await clearTokens();
        DeviceEventEmitter.emit('onSessionExpired');
        // Redirection vers le login à gérer ici (via Context, ou navigation globale)
        return Promise.reject(refreshError);
        
      } finally {
        // Peu importe si ça a réussi ou échoué, on déverrouille le flag
        isRefreshing = false;
      }
    }

    return Promise.reject(error);
  }
);