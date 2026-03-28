import { COLORS } from '@/constants/colors';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { Tabs } from 'expo-router';
import React from 'react';
import { StyleSheet, Text, View } from 'react-native';

const TabIcon = ({ focused, iconName, label }: { focused: boolean, iconName: any, label: string }) => {
    return (
        <View style={[styles.iconContainer, focused && styles.iconContainerFocused]}>
            <MaterialCommunityIcons
                name={iconName}
                size={26}
                color="#000000"
            />
            <Text style={[styles.iconLabel, focused && styles.iconLabelFocused]}>
                {label}
            </Text>
        </View>
    );
};

export default function HomeLayout() {
    return (
        <Tabs
            screenOptions={{
                headerShown: false,
                tabBarShowLabel: false,
                tabBarStyle: {
                    backgroundColor: "#D8D8D8",
                    borderRadius: 50,
                    marginHorizontal: 12,
                    marginBottom: 12,
                    height: 85,
                    paddingTop: 20,
                    position: "absolute",
                    overflow: "hidden",
                    borderWidth: 0,
                },
                tabBarItemStyle: {
                    height: 85,
                    justifyContent: 'center',
                    alignItems: 'center',
                    paddingTop: 0,
                    paddingBottom: 0,
                },
            }}
        >
            <Tabs.Screen
                name="index"
                options={{
                    title: 'Accueil',
                    headerShown: false,
                    tabBarIcon: ({ focused }) => (
                        <TabIcon focused={focused} iconName="home" label="Accueil" />
                    ),
                }}
            />
            <Tabs.Screen
                name="discovery"
                options={{
                    title: 'Découvrir',
                    tabBarIcon: ({ focused }) => (
                        <TabIcon focused={focused} iconName="dumbbell" label="Découvrir" />
                    ),
                }}
            />
            <Tabs.Screen
                name="report"
                options={{
                    title: 'Rapport',
                    tabBarIcon: ({ focused }) => (
                        <TabIcon focused={focused} iconName="chart-bar" label="Rapport" />
                    ),
                }}
            />
            <Tabs.Screen
                name="settings"
                options={{
                    title: 'Paramètres',
                    tabBarIcon: ({ focused }) => (
                        <TabIcon focused={focused} iconName="account" label="Paramètres" />
                    ),
                }}
            />
        </Tabs>
    );
}

const styles = StyleSheet.create({
    tabBar: {
        position: 'absolute',
        bottom: 25,
        left: 20,
        right: 20,
        marginHorizontal: 10,
        backgroundColor: '#D8D8D8',
        borderRadius: 50,
        height: 85,
        borderTopWidth: 0,
        elevation: 0,
        shadowOpacity: 0,
        paddingHorizontal: 20,
        paddingBottom: 0,

        // 2. Aligner les items sur l'axe horizontal et vertical
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
    },
    iconContainer: {
        alignItems: 'center',
        justifyContent: 'center',
        height: 68,
        width: 68,
        borderRadius: 34,
        backgroundColor: 'transparent',
    },
    iconContainerFocused: {
        backgroundColor: COLORS.primary,
    },
    iconLabel: {
        fontSize: 10,
        color: '#000000',
        marginTop: 2,
        fontWeight: '500',
    },
    iconLabelFocused: {
        fontWeight: '800',
    },
});
