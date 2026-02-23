import { useColorScheme } from '@/hooks/use-color-scheme';
import { DarkTheme, DefaultTheme, ThemeProvider } from '@react-navigation/native';
import { Stack } from 'expo-router';
import { StatusBar } from 'expo-status-bar';

export default function GoalLayout() {
    const colorScheme = useColorScheme();

    return (
        <ThemeProvider value={colorScheme === 'dark' ? DarkTheme : DefaultTheme}>
            <Stack screenOptions={{
                headerShown: false,
                animation: 'slide_from_right',
                headerTintColor: '#E67E22',
                headerBackTitle: 'Retour',
            }}>
                <Stack.Screen
                    name="gender"
                    options={{
                        title: 'Genre',
                    }}
                />
                <Stack.Screen
                    name="weight"
                    options={{
                        title: 'Poids',
                    }}
                />
                <Stack.Screen
                    name="height"
                    options={{
                        title: 'Taille',
                    }}
                />

            </Stack>
            <StatusBar style="dark" />
        </ThemeProvider>
    );
}