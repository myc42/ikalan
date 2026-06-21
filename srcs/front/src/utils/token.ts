import * as SecureStore from 'expo-secure-store';

export const saveTokens = async (accessToken: string, refreshToken: string): Promise<void> => {
  try {
    await SecureStore.setItemAsync('accessToken', accessToken);
    await SecureStore.setItemAsync('refreshToken', refreshToken);
  } catch (error) {
    console.error("Erreur lors de la sauvegarde des tokens", error);
  }
};

export const getAccessToken = async (): Promise<string | null> => {
  return await SecureStore.getItemAsync('accessToken');
};

export const getRefreshToken = async (): Promise<string | null> => {
  return await SecureStore.getItemAsync('refreshToken');
};

export const clearTokens = async (): Promise<void> => {
  try {
    await SecureStore.deleteItemAsync('accessToken');
    await SecureStore.deleteItemAsync('refreshToken');
  } catch (error) {
    console.error("Erreur lors de la suppression des tokens", error);
  }
};