import { COLORS } from '@/constants/colors';
import { images } from '@/constants/images';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { router } from 'expo-router';
import React from 'react';
import { Image, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

const ChallengeDetailStaticHeader = () => {
    const insets = useSafeAreaInsets();

    return (
        <View style={[styles.container, { paddingTop: insets.top }]}>
            <View style={styles.content}>
                {/* Back Button */}
                <TouchableOpacity
                    style={styles.backButton}
                    onPress={() => router.back()}
                    activeOpacity={0.8}
                >
                    <MaterialCommunityIcons name="arrow-left" size={24} color="#000000" />
                </TouchableOpacity>

                {/* Brand Identity */}
                <View style={styles.brandContainer}>
                    <Image source={images.logo} style={styles.logo} resizeMode="contain" />
                    <Text style={styles.brandName}>Ton Pote Musclé</Text>
                </View>

                {/* Flag Badge */}
                <View style={styles.flagBadge}>
                    <Text style={{ fontSize: 14 }}>🇫🇷</Text>
                </View>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        width: '100%',
        backgroundColor: '#000000',
        zIndex: 100,
    }, logo: {
        width: 30,
        height: 30,
        marginRight: 1,
    },
    content: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingHorizontal: 20,
        paddingVertical: 12,
    },
    backButton: {
        width: 36,
        height: 36,
        borderRadius: 18,
        backgroundColor: COLORS.primary,
        justifyContent: 'center',
        alignItems: 'center',
    },
    brandContainer: {
        flexDirection: 'row',
        alignItems: 'center',
    },
    brandName: {
        color: '#FFFFFF',
        fontSize: 14,
        fontWeight: '700',
        marginLeft: 6,
    },
    flagBadge: {
        width: 24,
        height: 24,
        borderRadius: 12,
        backgroundColor: '#333333',
        alignItems: 'center',
        justifyContent: 'center',
    },
});

export default ChallengeDetailStaticHeader;
