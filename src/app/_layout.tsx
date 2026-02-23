import { DarkTheme, DefaultTheme, ThemeProvider } from '@react-navigation/native';
import { Stack } from 'expo-router';
import * as SplashScreen from 'expo-splash-screen';
import { StatusBar } from 'expo-status-bar';
import { useEffect, useState } from 'react';
import 'react-native-reanimated';

import CustomSplashScreen from '@/components/common/CustomSplashScreen';
import OnboardingScreen from '@/features/onboarding/screens/OnboardingScreen';
import WelcomeScreen from '@/features/onboarding/screens/WelcomeScreen';
import { useColorScheme } from '@/hooks/use-color-scheme';

// Prevent the splash screen from auto-hiding before asset loading is complete.
SplashScreen.preventAutoHideAsync();

export const unstable_settings = {
  anchor: 'index',
};

export default function RootLayout() {
  const colorScheme = useColorScheme();
  const [appIsReady, setAppIsReady] = useState(false);
  const [showCustomSplash, setShowCustomSplash] = useState(true);
  const [showWelcome, setShowWelcome] = useState(false);
  const [showOnboarding, setShowOnboarding] = useState(false);
  const [showGenderGoal, setShowGenderGoal] = useState(false);

  useEffect(() => {
    async function prepare() {
      try {
        // Pre-load fonts or other assets here
        await new Promise(resolve => setTimeout(resolve, 2000));
      } catch (e) {
        console.warn(e);
      } finally {
        setAppIsReady(true);
      }
    }

    prepare();
  }, []);

  useEffect(() => {
    if (appIsReady) {
      // Hide the native splash screen
      SplashScreen.hideAsync();

      // Keep custom splash for 1.5 seconds, then show Welcome Screen
      const splashTimer = setTimeout(() => {
        setShowCustomSplash(false);
        setShowWelcome(true);
      }, 1500);

      return () => clearTimeout(splashTimer);
    }
  }, [appIsReady]);

  useEffect(() => {
    if (showWelcome) {
      // Show Welcome Screen for 4 seconds
      const welcomeTimer = setTimeout(() => {
        setShowWelcome(false);
        setShowOnboarding(true);
      }, 4000);

      return () => clearTimeout(welcomeTimer);
    }
  }, [showWelcome]);

  if (showCustomSplash) {
    return <CustomSplashScreen />;
  }

  if (showWelcome) {
    return <WelcomeScreen />;
  }

  if (showOnboarding) {
    return <OnboardingScreen />;
  }



  return (
    <ThemeProvider value={colorScheme === 'dark' ? DarkTheme : DefaultTheme}>
      <Stack>
        <Stack.Screen name="index" options={{ headerShown: false }} />
        <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
        <Stack.Screen name="modal" options={{ presentation: 'modal', title: 'Modal' }} />
        <Stack.Screen name="(goal)" options={{ headerShown: false }} />
      </Stack>
      <StatusBar style="auto" />
    </ThemeProvider>
  );
}
