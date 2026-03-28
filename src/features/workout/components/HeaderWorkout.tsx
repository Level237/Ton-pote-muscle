import { COLORS } from '@/constants/colors';
import { images } from '@/constants/images';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import React, { useState } from 'react';
import { Image, StyleSheet, Text, TextInput, TouchableOpacity, View } from 'react-native';
import Animated, { FadeIn, FadeOut, Layout } from 'react-native-reanimated';

const HeaderWorkout = () => {
    const [isSearchVisible, setIsSearchVisible] = useState(false);

    return (
        <View style={styles.container}>
            {/* Top Row */}
            <View style={styles.topRow}>
                {/* Logo & Name */}
                <View style={styles.logoContainer}>
                    <Image source={images.logo} style={styles.logo} resizeMode="contain" />
                    <Text style={styles.appName}>Ton Pote Musclé</Text>
                </View>

                {/* Right Actions */}
                <View style={styles.actionsContainer}>
                    <TouchableOpacity
                        onPress={() => setIsSearchVisible(!isSearchVisible)}
                        style={styles.iconButton}
                        activeOpacity={0.7}
                    >
                        <MaterialCommunityIcons name="magnify" size={24} color="#FFFFFF" />
                    </TouchableOpacity>

                    <Text style={styles.greetingText}>Hello Level</Text>
                    {/* Placeholder for Flag/Avatar */}
                    <View style={styles.flagPlaceholder}>
                        <Text style={{ fontSize: 14 }}>🇫🇷</Text>
                    </View>
                </View>
            </View>

            {/* Separator Line */}
            <View style={styles.separator} />

            {/* Dynamic Search Bar */}
            {isSearchVisible && (
                <Animated.View
                    entering={FadeIn.duration(200)}
                    exiting={FadeOut.duration(200)}
                    layout={Layout.duration(200)}
                    style={styles.searchContainer}
                >
                    <MaterialCommunityIcons name="magnify" size={20} color="#888888" style={styles.searchIconInside} />
                    <TextInput
                        style={styles.searchInput}
                        placeholder="Rechercher les entraînements, programmes"
                        placeholderTextColor="#888888"
                        autoFocus
                    />
                </Animated.View>
            )}
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        width: '100%',
        paddingHorizontal: 20,
        paddingTop: 10,
        backgroundColor: '#000000',
    },
    topRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 15,
    },
    logoContainer: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    logo: {
        width: 30,
        height: 30,
        marginRight: 1,
    },
    appName: {
        color: '#FFFFFF',
        fontSize: 13,
        fontWeight: 'bold',
    },
    actionsContainer: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    iconButton: {
        marginRight: 15,
        padding: 5,
    },
    greetingText: {
        color: '#FFFFFF',
        fontSize: 14,
        marginRight: 8,
    },
    flagPlaceholder: {
        width: 24,
        height: 24,
        borderRadius: 12,
        backgroundColor: '#333333',
        alignItems: 'center',
        justifyContent: 'center',
    },
    separator: {
        height: 1,
        backgroundColor: COLORS.primary, // Yellow thin line
        width: '100%',
        marginBottom: 20,
    },
    searchContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#E0E0E0',
        borderRadius: 25,
        paddingHorizontal: 15,
        height: 45,
        marginBottom: 25,
    },
    searchIconInside: {
        marginRight: 10,
    },
    searchInput: {
        flex: 1,
        fontSize: 14,
        color: '#000000',
    },
});

export default HeaderWorkout;
